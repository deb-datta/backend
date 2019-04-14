<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Loantracking;
use App\Marchantlist;
use APP\User;
use App\Otpfailedoverned;
use App\Creditlimitlogs;
use App\Transactioninfo;
use App\Repaymentcollection;
use DB;
use DateTime;
use Validator;
use File;
use Image;
use GuzzleHttp\Pool;
use GuzzleHttp\Client;
//use App\Http\Controllers\Image;

class CurlrqstController extends Controller
{   
    public function getMytockenfrobank(){
        $mreturnaccestkn="";
        $newurls='http://localhost/bankccloan/public/oauth/token';
        $headers = ['Accept'=>'application/json'];
        $postfields = [
            'grant_type'=>'password',
            'client_id'=> '2',
            'client_secret'=> 'gfhfgh58585JHFHJFJVBiuibkeHdkhjfsdjfds0923fd435',
            'username'=> '01971200249',
            'password'=> '123456',
        ];
                // Get cURL resource
                $curl = curl_init();
                // Set some options - we are passing in a useragent too here
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL => $newurls,
                    CURLOPT_USERAGENT => 'SMS mobile Request',
                    CURLOPT_HTTPHEADER=>$headers,
                    CURLOPT_POST=>1,
                    CURLOPT_POSTFIELDS=>$postfields,
                ));
                // Send the request & save response to $resp
                $resp = curl_exec($curl);
                $jsondecode=json_decode($resp);
                $mreturnaccestkn=$jsondecode->access_token;
                // Close request to clear up some resources
                curl_close($curl);

        return $mreturnaccestkn; 
    }

    function post($url, $postVars = array()){

        $postfields = [
                'grant_type'=>'password',
                'client_id'=> '2',
                'client_secret'=> 'gfhfgh58585JHFHJFJVBiuibkeHdkhjfsdjfds0923fd435',
                'username'=> '01971200249',
                'password'=> '123456',
            ];
        //Transform our POST array into a URL-encoded query string.
        $postStr = http_build_query($postfields);
        //Create an $options array that can be passed into stream_context_create.
        $options = array(
            'http' =>
                array(
                    'method'  => 'POST', //We are using the POST HTTP method.
                    'header'  => 'Content-type: application/json',
                    'content' => $postStr //Our URL-encoded query string.
                )
        );
        //Pass our $options array into stream_context_create.
        //This will return a stream context resource.
        $streamContext  = stream_context_create($options);
        //Use PHP's file_get_contents function to carry out the request.
        //We pass the $streamContext variable in as a third parameter.
        $result = file_get_contents($url, false, $streamContext);
        //If $result is FALSE, then the request has failed.
        if($result === false){
            //If the request failed, throw an Exception containing
            //the error.
            $error = error_get_last();
            throw new Exception('POST request failed: ' . $error['message']);
        }
        //If everything went OK, return the response.
        return $result;
    }
     //Marchant Loan Amount Sync
    public function balancesync(Request $request)
    { /* $myacccode="435435435";
         $newurls="http://localhost/testapibank/addinvoice.php";//'http://127.0.0.1/bankccloan/public/api/bnkfbv1/invoice/loan';
                            $authorization='Bearer '.$myacccode;
                            $headers = [
                                'Content-Type'=>'application/json',
                                'Authorization'=>$authorization,
                                'Accept'=>'application/json',

                            ];
                            $postfields = [
                                'name'=>'Rahman Store Rampura',
                                'outletcode'=>'UBL_123456',
                                'mobileno'=>'01711200249',
                                'accountnumber'=>'1549477486',
                                'invoiceid'=>'UBL_666666',
                                'loanamount'=>'4000',
                                'distributorid'=>'99999',
                                'distributoraccount'=>'99999',
                            ];
                            //print_r($postfields);
                            $curl = curl_init();
                            // Set some options - we are passing in a useragent too here
                            curl_setopt_array($curl, array(
                                CURLOPT_RETURNTRANSFER => 1,
                                CURLOPT_URL => $newurls,
                                CURLOPT_USERAGENT => 'Loan Request',
                                CURLOPT_HTTPHEADER=>$headers,
                               // CURLOPT_USERAGENT=>$userAgent,
                                CURLOPT_POST=>1,
                                CURLOPT_POSTFIELDS=>$postfields,
                            ));
                            // Send the request & save response to $resp
                            $resp = curl_exec($curl);
                             curl_close($curl);
                             print_r($resp);*/
        $this->validate($request, [
            'mytoken'=> 'required',
        ]);
        if($request->mytoken=="2394234hHJHGGUJ88UJPNnghg=uiu"){
         
		  $myacccode="435435435435435";
           $getoutlets = Loantracking::select(['loantracking.id',DB::raw('marchantlist.outletName AS name'),'marchantlist.outletcode','marchantlist.mobileno','marchantlist.accountnumber','loantracking.invoiceid','loantracking.loanamount','loantracking.distributorid','loantracking.startingdate','loantracking.banksink','distributer.distributoraccount','distributer.compdistributorid'])->leftJoin('marchantlist', 'loantracking.outletid', '=', 'marchantlist.id')->leftJoin('distributer', 'loantracking.distributorid', '=', 'distributer.id')->where('loantracking.banksink','!=','3')->get()->take(10);//->orderBy('id','ASC')
            if(count($getoutlets) > 0){
                
                foreach ($getoutlets as $key => $value) {
                    if($value->loanamount && $value->loanamount<50000){

                        $idFinal=$value->id;
                        $name=$value->name;
                        $outletcode=$value->outletcode;
                        $mobileno=$value->mobileno;
                        $accountnumber=$value->accountnumber;
                        $invoiceid=$value->invoiceid;
                        $loanamount=$value->loanamount;
                        $distributorid=$value->distributorid;
                        $distributoraccount=$value->distributoraccount;
                        //print_r(expression)
                        //Starting REQUEST NOW
                            // FOR POST TO ADD LOAN TO BANK
                        $myacccode="435435435";
                        $newurls="http://35.229.195.70/testapibank/addinvoice.php";//'http://127.0.0.1/bankccloan/public/api/bnkfbv1/invoice/loan';
                            $authorization='Bearer '.$myacccode;
                            $headers = [
                                'Content-Type'=>'application/json',
                                'Authorization'=>$authorization,
                                'Accept'=>'application/json',

                            ];
                            $postfields = [
                                'name'=>$name,
                                'outletcode'=>$outletcode,
                                'mobileno'=>$mobileno,
                                'accountnumber'=>$accountnumber,
                                'invoiceid'=>$invoiceid,
                                'loanamount'=>$loanamount,
                                'distributorid'=>$distributorid,
                                'distributoraccount'=>$distributoraccount,
                            ];
                           // print_r($postfields);
                            $curl = curl_init();
                            // Set some options - we are passing in a useragent too here
                            curl_setopt_array($curl, array(
                                CURLOPT_RETURNTRANSFER => 1,
                                CURLOPT_URL => $newurls,
                                CURLOPT_USERAGENT => 'Loan Request',
                                CURLOPT_HTTPHEADER=>$headers,
                               // CURLOPT_USERAGENT=>$userAgent,
                                CURLOPT_POST=>1,
                                CURLOPT_POSTFIELDS=>$postfields,
                            ));
                            // Send the request & save response to $resp
                            $resp = curl_exec($curl);
                             curl_close($curl);

                            
                           // print_r($resp);
                            if($resp){
                                $transactionid="";
                                $jsondecode=json_decode($resp);

                                $transactionid=$jsondecode->transactionid;

                                if($transactionid){
                                    $datetimeooo=date('Y-m-d h:i a'); 
                                    $updateit = Loantracking::where('loantracking.banksink','!=','3')->where('loantracking.banksink','!=','3')->where('id',$idFinal)->where('invoiceid',$invoiceid)->where('loanamount',$loanamount)->update(['banksink'=>3,'bankpaidtocompany'=>1,'bankpaiddatetime'=>$datetimeooo,'bankloantransactionid'=>$transactionid]);



                                    $addtransactn= new Transactioninfo;
                                    $addtransactn->accountnumber=$accountnumber;
                                    $addtransactn->outletcode=$outletcode;
                                    $addtransactn->transactionid=$transactionid;
                                    $addtransactn->referenceinvoice=$invoiceid;
                                    $addtransactn->transactiondetails='Invoice wise Loan';
                                    $addtransactn->transactionhead=26;
                                    $addtransactn->debitamout=$loanamount;
                                    $addtransactn->creditamount=0;
                                    $addtransactn->save();
                                } 
                            }else{
                                
                            }
                           
                            // Close request to clear up some resources
                            





                    }else{
                        return response()->json([
                            'response' => 'error',
                            'message' => 'Loan Amount is not ok'
                        ], 400);
                    }
                    
                  
               }

               return response()->json(['status'=>'ok'], 200);
            }else{
                return response()->json(['status'=>'ok2'], 200);
            }
           
        }else{
            return response()->json([
                'response' => 'error',
                'message' => 'No Record Found'
            ], 400);
        }
        
        return response()->json([
                'response' => 'error',
                'message' => 'No Record Found'
            ], 400);

       
        
    }



    //Marchant Loan Repayment Sync
    public function repaymentAmntsync(Request $request)
    { /* $myacccode="435435435";
         $newurls="http://localhost/testapibank/addinvoice.php";//'http://127.0.0.1/bankccloan/public/api/bnkfbv1/invoice/loan';
                            $authorization='Bearer '.$myacccode;
                            $headers = [
                                'Content-Type'=>'application/json',
                                'Authorization'=>$authorization,
                                'Accept'=>'application/json',

                            ];
                            $postfields = [
                                'name'=>'Rahman Store Rampura',
                                'outletcode'=>'UBL_123456',
                                'mobileno'=>'01711200249',
                                'accountnumber'=>'1549477486',
                                'invoiceid'=>'UBL_666666',
                                'loanamount'=>'4000',
                                'distributorid'=>'99999',
                                'distributoraccount'=>'99999',
                            ];
                            //print_r($postfields);
                            $curl = curl_init();
                            // Set some options - we are passing in a useragent too here
                            curl_setopt_array($curl, array(
                                CURLOPT_RETURNTRANSFER => 1,
                                CURLOPT_URL => $newurls,
                                CURLOPT_USERAGENT => 'Loan Request',
                                CURLOPT_HTTPHEADER=>$headers,
                               // CURLOPT_USERAGENT=>$userAgent,
                                CURLOPT_POST=>1,
                                CURLOPT_POSTFIELDS=>$postfields,
                            ));
                            // Send the request & save response to $resp
                            $resp = curl_exec($curl);
                             curl_close($curl);
                             print_r($resp);*/
        $this->validate($request, [
            'mytoken'=> 'required',
        ]);
        if($request->mytoken=="2394234hHJHGGUJ88UJPNnghg=uiu"){
         
          $myacccode="435435435435435";
           $getoutlets = Repaymentcollection::select(['repaymentcollection.id',DB::raw('marchantlist.outletName AS name'),'marchantlist.outletcode','marchantlist.mobileno','marchantlist.accountnumber','repaymentcollection.invoiceid','repaymentcollection.collectedamount','repaymentcollection.moneyreceptno','repaymentcollection.distributorid','repaymentcollection.created_at','repaymentcollection.banksink','distributer.distributoraccount','distributer.compdistributorid'])->leftJoin('marchantlist', 'repaymentcollection.outletid', '=', 'marchantlist.id')->leftJoin('distributer', 'repaymentcollection.distributorid', '=', 'distributer.id')->where('repaymentcollection.banksink','!=','3')->get()->take(10);//->orderBy('id','ASC')
            if(count($getoutlets) > 0){
                
                foreach ($getoutlets as $key => $value) {
                    if($value->collectedamount && $value->collectedamount<50000){

                        $idFinal=$value->id;
                        $name=$value->name;
                        $outletcode=$value->outletcode;
                        $mobileno=$value->mobileno;
                        $accountnumber=$value->accountnumber;
                        $invoiceid=$value->invoiceid;
                        $collectedamount=$value->collectedamount;

                        $moneyreceptno=$value->moneyreceptno;

                        $distributorid=$value->distributorid;
                        $distributoraccount=$value->distributoraccount;
                        //print_r(expression)
                        //Starting REQUEST NOW
                            // FOR POST TO ADD LOAN TO BANK
                        $myacccode="435435435";
                        $newurls="http://35.229.195.70/testapibank/addcollection.php";//'http://127.0.0.1/bankccloan/public/api/bnkfbv1/invoice/loan';
                            $authorization='Bearer '.$myacccode;
                            $headers = [
                                'Content-Type'=>'application/json',
                                'Authorization'=>$authorization,
                                'Accept'=>'application/json',

                            ];
                            $postfields = [
                                'name'=>$name,
                                'outletcode'=>$outletcode,
                                'mobileno'=>$mobileno,
                                'accountnumber'=>$accountnumber,
                                'invoiceid'=>$invoiceid,
                                'collectedamount'=>$collectedamount,

                                'moneyreceptno'=>$moneyreceptno,
                                'distributorid'=>$distributorid,
                                'distributoraccount'=>$distributoraccount,
                            ];
                            //print_r($postfields);
                            $curl = curl_init();
                            // Set some options - we are passing in a useragent too here
                            curl_setopt_array($curl, array(
                                CURLOPT_RETURNTRANSFER => 1,
                                CURLOPT_URL => $newurls,
                                CURLOPT_USERAGENT => 'Loan Repayment Request',
                                CURLOPT_HTTPHEADER=>$headers,
                               // CURLOPT_USERAGENT=>$userAgent,
                                CURLOPT_POST=>1,
                                CURLOPT_POSTFIELDS=>$postfields,
                            ));
                            // Send the request & save response to $resp
                            $resp = curl_exec($curl);
                             curl_close($curl);

                            
                            //print_r($resp);
                            if($resp){
                                $transactionid="";
                                $jsondecode=json_decode($resp);

                                $transactionid=$jsondecode->transactionid;
                                //$currentbalancedd=$jsondecode->currentbalance;
                                // print_r($resp);
                                if($transactionid){
                                    $datetimeooo=date('Y-m-d h:i a'); 
                                    $updateit = Repaymentcollection::where('repaymentcollection.banksink','!=','3')->where('repaymentcollection.banksink','!=','3')->where('id',$idFinal)->where('invoiceid',$invoiceid)->where('collectedamount',$collectedamount)->update(['banksink'=>3,'bankpaiddatetime'=>$datetimeooo,'bankloantransactionid'=>$transactionid]);
                                    if($updateit){
                                        $updateitbalance = Marchantlist::where('mobileno',$mobileno)->where('outletcode',$outletcode)->where('accountnumber',$accountnumber)->increment('currentavailable', $collectedamount); 

                                        $addtransactn= new Transactioninfo;
                                        $addtransactn->accountnumber=$accountnumber;
                                        $addtransactn->outletcode=$outletcode;
                                        $addtransactn->transactionid=$transactionid;
                                        $addtransactn->referenceinvoice=$invoiceid;
                                        $addtransactn->transactiondetails='Invoice wise Loan Repayment-MR '.$moneyreceptno;
                                        $addtransactn->transactionhead=28;
                                        $addtransactn->debitamout=0;
                                        $addtransactn->creditamount=$collectedamount;
                                        $addtransactn->repaymoneyreceipt=$moneyreceptno;
                                        $addtransactn->save(); 
                                    }

                                    
                                } 
                            }else{
                                
                            }
                           
                            // Close request to clear up some resources
                            





                    }
                    
                  
               }

               return response()->json(['status'=>'ok'], 200);
            }else{
                return response()->json(['status'=>'ok'], 200);
            }
           
        }else{
            return response()->json([
                'response' => 'error',
                'message' => 'No Record Found'
            ], 400);
        }
        
        return response()->json([
                'response' => 'error',
                'message' => 'No Record Found'
            ], 400);

       
        
    }








//BANK EXTRA CHARGE CALCULATION APPLY API
     //BANK CHARGE Sync
    public function bankchargeSync(Request $request)
    { 
        $this->validate($request, [
            'mytoken'=> 'required',
            'name'=> 'required',
            'outletcode'=> 'required',
            'mobileno'=> 'required',
            'accountnumber'=> 'required',
            'perpose'=> 'required',
            'charge'=> 'required|numeric|min:0|not_in:0',
            'transactionid'=> 'required',
        ]);
        if($request->mytoken=="2394234hHKK5UJ88UJPNnghg=uiu"){
         
          $myacccode="435435435435435";
           $outletcheck = Marchantlist::where('mobileno',$request->mobileno)->where('outletcode',$request->outletcode)->where('accountnumber',$request->accountnumber)->get(); 
            if(count($outletcheck) > 0){
                
                        $datetimeooo=date('Y-m-d h:i a'); 
                        $chargeamount= $request->charge;

                        $debitamount=0; $creditamount=0;
                        if($chargeamount>0){
                            $debitamount=$chargeamount; $creditamount=0;
                        }else{
                            $debitamount=0; $creditamount=$chargeamount;
                        }
                                   
                                    
                                        $updateitbalance = Marchantlist::where('mobileno',$request->mobileno)->where('outletcode',$request->outletcode)->where('accountnumber',$request->accountnumber)->increment('currentavailable',$chargeamount); 
                                         if($updateitbalance){

                                            $addtransactn= new Transactioninfo;
                                            $addtransactn->accountnumber=$request->accountnumber;
                                            $addtransactn->outletcode=$request->outletcode;
                                            $addtransactn->transactionid=$request->transactionid;
                                            $addtransactn->referenceinvoice='';
                                            $addtransactn->transactiondetails=$request->perpose;
                                            $addtransactn->transactionhead=30;
                                            $addtransactn->debitamout=$debitamount;
                                            $addtransactn->creditamount=$creditamount;
                                            $addtransactn->repaymoneyreceipt='';
                                            $addtransactn->updated_by=$request->user()->id;
                                            $datasaved=$addtransactn->save(); 
                                   
                                            
                                            return response()->json(['status'=>'ok'], 200);
                                            
                                        }else{
                                            return response()->json([
                                                'response' => 'error',
                                                'message' => 'Failed to save'
                                            ], 400);
                                        }
                    
                  
            }else{
                return response()->json([
                    'response' => 'error',
                    'message' => 'No Record Found'
                ], 400);
            }

               
            
           
        }else{
            return response()->json([
                'response' => 'error',
                'message' => 'Permission Denied'
            ], 400);
        }
        

       
        
    }
























    
    //*-----------------------UBL API OUTLET ONBOARDING----------------------*/
    public function onboardOutlet(Request $request)
    {
        $this->validate($request, [
            'nidorbinno' => 'required|unique:marchantlist',
            'name'=> 'required',
            'mobileno'=> 'required',
            'address'=> 'required',
            'doctype'=> 'required',
            'marchantdoc' => 'max:1000000|mimes:png,jpg,jpeg,PNG,JPG,bmp',
        ]);
        $imageName="";
       if($request->hasFile('marchantdoc')){
                $imageName = time().'.'.$request->marchantdoc->getClientOriginalExtension();
                $request->marchantdoc->move(public_path('uploads/outlet/docs'), $imageName);
                $image = Image::make(sprintf('uploads/outlet/docs/%s', $imageName))
                    ->resize(null,300, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save();
        }
       /* $filedocName="";
        if($request->hasFile('docpdf')){
                $filedocName = time().'.'.$request->docpdf->getClientOriginalExtension();
                $request->docpdf->move(public_path('uploads/orderitemdtls'), $filedocName);
               
        }*/
            
            $marchant = new Marchantlist;

            $marchant->outletcode = 'UBL_'.$request->outletcode;  
            $marchant->outletName = $request->name;  
            $marchant->mobileno = $request->mobileno;
            $marchant->nidorbinno = $request->nidorbinno;  
            $marchant->address = $request->address; 
            $marchant->doctype = $request->doctype;  
            $marchant->storelat = $request->storelat; 
            $marchant->storelong = $request->storelong;     
            $marchant->enlisted_by = $request->user()->id;                
            $marchant->enlisted_bydistributor = $request->user()->distributerid;

            $marchant->nidorbinnopicture = $imageName;
            
            $marchant->updated_by = $request->user()->id;

            $marchant->enlisted_at=date('Y-m-d H:i:s'); 
            $marchant->created_at=date('Y-m-d H:i:s'); 
            $marchant->save();
        

        return response()->json([
            'message' => 'Outlet enlisted successfully'
        ], 200);
    }
    //Specific outlet FairBanc Data get
    public function specificdOutletdata(Request $request)
    {
        $this->validate($request, [
            'mobileno'=> 'required',
        ]);
        
            //$ubloutletcode="UBL_".$request->outletcode;
            $marchantdata = Marchantlist::where('mobileno',$request->mobileno)->get(['id AS fbid','outletName AS name','outletcode','mobileno','address','fbcreditlinebm AS creditlimit' ,'currentavailable AS availablelimit' ,'lastfbapicheck AS lastoparationat' ]);
       
            
            if(count($marchantdata) > 0){
                $outletdatnow=$marchantdata->first();
                $outletcode=$outletdatnow['outletcode'];
                $nowexplode=explode('UBL_',$outletcode);
                $outletdatnow['outletcode']=$nowexplode[1];
                return response()->json($outletdatnow, 200);
            }
        return response()->json([
            'response' => 'error',
            'message' => 'No Record Found'
        ], 400);
    }




}
