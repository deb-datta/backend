<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Ordercollection;
use App\Loantracking;
use App\Repaymentcollection;
use App\Marchantlist;
use DateTime;
use DB;
use Validator;
use File;
use Image;
//use App\Http\Controllers\Image;

class UblorderController extends Controller
{   
   

    public function getAllorders(Request $request)
    {
       
            $orderdtls=Ordercollection::select()->where('okupdate','!=','50')->where('activestate','1')->orderBy('id', 'desc')->paginate(15);
            //$customers=Company::get(['id','name']);
            //$brandsall=Brand::get(['id','name']);
            if(count($orderdtls) > 0){
                return response()->json([
                'allorder' => $orderdtls,
            ], 200);
            }
        
        return response()->json([
            'response' => 'error',
            'message' => 'No Record Found'
        ], 400);
    }
    public function getOutletwise(Request $request,$idoutlet)
    {
       
            $orderdtls=Ordercollection::select()->where('okupdate','!=','50')->where('activestate','1')->where('outletid',$idoutlet)->orderBy('id', 'desc')->paginate(15);
            //$customers=Company::get(['id','name']);activestate
            //$brandsall=Brand::get(['id','name']);
            if(count($orderdtls) > 0){
                return response()->json([
                'allorder' => $orderdtls,
            ], 200);
            }
        
        return response()->json([
            'response' => 'error',
            'message' => 'No Record Found'
        ], 400);
    }
    public function getOutletinvoice(Request $request)
    {
       $this->validate($request, [
            'orderid'=> 'required',
        ]);
            $orderdtls=Ordercollection::select()->where('okupdate','!=','50')->where('activestate','1')->where('id',$request->orderid)->get();
            //$customers=Company::get(['id','name']);
            //$brandsall=Brand::get(['id','name']);
            if(count($orderdtls) > 0){
                return response()->json([
                    'invoice' => $orderdtls->first(),
                ], 200);
            }
        
        return response()->json([
            'response' => 'error',
            'message' => 'No Record Found'
        ], 400);
    }

    public function getOutletpaymentpendinginv(Request $request)
    {
       $this->validate($request, [
            'outletid'=> 'required',
        ]);
            $orderdtls=Loantracking::where('closestate','!=','3')->where('banksink','3')->where('outletid',$request->outletid)->get();
            //$customers=Company::get(['id','name']);
            //$brandsall=Brand::get(['id','name']);
            if(count($orderdtls) > 0){
                return response()->json([
                    'repayorder' => $orderdtls,
                ], 200);
            }else{
                return response()->json([
                    'response' => 'error',
                    'message' => 'No Record Found'
                ], 400);
            }
        
        
    }
    public function getOutletpaymentspecific(Request $request)
    {
       $this->validate($request, [
            'outletid'=> 'required',
            'id'=> 'required',
        ]);
            $orderdtls=Loantracking::where('closestate','!=','3')->where('banksink','3')->where('outletid',$request->outletid)->where('id',$request->id)->get();
            //$customers=Company::get(['id','name']);
            //$brandsall=Brand::get(['id','name']);
            if(count($orderdtls) > 0){
                return response()->json([
                    'repayorderinv' => $orderdtls->first(),
                ], 200);
            }else{
                return response()->json([
                    'response' => 'error',
                    'message' => 'No Record Found'
                ], 400);
            }
        
        
    }






    /*.......FairBac UBL API.......*/
    //*-----------------------UBL API REPAYMENT ORDER FINAL (JSO)----------------------*/
    public function repaymentpayOrderedit(Request $request,$invoice)
    {
        $this->validate($request, [
            'invoiceid' => 'required',
            'outletcode' => 'required',
            'outletmobileno'=> 'required',
            'totalloan'=> 'required',
            'paidamount'=> 'required',
            'moneyreceipt'=> 'required',
        ]);
        
        $sossoid=$request->user()->id;
        $distributerid=$request->user()->distributerid;
        $paidamount=$request->paidamount;
        $moneyreceipt=$request->moneyreceipt;
        $currentdtaetime=date("Y-m-d H:i:s");
        if($sossoid && $distributerid && $paidamount){
            $ubloutletcode="UBL_".$request->outletcode;
            $invoicecodenow='UBL_'.$request->invoiceid;
            $chekmoneyreceipt= Repaymentcollection::where('invoiceid',$invoicecodenow)->where('moneyreceptno',$moneyreceipt)->get(['id']);
            if(count($chekmoneyreceipt) <= 0){
                $marchantinfo= Marchantlist::where('mobileno',$request->outletmobileno)->where('outletcode',$ubloutletcode)->get(['id','outletcode','mobileno','fbcreditlinebm' ,'currentavailable' ,'lastfbapicheck' ]);
                if(count($marchantinfo) > 0){
                    $outletdatnow=$marchantinfo->first();
                    $mainorderinfo = Ordercollection::where('company','ubl')->where('outletcode',$ubloutletcode)->where('invoiceid',$invoicecodenow)->where('okupdate','>=','1')->get();
                    if(count($mainorderinfo) > 0){
                      $mainorderinfo=$mainorderinfo[0];
                        //$currentstate=($mainorderinfo->currentstate)+1;
                         $last_dueamount = floatval($mainorderinfo->dueamount);
                         $total_payment= floatval($mainorderinfo->totalamount);
                         $total_paidamount= floatval($mainorderinfo->paidamount);


                         $new_paid=floatval($total_paidamount)+floatval($paidamount);
                         $new_dueamout=floatval($last_dueamount)-floatval($paidamount);

                         if($new_dueamout>0){
                            $activestate=1; $closestate=0;
                         }else{
                             $activestate=0; $closestate=3;
                         }




                         

                         $newloantracking =  loantracking::where('companyname','ubl')->where('outletcode',$ubloutletcode)->where('invoiceid',$invoicecodenow)->where('banksink','>=','3')->get();
                        if(count($newloantracking) > 0){
                            $inv_adjustment= floatval($newloantracking[0]->adjustedamount);

                            $new_adjustment= floatval($inv_adjustment)+floatval($paidamount);


                            $inv_beforedue= floatval($newloantracking[0]->loanamount)-floatval($inv_adjustment);
                            $inv_afterdue= floatval($newloantracking[0]->loanamount)-floatval($new_adjustment);



                                $updateorders=Ordercollection::where('company','ubl')->where('outletcode',$ubloutletcode)->where('invoiceid',$invoicecodenow)->
                                    update(['paidamount'=>$new_paid,'dueamount'=>$new_dueamout,'lastpaidby'=>$request->user()->id,'lastpaidatdate'=>$currentdtaetime,'activestate'=>$activestate]);

                               

                                 $updateinvs=loantracking::where('companyname','ubl')->where('outletcode',$ubloutletcode)->where('invoiceid',$invoicecodenow)->where('banksink','>=','3')->
                                    update(['adjustedamount'=>$new_adjustment,'lastadjustmentdate'=>$currentdtaetime,'adjustmentapprovalby'=>$request->user()->id,'lastadjustmentdate'=>$currentdtaetime,'closestate'=>$closestate]);



                                     if($updateinvs){
                                        $crentdttm=date('Y-m-d H:i:s'); 
                                         
                                         //FOR DATA STORING TO LOAN FINALIGATION

                                            $loantrackinginfo = new Repaymentcollection;
                                            $loantrackinginfo->companyname='ubl'; 

                                            $loantrackinginfo->orderid=$mainorderinfo->id; 
                                            $loantrackinginfo->outletid=$outletdatnow->id; 
                                            $loantrackinginfo->outletcode = $ubloutletcode;  
                                            $loantrackinginfo->invoiceid= $invoicecodenow;
                                            $loantrackinginfo->distributorid= $distributerid;
                                            $loantrackinginfo->totalbill = $newloantracking[0]->totalbill;
                                            $loantrackinginfo->loanamount =$newloantracking[0]->loanamount;
                                            $loantrackinginfo->startingdate =$newloantracking[0]->startingdate;
                                            $loantrackinginfo->beforedue=$inv_beforedue;
                                            $loantrackinginfo->afterdue=$inv_afterdue;

                                            $loantrackinginfo->collectedamount=$paidamount;
                                            $loantrackinginfo->moneyreceptno=$moneyreceipt;

                                            $loantrackinginfo->updated_by = $request->user()->id;
                                            $loantrackinginfo->created_at=date('Y-m-d H:i:s'); 
                                            $datastpone= $loantrackinginfo->save();

                                            if($datastpone){
                                                $newsms="Dear+Merchant,".$paidamount."Tk+is+collected+for+UBL+order+reff.+invoice+".$invoicecodenow."+Your+Due for this invoice is=".$new_dueamout."Tk+Thanks+for+payment";
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
                                                    'message' => 'Payment Successful'
                                                ], 200);
                                            }


                                            
                                         
                                    }else{
                                        return response()->json([
                                            'response' => 'error',
                                            'message' => 'FAIL UPDATE LOAN TABLE'
                                        ], 400);

                                    }
                        }else{
                            return response()->json([
                                'response' => 'error',
                                'message' => 'LOAN TRACKING NOT FOUND'
                            ], 400);
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
            }else{
                return response()->json([
                    'response' => 'error',
                    'message' => 'Moneyreceipt Already there'
                ], 400);
            }     
                
        } else{
            return response()->json([
                'response' => 'error',
                'message' => 'Not Eligible to order'
            ], 400);
        }
        
        
    }  

}