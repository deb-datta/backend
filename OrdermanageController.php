<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Marchantlist;
use App\Ordercollection;
use App\Loantracking;
use App\Manualotpsmssent;
use App\Dailycollection;
use App\Repaymentcollection;
use DateTime;
use DB;
use Validator;
use File;
use Image;
//use App\Http\Controllers\Image;

class OrdermanageController extends Controller
{   

    
   
    /*..........SENT ME SMS...........*/
    public function newsmssento($mobileno,$sms){
        // Send the POST request with cURL
            $ch = curl_init('https://api.txtlocal.com/send/');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);
            
            // Process your response here
            echo $response;
    }
    /*................FOR DEMO APP SOJSO ORDER COLLECTION................*/
     public function myCollectiondtls(Request $request)
    {
       

        $currentdtaetime=date("Y-m-d H:i:s", strtotime("+30 minutes"));
         
        $noofinitorder = Ordercollection::select(DB::raw('count(*) as initialorder'))->whereDate('updated_at',date("Y-m-d"))->where('sossoid',$request->user()->id)
            ->where('okupdate','0')->where('totalamount','!=','0')->get();
            if(count($noofinitorder) > 0){
                $totalinit=$noofinitorder->first();
                $neworders=$totalinit['initialorder'];
            }else{
                $neworders=0;
            }

            $totalrepay=Repaymentcollection::select(DB::raw('sum(collectedamount) as totalrepay'))->whereDate('created_at',date("Y-m-d"))->where('updated_by',$request->user()->id)->get()->first();
            $dailycollect=Dailycollection::select(DB::raw('sum(collectedamount) as totalpay'))->whereDate('created_at',date("Y-m-d"))->where('collectedby',$request->user()->id)->get()->first();

         $noofdeliveredorder = Ordercollection::select(DB::raw('count(*) as deliveredorder,SUM(dueamount) as creditamount'))->whereDate('finalorderdeliverydt',date("Y-m-d"))->where('sossoid',$request->user()->id)
            ->where('okupdate','1')->where('totalamount','!=','0')->get();
            if(count($noofdeliveredorder) > 0){
                $totalcolects=$noofdeliveredorder->first();
                $deliveredorder=$totalcolects['deliveredorder'];
                if($totalcolects['collected']){
                   $collected=$totalcolects['collected']; 
               }else{
                    $collected=0;
               }
                if($totalcolects['creditamount'] && $totalcolects['creditamount']!=null){
                   $creditamount=$totalcolects['creditamount']; 
               }else{
                    $creditamount=0;
               }
            }else{
                $deliveredorder=0;
                $collected=0;
                $creditamount=0;
            }
            if($totalrepay['totalrepay']!=null){
                $totalrepayamnt=$totalrepay['totalrepay'];
            }else{
                $totalrepayamnt=0;
            }

            if($dailycollect['totalpay']!=null){
                $dailycolect=$dailycollect['totalpay'];
            }else{
                $dailycolect=0;
            }

        if(true){  //count($noofdeliveredorder) > 0
           
                                
                        return response()->json([
                            'neworders' => $neworders,
                            'deliveredorder' => $deliveredorder,'collected' =>  $dailycolect,'creditamount' => $creditamount,'repayment'=>$totalrepayamnt,
                        ], 200);


        }else{
            return response()->json([
                'response' => 'error',
                'message' => 'Problem in data'
            ], 400); 
        }
        
        
    }  
    
    /*.......FairBac UBL API.......*/
     //*-----------------------UBL API ORDER INITIATION (SO/SSO)----------------------*/
    public function sossoOrderadd(Request $request)
    {
        $this->validate($request, [
            'invoiceid' => 'required',
            'outletcode' => 'required',
            'outletmobileno'=> 'required',
            'totalamount'=> 'required',
        ]);
        
        $sossoid=$request->user()->id;
        $distributerid=$request->user()->distributerid;
        if($sossoid && $distributerid){
            $ubloutletcode="UBL_".$request->outletcode;
            $invoicecodenow='UBL_'.$request->invoiceid;
            $currentorderdt=date('Y-m-d');
            $marchantinfo= Marchantlist::where('mobileno',$request->outletmobileno)->where('outletcode',$ubloutletcode)->get(['id','outletcode','mobileno','fbcreditlinebm' ,'currentavailable' ,'lastfbapicheck' ]);
            $checkorderhave=Ordercollection::where('company','ubl')->where('orderdate',$currentorderdt)->where('outletcode',$request->outletcode)->where('outletmobileno',$request->outletmobileno)->where('invoiceid',$invoicecodenow)->where('totalamount',$request->totalamount)->get(['id']);
            if((count($marchantinfo) > 0) && (count($checkorderhave) <= 0)){
                $outletdatnow=$marchantinfo->first();
                if($outletdatnow->currentavailable >=$request->dueamount){

               
                    $fullfblimit=$outletdatnow->fbcreditlinebm;
                    $currentavailable=floatval($outletdatnow->currentavailable);
                    if($request->dueamount){
                        $fullorderamount=floatval($request->dueamount);
                    }else{
                       $fullorderamount=floatval($request->totalamount); 
                    }
                    
                    if($fullorderamount>=$currentavailable){
                        $newcurrentavailable=0;
                    }else{
                        $newcurrentavailable=($currentavailable-$fullorderamount);  
                    }


                    $mainorderinfo = new Ordercollection;
                    $mainorderinfo->orderdate=date('Y-m-d'); 
                    $mainorderinfo->company='ubl'; 
                    $mainorderinfo->outletid=$outletdatnow->id; 
                    $mainorderinfo->outletcode = $ubloutletcode;  
                    $mainorderinfo->invoiceid= $invoicecodenow;
                    $mainorderinfo->outletmobileno = $request->outletmobileno;
                    $mainorderinfo->sossoid = $sossoid;
                    $mainorderinfo->distributorid = $distributerid;
                    $mainorderinfo->totalamount = $request->totalamount;
                    $mainorderinfo->paidamount = $request->paidamount;
                    $mainorderinfo->dueamount = $request->dueamount;


                    $mainorderinfo->beforefairbacamount = $currentavailable; 
                    $mainorderinfo->afterfairbacamount = $newcurrentavailable;    
                    if($request->detailorder){
                       $mainorderinfo->detailorder = $request->detailorder; 
                    }
                    
                    $mainorderinfo->currentstate=0;

                    
                    $mainorderinfo->updated_by = $request->user()->id;
                    $mainorderinfo->created_at=date('Y-m-d H:i:s'); 
                   $datastpone= $mainorderinfo->save();


                    if($datastpone){
                        $crentdttm=date('Y-m-d H:i:s'); 
                         $marchantinfoupdate= Marchantlist::where('mobileno',$request->outletmobileno)->where('outletcode',$ubloutletcode)
                         ->where('id',$outletdatnow->id)
                         ->update(['currentavailable'=>$newcurrentavailable,'lastfbapicheck'=>$crentdttm,'lastfbapicheck_by'=>$sossoid ]);   


                         if($marchantinfoupdate){
                            $newsms="Dear+Merchant,for+your+new+UBL+order+reff.+invoice+".$request->invoiceid."+your+FairBanc+credit+Available=".$newcurrentavailable."Tk";
                            $newurls='http://alphasms.biz/index.php?app=ws&u=indexer&h=351794a3122fab8ff8bbc78b8092797b&op=pv&to='.$outletdatnow->mobileno.'&msg='.$newsms;

                           
                            // Get cURL resource
                            $curl = curl_init();
                            // Set some options - we are passing in a useragent too here
                            curl_setopt_array($curl, array(
                                CURLOPT_RETURNTRANSFER => 1,
                                CURLOPT_URL => $newurls,
                                CURLOPT_USERAGENT => 'SMS mobile Request'
                            ));
                            // Send the request & save response to $resp
                            $resp = curl_exec($curl);
                            // Close request to clear up some resources
                            curl_close($curl);


                            
                            return response()->json([
                                'message' => 'Order listing successfull'
                            ], 200);
                         }
                    }
            

                }else{
                    return response()->json([
                        'response' => 'error',
                        'message' => 'Outlet do not have sufficient fairbanc balance'
                    ], 400);
                } 


            }else{
                return response()->json([
                    'response' => 'error',
                    'message' => 'Outlet not enlisted at fairbanc'
                ], 400);
            } 
                
                
        } else{
            return response()->json([
                'response' => 'error',
                'message' => 'Not Eligible to order'
            ], 400);
        }
        return response()->json([
            'response' => 'error',
            'message' => 'Problem in data'
        ], 400); 
        
    }  
    
    /*.......FairBac UBL API.......*/
    //*-----------------------UBL API ORDER FINAL (JSO)----------------------*/
    public function jsoOrderedit(Request $request,$invoice)
    {
        $this->validate($request, [
            'invoiceid' => 'required',
            'outletcode' => 'required',
            'outletmobileno'=> 'required',
            //'detailorder'=> 'required',
            'totalamount'=> 'required',
        ]);
        
        $sossoid=$request->user()->id;
        $distributerid=$request->user()->distributerid;
        
        if($sossoid && $distributerid){
            $ubloutletcode="UBL_".$request->outletcode;
            $invoicecodenow='UBL_'.$request->invoiceid;
            $marchantinfo= Marchantlist::where('mobileno',$request->outletmobileno)->where('outletcode',$ubloutletcode)->get(['id','outletcode','mobileno','fbcreditlinebm' ,'currentavailable' ,'lastfbapicheck' ]);
            if(count($marchantinfo) > 0){
                $outletdatnow=$marchantinfo->first();
                $mainorderinfo = Ordercollection::where('company','ubl')->where('outletcode',$ubloutletcode)->where('invoiceid',$invoicecodenow)->where('okupdate','<','1')->get();
                if(count($mainorderinfo) > 0){
                  $mainorderinfo=$mainorderinfo[0];
                    $currentstate=($mainorderinfo->currentstate)+1;
                     $last_dueamount = floatval($mainorderinfo->dueamount);
                     $last_currentavailable=floatval($outletdatnow->currentavailable);
                     $oparative_currentavailable=$last_currentavailable+$last_dueamount;


                    $fullfblimit=$outletdatnow->fbcreditlinebm;
                    $currentavailable=$oparative_currentavailable;
                    if($request->dueamount){
                        $fullorderamount=floatval($request->dueamount);
                    }else{
                       $fullorderamount=floatval($request->totalamount); 
                    }
                    
                    if($fullorderamount>=$currentavailable){
                        $newcurrentavailable=0;
                    }else{
                        $newcurrentavailable=($currentavailable-$fullorderamount);  
                    }

                    $verifiedcode=rand(100000,999999);
                    $codeexpiredat=date("Y-m-d H:i:s", strtotime("+2 days"));

                    $updateorders=Ordercollection::where('company','ubl')->where('outletcode',$ubloutletcode)->where('invoiceid',$invoicecodenow)->
                    update(['invoiceid'=>$invoicecodenow,'jsoid'=>$request->user()->id,'totalamount'=>$request->totalamount,'paidamount'=>$request->paidamount,
                    'dueamount'=>$request->dueamount,'beforefairbacamount'=>$currentavailable,'afterfairbacamount'=>$newcurrentavailable,'currentstate'=>$currentstate,'updated_by'=>$request->user()->id,'verifiedmobilecode'=>$verifiedcode,'codeexpiredat'=>$codeexpiredat]);


                    if($updateorders){
                        $crentdttm=date('Y-m-d H:i:s'); 
                         $marchantinfoupdate= Marchantlist::where('mobileno',$request->outletmobileno)->where('outletcode',$ubloutletcode)
                         ->where('id',$outletdatnow->id)
                         ->update(['currentavailable'=>$newcurrentavailable,'lastfbapicheck'=>$crentdttm,'lastfbapicheck_by'=>$sossoid ]);   


                         if($marchantinfoupdate){
                            $newsms="Dear+Merchant,for+your+UBL+order+reff.+invoice+".$request->invoiceid."+your+loan+amount=".$request->dueamount."FairBanc+credit+Available=".$newcurrentavailable."Tk+and+Confirmation+OTP=".$verifiedcode;
                            $newurls='http://alphasms.biz/index.php?app=ws&u=indexer&h=351794a3122fab8ff8bbc78b8092797b&op=pv&to='.$request->outletmobileno.'&msg='.$newsms;

                          
                            $curl = curl_init();
                            // Set some options - we are passing in a useragent too here
                            curl_setopt_array($curl, array(
                                CURLOPT_RETURNTRANSFER => 1,
                                CURLOPT_URL => $newurls,
                                CURLOPT_USERAGENT => 'SMS mobile Request'
                            ));
                            // Send the request & save response to $resp
                            $resp = curl_exec($curl);
                            // Close request to clear up some resources
                            curl_close($curl);

                            /****JSO SMS*****/
                             $newsmsJso="Your+are+delivering+invoice+".$request->invoiceid."+Merchant+loan+amount=".$request->dueamount."FairBanc+credit+Available=".$newcurrentavailable."Tk";
                            $newurlsJSO='http://alphasms.biz/index.php?app=ws&u=indexer&h=351794a3122fab8ff8bbc78b8092797b&op=pv&to='.$request->user()->mobileno.'&msg='.$newsmsJso;

                          
                            $curl2 = curl_init();
                            // Set some options - we are passing in a useragent too here
                            curl_setopt_array($curl2, array(
                                CURLOPT_RETURNTRANSFER => 1,
                                CURLOPT_URL => $newurlsJSO,
                                CURLOPT_USERAGENT => 'SMS mobile Request'
                            ));
                            // Send the request & save response to $resp
                            $resp2 = curl_exec($curl2);
                            // Close request to clear up some resources
                            curl_close($curl2);
                            
                            return response()->json([
                                'message' => 'Order OTP generation complete'
                            ], 200);
                         }
                    }
            
                }else{
                    return response()->json([
                        'response' => 'error',
                        'message' => 'No relavent invoice found at fairbanc'
                    ], 400);
                }
                


            }else{
                return response()->json([
                    'response' => 'error',
                    'message' => 'Outlet not enlisted at fairbanc'
                ], 400);
            } 
                
                
        } else{
            return response()->json([
                'response' => 'error',
                'message' => 'Not Eligible to order'
            ], 400);
        }
        return response()->json([
            'response' => 'error',
            'message' => 'Problem in data'
        ], 400); 
        
    }  
     //*-----------------------UBL API ORDER OTP FINAL ALL FINAL (JSO)----------------------*/
    public function jsoOrderotp(Request $request,$invoice)
    {
        $this->validate($request, [
            'invoiceid' => 'required',
            'outletcode' => 'required',
            'outletmobileno'=> 'required',
           // 'detailorder'=> 'required',
            'totalamount'=> 'required',
            'otp'=> 'required',
        ]);
        
        $sossoid=$request->user()->id;
        $distributerid=$request->user()->distributerid;
        $verifiedmobilecode=$request->otp;
        $currentdtaetime=date("Y-m-d H:i:s");
        if($sossoid && $distributerid && $verifiedmobilecode){
            $ubloutletcode="UBL_".$request->outletcode;
            $invoicecodenow='UBL_'.$request->invoiceid;
            $marchantinfo= Marchantlist::where('mobileno',$request->outletmobileno)->where('outletcode',$ubloutletcode)->get(['id','outletcode','mobileno','fbcreditlinebm' ,'currentavailable' ,'lastfbapicheck' ]);
            if(count($marchantinfo) > 0){
                $outletdatnow=$marchantinfo->first();
                $mainorderinfo = Ordercollection::where('company','ubl')->where('outletcode',$ubloutletcode)->where('invoiceid',$invoicecodenow)->where('verifiedmobilecode',$verifiedmobilecode)->where('codeexpiredat','>=',$currentdtaetime)->where('okupdate','<','1')->get();
                if(count($mainorderinfo) > 0){
                  $mainorderinfo=$mainorderinfo[0];
                    $currentstate=($mainorderinfo->currentstate)+1;
                     $last_dueamount = floatval($mainorderinfo->dueamount);
                     $last_currentavailable=floatval($outletdatnow->currentavailable);
                     $oparative_currentavailable=$last_currentavailable+$last_dueamount;


                    $fullfblimit=$outletdatnow->fbcreditlinebm;
                    $currentavailable=$oparative_currentavailable;
                    if($request->dueamount){
                        $fullorderamount=floatval($request->dueamount);
                    }else{
                       $fullorderamount=floatval($request->totalamount); 
                    }
                    
                    if($fullorderamount>=$currentavailable){
                        $newcurrentavailable=0;
                    }else{
                        $newcurrentavailable=($currentavailable-$fullorderamount);  
                    }

                    //$verifiedcode=rand(100000,999999);
                    //$codeexpiredat=date("Y-m-d H:i:s", strtotime("+30 minutes"));

                    if($request->dueamount>0){
                        $updateorders=Ordercollection::where('company','ubl')->where('outletcode',$ubloutletcode)->where('invoiceid',$invoicecodenow)->
                    update(['invoiceid'=>$invoicecodenow,'jsoid'=>$request->user()->id,'totalamount'=>$request->totalamount,'paidamount'=>$request->paidamount,
                    'dueamount'=>$request->dueamount,'beforefairbacamount'=>$currentavailable,'afterfairbacamount'=>$newcurrentavailable,'currentstate'=>$currentstate,'updated_by'=>$request->user()->id,'verifiedmobilecode'=>'','otpferifiedat'=>$currentdtaetime,'finalorderdeliverydt'=>$currentdtaetime,'okupdate'=>'1']);
                                
                    }else{
                        $updateorders=Ordercollection::where('company','ubl')->where('outletcode',$ubloutletcode)->where('invoiceid',$invoicecodenow)->
                    update(['invoiceid'=>$invoicecodenow,'jsoid'=>$request->user()->id,'totalamount'=>$request->totalamount,'paidamount'=>$request->paidamount,
                    'dueamount'=>$request->dueamount,'beforefairbacamount'=>$currentavailable,'afterfairbacamount'=>$newcurrentavailable,'currentstate'=>$currentstate,'updated_by'=>$request->user()->id,'verifiedmobilecode'=>'','otpferifiedat'=>$currentdtaetime,'finalorderdeliverydt'=>$currentdtaetime,'okupdate'=>'1','activestate'=>'0']);
                    }

                            $dailycolectiondt = new Dailycollection;
                            
                            $dailycolectiondt->collectedby=$request->user()->id;
                            $dailycolectiondt->distributerid= $distributerid;

                            $dailycolectiondt->orderid=$mainorderinfo->id; 
                            $dailycolectiondt->outletcode = $ubloutletcode; 
                            $dailycolectiondt->invoiceid=$invoicecodenow;
                            $dailycolectiondt->totalamount = $request->totalamount;
                            $dailycolectiondt->collectedamount = $request->paidamount;


                            $dailycolectiondt->updated_by = $request->user()->id;
                            //$dailycolection->created_at=date('Y-m-d H:i:s'); 
                            $dailycolectiondt->save();

                            
                            
                   


                    if($updateorders){
                        $crentdttm=date('Y-m-d H:i:s'); 
                         $marchantinfoupdate= Marchantlist::where('mobileno',$request->outletmobileno)->where('outletcode',$ubloutletcode)
                         ->where('id',$outletdatnow->id)
                         ->update(['currentavailable'=>$newcurrentavailable,'lastfbapicheck'=>$crentdttm,'lastfbapicheck_by'=>$sossoid ]);   

                         if($marchantinfoupdate && ($request->dueamount>0)){
                            //FOR DATA STORING TO LOAN FINALIGATION


                            $loantrackinginfo = new Loantracking;
                            $loantrackinginfo->companyname='ubl'; 

                            $loantrackinginfo->orderid=$mainorderinfo->id; 
                            $loantrackinginfo->outletid=$outletdatnow->id; 
                            $loantrackinginfo->outletcode = $ubloutletcode;  
                            $loantrackinginfo->invoiceid= $invoicecodenow;
                            $loantrackinginfo->distributorid= $distributerid;
                            $loantrackinginfo->totalbill = $request->totalamount;
                            $loantrackinginfo->loanamount = $request->dueamount;
                            $loantrackinginfo->startingdate = date('Y-m-d'); 
                            $loantrackinginfo->interestrate = 2.00;
                            $loantrackinginfo->adjustedamount = 0.00;
                            $loantrackinginfo->jsoid = $request->user()->id;


                            $loantrackinginfo->lastbariardate = date("Y-m-d H:i:s", strtotime("+7 days")); 
                          //  $loantrackinginfo->afterfairbacamount = $newcurrentavailable;    

                            $loantrackinginfo->closestate=0;

                            
                            $loantrackinginfo->updated_by = $request->user()->id;
                            $loantrackinginfo->created_at=date('Y-m-d H:i:s'); 
                            $datastpone= $loantrackinginfo->save();

                            if($datastpone){




                                $newsms="Dear+Merchant,loan+amount=".$request->dueamount."Tk+is+approved+for+UBL+order+reff.+invoice+".$request->invoiceid."+Your+FairBanc+Available+Credit=".$newcurrentavailable."Tk+Thanks+for+approval";
                                $newurls='http://alphasms.biz/index.php?app=ws&u=indexer&h=351794a3122fab8ff8bbc78b8092797b&op=pv&to='.$request->outletmobileno.'&msg='.$newsms;

                              
                                $curl = curl_init();
                                // Set some options - we are passing in a useragent too here
                                curl_setopt_array($curl, array(
                                    CURLOPT_RETURNTRANSFER => 1,
                                    CURLOPT_URL => $newurls,
                                    CURLOPT_USERAGENT => 'SMS mobile Request'
                                ));
                                // Send the request & save response to $resp
                                $resp = curl_exec($curl);
                                // Close request to clear up some resources
                                curl_close($curl);


                                
                                return response()->json([
                                    'message' => 'Order OTP varification complete'
                                ], 200);
                            }


                            
                         }else if($marchantinfoupdate && ($request->dueamount<=0)){
                            $newsms="Dear+Merchant,loan+amount=".$request->dueamount."Tk+is+approved+for+UBL+order+reff.+invoice+".$request->invoiceid."+Your+FairBanc+Available+Credit=".$newcurrentavailable."Tk+Thanks+for+approval";
                                $newurls='http://alphasms.biz/index.php?app=ws&u=indexer&h=351794a3122fab8ff8bbc78b8092797b&op=pv&to='.$request->outletmobileno.'&msg='.$newsms;

                              
                                $curl = curl_init();
                                // Set some options - we are passing in a useragent too here
                                curl_setopt_array($curl, array(
                                    CURLOPT_RETURNTRANSFER => 1,
                                    CURLOPT_URL => $newurls,
                                    CURLOPT_USERAGENT => 'SMS mobile Request'
                                ));
                                // Send the request & save response to $resp
                                $resp = curl_exec($curl);
                                // Close request to clear up some resources
                                curl_close($curl);


                                
                                return response()->json([
                                    'message' => 'Order OTP varification complete'
                                ], 200);
                         }else{
                            return response()->json([
                                'response' => 'error',
                                'message' => 'OTP fail to match at fairbanc'
                            ], 400);
                         }
                    }
            
                }else{
                    return response()->json([
                        'response' => 'error',
                        'message' => 'OTP fail to match at fairbanc'
                    ], 400);
                }
                


            }else{
                return response()->json([
                    'response' => 'error',
                    'message' => 'Outlet not enlisted at fairbanc'
                ], 400);
            } 
                
                
        } else{
            return response()->json([
                'response' => 'error',
                'message' => 'Not Eligible to order'
            ], 400);
        }
        return response()->json([
            'response' => 'error',
            'message' => 'Problem in data'
        ], 400); 
        
    } 
    




/*.......................................FOR SERVER SIDE OTP SENT ........................................*/
//*-----------------------UBL API ORDER FINAL OTP MANUAL SEND----------------------*/
    public function manualSmsotpsend(Request $request)
    {
        $this->validate($request, [
            'orderid' => 'required',
        ]);

        $currentdtaetime=date("Y-m-d H:i:s", strtotime("+30 minutes"));
        $mainorderinfo = Ordercollection::where('id',$request->orderid)->where('okupdate','0')->where('totalamount','!=','0')->where('verifiedmobilecode','!=','')->get();
        if(count($mainorderinfo) > 0){
            $mainorderinfo=$mainorderinfo->first();
            $updateorders=Ordercollection::where('id',$request->orderid)->where('okupdate','0')->where('totalamount','!=','0')->where('verifiedmobilecode','!=','')->
                    update(['otpferifiedat'=>$currentdtaetime]);
                   


            

            $outletidd=$mainorderinfo->outletid;
            $marchantidinfo = Marchantlist::where('id',$outletidd)->get(['currentavailable'])->first();
            $newcurrentavailable=$marchantidinfo->currentavailable;
                        $newsms="Dear+Merchant,for+your+UBL+order+reff.+invoice+".$mainorderinfo->invoiceid."+your+loan+amount=".$mainorderinfo->dueamount."FairBanc+credit+Available=".$newcurrentavailable."Tk+and+Confirmation+OTP=".$mainorderinfo->verifiedmobilecode;
                        
                        $newurls='http://alphasms.biz/index.php?app=ws&u=indexer&h=351794a3122fab8ff8bbc78b8092797b&op=pv&to='.$mainorderinfo->outletmobileno.'&msg='.$newsms;

                              
                        $curl = curl_init();
                         // Set some options - we are passing in a useragent too here
                        curl_setopt_array($curl, array(
                        CURLOPT_RETURNTRANSFER => 1,
                        CURLOPT_URL => $newurls,
                        CURLOPT_USERAGENT => 'SMS mobile Request'
                        ));
                        // Send the request & save response to $resp
                        $resp = curl_exec($curl);
                        // Close request to clear up some resources
                        curl_close($curl);

                        if($resp){
                            $newsavedta= new Manualotpsmssent;
                            $newsavedta->sentbyid=$request->user()->id;
                            $newsavedta->sentfororderid=$request->orderid;
                            $newsavedta->invoiceidfor=$mainorderinfo->invoiceid;
                            $newsavedta->updated_by=$request->user()->id;
                            $newsavedta->save();
                        }
                                
                        return response()->json([
                            'message' => 'SMS sent complete'
                        ], 200);


        }
        return response()->json([
            'response' => 'error',
            'message' => 'Problem in data'
        ], 400); 
        
    }  















    //MYAPP TOTAL CASH COLLECTION TODAY

     public function mycollectedCash(Request $request)
    {
       

        $currentdtaetime=date("Y-m-d H:i:s", strtotime("+30 minutes"));
         
        $noofinitorder = Ordercollection::select()->whereDate('finalorderdeliverydt',date("Y-m-d"))->where('sossoid',$request->user()->id)
            ->where('okupdate','1')->where('paidamount','!=','0')->get();
            if(count($noofinitorder) > 0){
                return response()->json($noofinitorder, 200);
            }else{
                return response()->json([
                    'response' => 'error',
                    'message' => 'Problem in data'
                ], 400); 
            }
        
        
    }  
    
 
}
