<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Marchantlist;
use APP\User;
use App\Otpfailedoverned;
use App\Creditlimitlogs;
use App\Transactioninfo;
use App\Merchantonboarding;
use App\Merchantkycform;
use DateTime;
use DB;
use Validator;
use File;
use Image;
//use App\Http\Controllers\Image;

class OnboardmarchantController extends Controller
{   

    
    public function index(Request $request)
    {
        $getoutlets = Merchantonboarding::select()->orderBy('id','DESC')->paginate(15);

            if(count($getoutlets) > 0){
                return response()->json($getoutlets, 200);
            }
        return response()->json([
            'response' => 'error',
            'message' => 'No Record Found'
        ], 400);
    } 
    //SEARCH MERCHANT
    public function searchmerchant(Request $request)
    {
        $merchantmobile=$request->mobileno;
        $merchantcode=$request->outletcode;

        if($merchantmobile){
            $getoutlets = Merchantonboarding::select()->where('mobileno',$merchantmobile)->orderBy('id','DESC')->paginate(15);

            if(count($getoutlets) > 0){
                return response()->json($getoutlets, 200);
            }
        }else if($merchantcode){
            $merchantcode=$merchantcode;
            $getoutlets = Merchantonboarding::select()->where('outletcode',$merchantcode)->orderBy('id','DESC')->paginate(15);

            if(count($getoutlets) > 0){
                return response()->json($getoutlets, 200);
            }
        }

        
        return response()->json([
            'response' => 'error',
            'message' => 'No Record Found'
        ], 400);
    } 
    public function getSpecificmerchantdatas(Request $request,$id)
    {
        if($id){
            $getitemms = Merchantonboarding::where("id",$id)->get();
            $getitemmsdatas = Merchantkycform::where("refdtaid",$id)->get();
            if(count($getitemms) > 0){
                $datasss=$getitemms->first();
                $datasssnkt=$getitemmsdatas->first();
                return response()->json(['gnrl'=>$datasss,'kyc'=>$datasssnkt], 200);
            }
        }
        return response()->json([
            'response' => 'error',
            'message' => 'No Record Found'
        ], 400);
    } 

    public function imageAnalysysowner(Request $request,$id){
        $getitemms = Merchantonboarding::where("id",$id)->first();
        $imageName=$getitemms->facedetectpic;
        $facedetectpose=$getitemms->facedetectpose;
        if($imageName){
           $newurls='https://vision.googleapis.com/v1/images:annotate?key=AIzaSyCMd-4yrcFcasbIKL0xBIjGaJwhMf7u3M4';
                            $requestparameters="{'requests':[{'image': { 'source': { 'imageUri': 'http://35.229.195.70/distributorbackend/public/uploads/outlet/face/".$imageName."'}},'features': [{'type': 'FACE_DETECTION'}]}]}";
                          $headers = array(
                                            'Content-Type:application/json',
                                        );
                            $curl = curl_init();
                            // Set some options - we are passing in a useragent too here
                            curl_setopt_array($curl, array(
                                CURLOPT_POST=>1,
                                CURLOPT_RETURNTRANSFER => 1,
                                CURLOPT_HTTPHEADER=>$headers,
                                CURLOPT_URL => $newurls,
                                CURLOPT_POSTFIELDS=>$requestparameters,
                                CURLOPT_USERAGENT => 'SMS mobile Request'
                            ));
                            // Send the request & save response to $resp
                            $resp = curl_exec($curl);
                            // Close request to clear up some resources
                            curl_close($curl);

                            if($resp ){
                                 $facedatas=json_decode($resp);

                                $datasasasas=$facedatas->responses[0]->faceAnnotations[0];

                                $detectionConfidence=$datasasasas->detectionConfidence;
                                $tiltAngle=$datasasasas->tiltAngle;
                                $joyLikelihood=$datasasasas->joyLikelihood;
                                $sorrowLikelihood=$datasasasas->sorrowLikelihood;
                                $angerLikelihood=$datasasasas->angerLikelihood;
                                $surpriseLikelihood=$datasasasas->surpriseLikelihood;
                                $headwearLikelihood=$datasasasas->headwearLikelihood;                            
                                $rollAngle=$datasasasas->rollAngle;
                                $okdoneego=0;
                                if($detectionConfidence>0.75 && $headwearLikelihood=='VERY_UNLIKELY'){
                                    if($facedetectpose==1){ //HAPPY FACE
                                        if(($joyLikelihood=='VERY_LIKELY') ||($joyLikelihood=='LIKELY')){
                                            $okdoneego=1;
                                        }

                                    }else if($facedetectpose==2){ //HAPPY FACE
                                        if(($rollAngle>15) || ($rollAngle<-15)){
                                            $okdoneego=1;
                                        }

                                    }else if($facedetectpose==3){ //HAPPY FACE
                                        if(($angerLikelihood=='VERY_LIKELY') ||($angerLikelihood=='LIKELY')){
                                            $okdoneego=1;
                                        }

                                    }else if($facedetectpose==4){ //HAPPY FACE
                                        if(($surpriseLikelihood=='VERY_LIKELY') ||($surpriseLikelihood=='LIKELY')){
                                            $okdoneego=1;
                                        }

                                    }else if($facedetectpose==5){ //HAPPY FACE
                                        if(($sorrowLikelihood=='VERY_LIKELY') ||($sorrowLikelihood=='LIKELY')){
                                            $okdoneego=1;
                                        }

                                    }else{ //HAPPY FACE
                                            $okdoneego=1;
                                        

                                    }
                                }

                                 // read all existing data into an array
               // $data = Image::make('http://35.229.195.70/distributorbackend/public/uploads/outlet/nid/1551287809.jpg')->exif();
                // read model of the camera
                //$name = Image::make('http://35.229.195.70/distributorbackend/public/uploads/outlet/nid/1551287809.jpg')->exif('Model');
                                 return response()->json(['confidence'=>$detectionConfidence,'tiltangle'=>$tiltAngle,'joy'=>$joyLikelihood,'angry'=>$angerLikelihood,'surprise'=>$surpriseLikelihood,'sad'=>$sorrowLikelihood,'headwear'=>$headwearLikelihood,'rollangle'=>$rollAngle,'perfect'=>$okdoneego], 200);
                               

                            }


            }
    }

    public function logoAnalysysowner(Request $request,$id){
        $getitemms = Merchantonboarding::where("id",$id)->first();
        $imageName=$getitemms->innerimage;
       // $facedetectpose=$getitemms->facedetectpose;
        if($imageName){
           $newurls='https://vision.googleapis.com/v1/images:annotate?key=AIzaSyCMd-4yrcFcasbIKL0xBIjGaJwhMf7u3M4';
                            $requestparameters="{'requests':[{'image': { 'source': { 'imageUri': 'http://35.229.195.70/distributorbackend/public/uploads/outlet/inner/".$imageName."'}},'features': [{'type': 'OBJECT_LOCALIZATION'}]}]}";
                          $headers = array(
                                            'Content-Type:application/json',
                                        );
                            $curl = curl_init();
                            // Set some options - we are passing in a useragent too here
                            curl_setopt_array($curl, array(
                                CURLOPT_POST=>1,
                                CURLOPT_RETURNTRANSFER => 1,
                                CURLOPT_HTTPHEADER=>$headers,
                                CURLOPT_URL => $newurls,
                                CURLOPT_POSTFIELDS=>$requestparameters,
                                CURLOPT_USERAGENT => 'SMS mobile Request'
                            ));
                            // Send the request & save response to $resp
                            $resp = curl_exec($curl);
                            // Close request to clear up some resources
                            curl_close($curl);

                            if($resp ){
                                $responsesd=json_decode($resp);

                                if(sizeof($responsesd->responses)){

                                    $datasasasas=$responsesd->responses[0]->localizedObjectAnnotations;
                                    return response()->json(['datas'=>$datasasasas], 200);
                                }else{
                                    return response()->json('', 200);
                                }

                                /*print_r($resp);die();
                                 $facedatas=json_decode($resp);

                                $datasasasas=$facedatas->responses[0]->faceAnnotations[0];

                                $detectionConfidence=$datasasasas->detectionConfidence;
                                $tiltAngle=$datasasasas->tiltAngle;
                                $joyLikelihood=$datasasasas->joyLikelihood;
                                $headwearLikelihood=$datasasasas->headwearLikelihood;                            
                                $rollAngle=$datasasasas->rollAngle;
                                $okdoneego=0;
                                if($detectionConfidence>0.75 && $headwearLikelihood=='VERY_UNLIKELY'){
                                    if($facedetectpose==1){ //HAPPY FACE
                                        if(($joyLikelihood=='VERY_LIKELY') ||($joyLikelihood=='LIKELY')){
                                            $okdoneego=1;
                                        }

                                    }else if($facedetectpose==2){ //HAPPY FACE
                                        if(($rollAngle>15) || ($rollAngle<-15)){
                                            $okdoneego=1;
                                        }

                                    }else{ //HAPPY FACE
                                            $okdoneego=1;
                                        

                                    }
                                }*/

                                 // read all existing data into an array
               // $data = Image::make('http://35.229.195.70/distributorbackend/public/uploads/outlet/nid/1551287809.jpg')->exif();
                // read model of the camera
                //$name = Image::make('http://35.229.195.70/distributorbackend/public/uploads/outlet/nid/1551287809.jpg')->exif('Model');
                                 //return response()->json(['confidence'=>$detectionConfidence,'tiltangle'=>$tiltAngle,'joy'=>$joyLikelihood,'headwear'=>$headwearLikelihood,'rollangle'=>$rollAngle,'perfect'=>$okdoneego], 200);
                               

                            }


            }
    }


     public function landmarkAnalysysowner(Request $request,$id){
        $getitemms = Merchantonboarding::where("id",$id)->first();
        $imageName=$getitemms->outerimage;
       // $facedetectpose=$getitemms->facedetectpose;
        if($imageName){
           $newurls='https://vision.googleapis.com/v1/images:annotate?key=AIzaSyCMd-4yrcFcasbIKL0xBIjGaJwhMf7u3M4';
                            $requestparameters="{'requests':[{'image': { 'source': { 'imageUri': 'http://35.229.195.70/distributorbackend/public/uploads/outlet/outer/".$imageName."'}},'features': [{'type': 'LANDMARK_DETECTION'}]}]}";
                          $headers = array(
                                            'Content-Type:application/json',
                                        );
                            $curl = curl_init();
                            // Set some options - we are passing in a useragent too here
                            curl_setopt_array($curl, array(
                                CURLOPT_POST=>1,
                                CURLOPT_RETURNTRANSFER => 1,
                                CURLOPT_HTTPHEADER=>$headers,
                                CURLOPT_URL => $newurls,
                                CURLOPT_POSTFIELDS=>$requestparameters,
                                CURLOPT_USERAGENT => 'SMS mobile Request'
                            ));
                            // Send the request & save response to $resp
                            $resp = curl_exec($curl);
                            // Close request to clear up some resources
                            curl_close($curl);

                            if($resp ){
                                $responsesd=json_decode($resp);

                                if(sizeof($responsesd->responses)){

                                    $datasasasas=$responsesd->responses[0]->landmarkAnnotations;
                                    return response()->json(['datas'=>$datasasasas], 200);
                                }else{
                                    return response()->json([], 200);
                                }

                                /*print_r($resp);die();
                                 $facedatas=json_decode($resp);

                                $datasasasas=$facedatas->responses[0]->faceAnnotations[0];

                                $detectionConfidence=$datasasasas->detectionConfidence;
                                $tiltAngle=$datasasasas->tiltAngle;
                                $joyLikelihood=$datasasasas->joyLikelihood;
                                $headwearLikelihood=$datasasasas->headwearLikelihood;                            
                                $rollAngle=$datasasasas->rollAngle;
                                $okdoneego=0;
                                if($detectionConfidence>0.75 && $headwearLikelihood=='VERY_UNLIKELY'){
                                    if($facedetectpose==1){ //HAPPY FACE
                                        if(($joyLikelihood=='VERY_LIKELY') ||($joyLikelihood=='LIKELY')){
                                            $okdoneego=1;
                                        }

                                    }else if($facedetectpose==2){ //HAPPY FACE
                                        if(($rollAngle>15) || ($rollAngle<-15)){
                                            $okdoneego=1;
                                        }

                                    }else{ //HAPPY FACE
                                            $okdoneego=1;
                                        

                                    }
                                }*/

                                 // read all existing data into an array
               // $data = Image::make('http://35.229.195.70/distributorbackend/public/uploads/outlet/nid/1551287809.jpg')->exif();
                // read model of the camera
                //$name = Image::make('http://35.229.195.70/distributorbackend/public/uploads/outlet/nid/1551287809.jpg')->exif('Model');
                                 //return response()->json(['confidence'=>$detectionConfidence,'tiltangle'=>$tiltAngle,'joy'=>$joyLikelihood,'headwear'=>$headwearLikelihood,'rollangle'=>$rollAngle,'perfect'=>$okdoneego], 200);
                               

                            }


            }
    }

 




















    public function getOutlettransaction(Request $request)
    {
        $this->validate($request, [
            'accountno'=> 'required',
        ]);
        if($request->accountno){
            $getitemms = Transactioninfo::select()->where("accountnumber",$request->accountno)->orderBy('id','DESC')->paginate(15);
            if(count($getitemms) > 0){
                return response()->json($getitemms, 200);
            }
        }else{
            return response()->json([
                'response' => 'error',
                'message' => 'No Record Found'
            ], 400);
        }
        
    } 
    public function getOutlettransactionlastmnth(Request $request)
    {
        $this->validate($request, [
            'outletcode'=> 'required',
        ]);
        if($request->outletcode){
            $fromDate=date("Y-m-d", strtotime("-30 days"));
            $tillDate=date('Y-m-d'); 
            $getitemms = Transactioninfo::select()->where("outletcode",$request->outletcode)->whereDate('created_at','>=', $fromDate)
            ->whereDate('created_at','<=', $tillDate)->orderBy('id','DESC')->paginate(200);
            if(count($getitemms) > 0){
                return response()->json($getitemms, 200);
            }
        }else{
            return response()->json([
                'response' => 'error',
                'message' => 'No Record Found'
            ], 400);
        }
        
    }
    public function getOutlettransactionfromto(Request $request)
    {
        $this->validate($request, [
            'outletcode'=> 'required',
            'fromdate'=> 'required',
            'todate'=> 'required',
        ]);
        if($request->outletcode){
            $fromDate=date("Y-m-d", strtotime($request->fromdate));
            $tillDate=date('Y-m-d', strtotime($request->todate));
            $getitemms = Transactioninfo::select()->where("outletcode",$request->outletcode)->whereDate('created_at','>=', $fromDate)
            ->whereDate('created_at','<=', $tillDate)->orderBy('id','DESC')->paginate(200);
            if(count($getitemms) > 0){
                return response()->json($getitemms, 200);
            }
        }else{
            return response()->json([
                'response' => 'error',
                'message' => 'No Record Found'
            ], 400);
        }
        
    }

    public function searchIteminfo(Request $request,$nm)
    {
        if($nm){
            $getitemms = Marchantlist::where("itemno",$nm)->get(['id','itemno']);
            if(count($getitemms) > 0){
                $datasss=$getitemms->first();
                return response()->json($datasss, 200);
            }
        }
        return response()->json([
            'response' => 'error',
            'message' => 'No Record Found'
        ], 400);
    } 
    public function datechecker($datadate){
        $datass=explode("-",$datadate);

        return ($datass[2]."-".$datass[1]."-".$datass[0]);
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'nidorbinno' => 'required|unique:marchantlist',
            'name'=> 'required',
            'outletcode'=> 'required',
            'mobileno'=> 'required',
            'address'=> 'required',
            'doctype'=> 'required',
            'logo' => 'max:10000|mimes:png,jpg,jpeg',
        ]);
        $imageName="";
        if($request->hasFile('logo')){
                $imageName = time().'.'.$request->logo->getClientOriginalExtension();
                $request->logo->move(public_path('uploads/outlet/docs'), $imageName);
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

            $marchant->outletName = $request->name; 
            $marchant->outletcode = $request->outletcode; 
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
    
    /*.......FairBac Creditline Update.......*/
    public function fbCreditlineupdate(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'mobileno'=> 'required',
            'otpchck'=> 'required',
        ]);
        $nowdattime=date('Y-m-d H:i:s'); 

       // print($request->user()->verifiedmobilecode); die();
        if($request->user()->nooforpfail>10){
            ///FOR REVOKE TOKEN AND SEND SMS NOTIFICATION
            $request->user()->token()->revoke();
            $request->user()->token()->delete(); 

            $userupdated=User::where('id',$request->user()->id)->update(['verifiedmobilecode'=>'','codeexpiredat'=>$nowdattime,'active'=>'0']);
            $datsaalert= new Otpfailedoverned;
            $datsaalert->userid=$request->user()->id;
            $datsaalert->merchantid=$request->id;
            $datsaalert->details="More than 10 time failed OTP Attemped for Marchant Credit assign request of ".$request->creditset;
            $datsaalert->userid=$request->user()->id;
            $isitok=$datsaalert->save();
            if($isitok){
                $newsms="More+than+10+time+failed+OTP+Attemped+for+Marchant+Credit+assign+request";
                $newurls='http://alphasms.biz/index.php?app=ws&u=indexer&h=351794a3122fab8ff8bbc78b8092797b&op=pv&to=01711200249&msg='.$newsms;

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


                if( $resp ){
                     return response()->json([
                        'message' => 'Outlet Fairbanc credit update successfull'
                    ], 200);
                }
            }
            return response()->json([
                    'response' => 'error',
                    'message' => 'No Record Found2'
                ], 400);
        }//FOR MULTIPLE OTP FAIL ATTEMPT
        if(($request->user()->verifiedmobilecode==$request->otpchck) && ($request->user()->codeexpiredat>=$nowdattime) && $request->user()->verifiedmobilecode){
            $userupdated=User::where('id',$request->user()->id)->update(['verifiedmobilecode'=>'','codeexpiredat'=>$nowdattime,'nooforpfail'=>'0']);
            $marchant =  Marchantlist::where('id',$request->id)->where('mobileno',$request->mobileno)->get()->first();
            if($marchant){
                $currentfblimit=$marchant->fbcreditlinebm;
                $availablefblimit=$marchant->currentavailable;

                $newavailable=$request->currentavailable+(($request->creditset)-($currentfblimit));
                if( $newavailable<0){
                    $newavailable=0;
                }
            
                //CHECKING TO BANK SERVER IF LIMIT IS VALID

                        $myacccode="435435435";
                        $newurls="http://35.229.195.70/testapibank/checksetamount.php";//'http://127.0.0.1/bankccloan/public/api/bnkfbv1/invoice/loan';
                            $authorization='Bearer '.$myacccode;
                            $headers = [
                                'Content-Type'=>'application/json',
                                'Authorization'=>$authorization,
                                'Accept'=>'application/json',

                            ];
                            $postfields = [
                                'name'=>$marchant->outletName,
                                'outletcode'=>$marchant->outletcode,
                                'mobileno'=>$marchant->mobileno,
                                'creditlimit'=>$request->creditset,
                                'distributorid'=>$marchant->enlisted_bydistributor,
                            ];
                           // print_r($postfields);
                            $curl = curl_init();
                            // Set some options - we are passing in a useragent too here
                            curl_setopt_array($curl, array(
                                CURLOPT_RETURNTRANSFER => 1,
                                CURLOPT_URL => $newurls,
                                CURLOPT_USERAGENT => 'Credit limit Request',
                                CURLOPT_HTTPHEADER=>$headers,
                               // CURLOPT_USERAGENT=>$userAgent,
                                CURLOPT_POST=>1,
                                CURLOPT_POSTFIELDS=>$postfields,
                            ));
                            // Send the request & save response to $resp
                            $resp = curl_exec($curl);
                             curl_close($curl);

                          //  print_r( $resp);
                            //print_r($resp);
                            if($resp){

                                $withdralllimit=0.0;
                                $jsondecode=json_decode($resp);

                                $withdralllimit=$jsondecode->withdralllimit;
                                $currentavailable=$jsondecode->currentavailable;
                                $accountnumber=$jsondecode->accountnumber;

                                if($withdralllimit){
                                     $nowdattime=date('Y-m-d H:i:s'); 
                                    /*-------------Everything OK-----------------*/
                                    $newupdatedsd=Marchantlist::where('id',$request->id)->where('mobileno',$request->mobileno)->update(['fbcreditlinebm' =>$withdralllimit,'currentavailable' => $currentavailable,'accountnumber' => $accountnumber,'lastfbapicheck'=>$nowdattime,'lastfbapicheck_by'=>$request->user()->id]);

                                        if($newupdatedsd){
                                                $newsms="Dear+Merchant,+your+new+FairBanc+credit+limit=".$withdralllimit."Tk+Available=".$currentavailable."Tk";
                                                $newurls='http://alphasms.biz/index.php?app=ws&u=indexer&h=351794a3122fab8ff8bbc78b8092797b&op=pv&to='.$request->mobileno.'&msg='.$newsms;
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


                                                if( $resp ){
                                                    $newcreditsetto= new Creditlimitlogs;
                                                    $newcreditsetto->userid=$request->user()->id;
                                                    $newcreditsetto->usermobileno=$request->user()->mobileno;

                                                    $newcreditsetto->limitrequested=$request->creditset;
                                                    $newcreditsetto->limitset=$withdralllimit;
                                                    $newcreditsetto->setformerchanid=$request->id;
                                                    $newcreditsetto->setformerchanmob=$request->mobileno;
                                                    $newcreditsetto->beforeamount=$currentfblimit;
                                                    $newcreditsetto->updated_by=$request->user()->id;
                                                    $newcreditsetto->banksink=3;
                                                    $newcreditsetto->confirmationbank=$accountnumber;

                                                    $finalsaves=$newcreditsetto->save();
                                                    if($finalsaves){
                                                        return response()->json([
                                                            'message' => 'Outlet Fairbanc credit update successfull'
                                                        ], 200);
                                                    }
                                                     
                                                }else{
                                                    return response()->json([
                                                            'response' => 'error',
                                                            'message' => 'No Record Found'
                                                        ], 400);
                                                }




                                            /*-------------Everything ok done------------*/
                                        }else{
                                            return response()->json([
                                                            'response' => 'error',
                                                            'message' => 'No Record Found'
                                                        ], 400);
                                        } 
                                }else{
                                    return response()->json([
                                        'response' => 'error',
                                        'message' => 'No Record Found'
                                        ], 400);
                                } 
            
            

                                    //$this->newsmssento($mobileno,$sms);

               
                            }else{
                                return response()->json([
                                    'response' => 'error',
                                    'message' => 'No Record Found'
                                ], 400);
                            }
            }else{
                return response()->json([
                    'response' => 'error',
                    'message' => 'No Record Found'
                ], 400);
            }
        }else{
            $numbertime=(($request->user()->nooforpfail)+1);
            $userupdated=User::where('id',$request->user()->id)->update(['nooforpfail'=>$numbertime]);

                return response()->json([
                    'response' => 'error',
                    'message' => 'No Record Found2'
                ], 400);
        }
            
          return response()->json([
                    'response' => 'error',
                    'message' => 'No Record Found2'
                ], 400);  
        

        
    }    
    
    //Marchant Credit line add OTP
    public function genarateotpMarchantws(Request $request)
    {
        $this->validate($request, [
            'mobileno'=> 'required',
        ]);
        
        $userid=$request->user()->id;
        $verifiedcode=rand(100000,999999);
        $codeexpiredat=date("Y-m-d H:i:s", strtotime("+30 minutes"));
        if($request->user()->mobileno){

        
            //$ubloutletcode="UBL_".$request->outletcode;
            $otpupdate = User::where('id',$userid)->update(['verifiedmobilecode'=>$verifiedcode,'codeexpiredat'=>$codeexpiredat ]);
       
            
            if($otpupdate && $request->user()->mobileno){
                $usersmobileno=$request->user()->mobileno;
                $newsms="Dear+User,+your+new+OTP=".$verifiedcode;
                $newurls='http://alphasms.biz/index.php?app=ws&u=indexer&h=351794a3122fab8ff8bbc78b8092797b&op=pv&to='.$usersmobileno.'&msg='.$newsms;
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

              /*      $newsms="Fairbanc OTP=".$verifiedcode;
                    $newurls='http://alphasms.biz/index.php?app=ws&u=indexer&h=351794a3122fab8ff8bbc78b8092797b&op=pv&to='.$request->user()->mobileno.'&msg='.$newsms;
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
*/

                    if( $resp ){
                        return response()->json([
                           'message' => 'Otp sending successfull'
                        ], 200);
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
                'message' => 'No Record Found'
            ], 400);
        }
        return response()->json([
            'response' => 'error',
            'message' => 'No Record Found'
        ], 400);
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
