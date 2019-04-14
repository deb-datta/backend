<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Marchantlist;
use APP\User;
use App\Merchantonboarding;
use App\Merchantkycform;
use DateTime;
use DB;
use Validator;
use File;
use Image;
//use App\Http\Controllers\Image;

class EkycController extends Controller
{   

    
    public function searchForoutlet(Request $request)
    {
        $this->validate($request, [
            'mobileno'=> 'required',
        ]);
        $getoutlets = Merchantonboarding::where('mobileno',$request->mobileno)->get();

            if(count($getoutlets) > 0){
                return response()->json($getoutlets->first(), 200);
            }
        return response()->json([
            'response' => 'error',
            'message' => 'No Record Found'
        ], 400);
    } 
    //OTP REQUEST
    public function otpRequest(Request $request)
    {
        $this->validate($request, [
            'id'=> 'required',
        ]);
        $userid=$request->user()->id;
        $verifiedcode=rand(100000,999999);
        $codeexpiredat=date("Y-m-d H:i:s", strtotime("+30 minutes"));
         $getoutlets = Merchantonboarding::where('id',$request->id)->get();
            if(count($getoutlets) > 0){
                $outletdata=$getoutlets->first();
                $mobileno=$outletdata->mobileno;
                 $otpupdate = Merchantonboarding::where('id',$request->id)->update(['otptemp'=>$verifiedcode,'otpvalidity'=>$codeexpiredat]);

                if($mobileno && $otpupdate){

                            $newsms="Dear+Merchant,your+Confirmation+OTP=".$verifiedcode;
                            $newurls='http://alphasms.biz/index.php?app=ws&u=indexer&h=351794a3122fab8ff8bbc78b8092797b&op=pv&to='.$mobileno.'&msg='.$newsms;

                          
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

                    return response()->json(['message'=>'ok'], 200);
                }else{
                    return response()->json([
                        'response' => 'error',
                        'message' => 'Update Failed'
                    ], 400);
                }

                
            }else{
                    return response()->json([
                        'response' => 'error',
                        'message' => 'Wrong Outlet'
                    ], 400);
            }
        
    } 
    //OTP VARIFY
    public function otpVerify(Request $request)
    {
        $this->validate($request, [
            'id'=> 'required',
            'otpcode'=> 'required',
        ]);
        $userid=$request->user()->id;
        $verifiedmobilecode=$request->otpcode;
        $currentdtaetime=date("Y-m-d H:i:s");
         $getoutlets = Merchantonboarding::where('id',$request->id)->where('otptemp',$verifiedmobilecode)->where('otpvalidity','>=',$currentdtaetime)->get();
            if(count($getoutlets) > 0){

                 $updateorders=Merchantonboarding::where('id',$request->id)->where('otptemp',$verifiedmobilecode)->
                    update(['updated_at'=>$currentdtaetime,'updated_by'=>$request->user()->id,'otptemp'=>'','otpvarifiedat'=>$currentdtaetime]);

                if($updateorders){
                    return response()->json(['message'=>'ok'], 200);
                }else{
                    return response()->json([
                        'response' => 'error',
                        'message' => 'Update Failed'
                    ], 400);
                }

                
            }else{
                    return response()->json([
                        'response' => 'error',
                        'message' => 'Wrong Outlet'
                    ], 400);
            }
        
    } 
    //NID DATA UPLOAD
    public function niddataUpdate(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'nidno'=> 'required',
            'ocrinfo'=> 'required',
            'nidpic' => 'max:10000000|mimes:png,jpg,jpeg',//max:10000000|mimes:png,jpg,jpeg
        ]);
        $imageName="";
        if($request->hasFile('nidpic')){
                $imageName = time().'.'.$request->nidpic->getClientOriginalExtension();
                $request->nidpic->move(public_path('uploads/outlet/nid'), $imageName);
                $image = Image::make(sprintf('uploads/outlet/nid/%s', $imageName))->orientate()
                    ->resize(null,1000, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save();
        }

        // if($request->hasFile('nidpic')){
            /*    $imageName = time().'.png';
                $path=public_path().'uploads/outlet/docs'.$imageName;

                Image::make(file_get_contents($request->nidpic))->save($path);*/
                //.$request->nidpic->getClientOriginalExtension();
               /* $request->nidpic->move(public_path('uploads/outlet/docs'), $imageName);
                $image = Image::make(sprintf('uploads/outlet/docs/%s', $imageName))
                    ->resize(null,1000, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save();*/
       // }
        $userid=$request->user()->id;
        $verifiedmobilecode=$request->otpcode;
        $currentdtaetime=date("Y-m-d H:i:s");
        if($imageName){
          /*  $newurls='https://vision.googleapis.com/v1/images:annotate?key=AIzaSyCMd-4yrcFcasbIKL0xBIjGaJwhMf7u3M4';
                            $requestparameters="{'requests':[{'image': { 'source': { 'imageUri': 'http://35.229.195.70/distributorbackend/public/uploads/outlet/nid/".$imageName."'}},'features': [{'type': 'TEXT_DETECTION'}]}]}";
                          $headers = array(
                                            'Content-Type:application/json',
                                            'Authorization: gfhjui',
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
                           // print_r($resp);
                            
                           
                                
                          //  }
                            
                           
                            // $dataresponse=$jsondecode['responses'];//[0]['textAnnotations'][0]['description']
                            if($resp ){*/
                                $oknid=0;
                                $raffdatas = str_replace(' ', '', $request->ocrinfo);
                                if (strpos($raffdatas,$request->nidno) !== false) {
                                    $oknid=1;
                                }

                              //  $jsondecoddd=json_decode($datass);
                                //print_r($jsondecoddd->responses);die(); 
                               /*  print_r("new");
                            $jsondecoddd=json_decode($resp);
                           // if( $jsondecoddd){
                                print_r($jsondecoddd['responses']);
                                 die();*/

                                        $merchantekyc = Merchantonboarding::where('id',$request->id)->first();
                                    
                                    $merchantekyc->niddatano=$request->nidno;
                                    $merchantekyc->nidownerpic = $imageName;
                                    $merchantekyc->niddetails = $request->ocrinfo;
                                    $merchantekyc->nidmatchauto=$oknid;

                                    $merchantekyc->ocrvarifiedat=$currentdtaetime; 

                                    $merchantekyc->updated_by = $request->user()->id;
                                    $merchantekyc->updated_at=date('Y-m-d H:i:s'); 
                                    $okfine=$merchantekyc->save();

                                if($okfine){


                                    return response()->json(['message'=>'ok'], 200);
                                }else{
                                    return response()->json([
                                        'response' => 'error',
                                        'message' => 'Update Failed'
                                    ], 400);
                                }

                            /*}else{
                                    return response()->json([
                                        'response' => 'error',
                                        'message' => 'Update Failed'
                                    ], 400);
                                }*/
                            
        }
                            
                            
        
    } 
    //FACE RECOGNITION DATA UPLOAD
    public function facereconize(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'poseno' => 'required',
            'ownerpic' => 'max:10000000|mimes:png,jpg,jpeg',//max:10000000|mimes:png,jpg,jpeg
        ]);
        $imageName="";
        if($request->hasFile('ownerpic')){
                $imageName = time().'.'.$request->ownerpic->getClientOriginalExtension();
                $request->ownerpic->move(public_path('uploads/outlet/face'), $imageName);
                $image = Image::make(sprintf('uploads/outlet/face/%s', $imageName))->orientate()
                    ->resize(null,1000, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save();
        }

        // if($request->hasFile('nidpic')){
            /*    $imageName = time().'.png';
                $path=public_path().'uploads/outlet/docs'.$imageName;

                Image::make(file_get_contents($request->nidpic))->save($path);*/
                //.$request->nidpic->getClientOriginalExtension();
               /* $request->nidpic->move(public_path('uploads/outlet/docs'), $imageName);
                $image = Image::make(sprintf('uploads/outlet/docs/%s', $imageName))
                    ->resize(null,1000, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save();*/
       // }
        $userid=$request->user()->id;
        $verifiedmobilecode=$request->otpcode;
        $currentdtaetime=date("Y-m-d H:i:s");
        if($imageName){
          /*  $newurls='https://vision.googleapis.com/v1/images:annotate?key=AIzaSyCMd-4yrcFcasbIKL0xBIjGaJwhMf7u3M4';
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
                                 return response()->json(['message'=>$resp], 200);
                                /*$facedatas=json_decode($resp);

                                $datasasasas=$facedatas->responses[0]->faceAnnotations[0];

                                $detectionConfidence=$datasasasas->detectionConfidence;
                                $tiltAngle=$datasasasas->tiltAngle;
                                $joyLikelihood=$datasasasas->joyLikelihood;
                                $headwearLikelihood=$datasasasas->headwearLikelihood;                            
                                $rollAngle=$datasasasas->rollAngle;
                                $okdoneego=0;
                                /*if($detectionConfidence>0.75 && $headwearLikelihood=='VERY_UNLIKELY'){
                                    if($request->poseno==1){ //HAPPY FACE
                                        if(($joyLikelihood=='VERY_LIKELY') ||($joyLikelihood=='LIKELY')){
                                            $okdoneego=1;
                                        }

                                    }else if($request->poseno==2){ //HAPPY FACE
                                        if(($rollAngle>15) || ($rollAngle<-15)){
                                            $okdoneego=1;
                                        }

                                    }else{ //HAPPY FACE
                                            $okdoneego=1;
                                        

                                    }
                                }
*/
                            

                                    $merchantekyc = Merchantonboarding::where('id',$request->id)->first();
                                    
                                    $merchantekyc->facedetectpose=$request->poseno;
                                    $merchantekyc->facedetectpic = $imageName;
                                    $merchantekyc->faceparametersall = "";//"Confidence:".$detectionConfidence.","."HeadWear:".$headwearLikelihood.","."Joy:".$joyLikelihood.","."Bend Angle:".$rollAngle;
                                   // $merchantekyc->nidmatchauto=$okdoneego;

                                    $merchantekyc->facedetectvarifiedat=$currentdtaetime; 

                                    $merchantekyc->updated_by = $request->user()->id;
                                    $merchantekyc->updated_at=date('Y-m-d H:i:s'); 
                                    $okfine=$merchantekyc->save();

                                    if($okfine){


                                        return response()->json(['message'=>'ok'], 200);
                                    }else{
                                        return response()->json([
                                            'response' => 'error',
                                            'message' => 'Update Failed'
                                        ], 400);
                                    }
                          /*  }else{
                                    return response()->json([
                                        'response' => 'error',
                                        'message' => 'Update Failed'
                                    ], 400);
                            }*/

                            
                             //print_r($joyLikelihood);

                            //[0]->detectionConfidence
                            // print_r($facedatas->responses[0]->faceAnnotations[0]['headwearLikelihood']); 
                            // print_r($facedatas->responses[0]->faceAnnotations[0]['tiltAngle']);
                            // print_r($facedatas->responses[0]->faceAnnotations[0]['joyLikelihood']);
                            // print_r($facedatas->responses[0]->faceAnnotations[0]['rollAngle']);

                            
                           // print_r($resp);
                            
                           
                                
                          //  }
                            
                           
                            // $dataresponse=$jsondecode['responses'];//[0]['textAnnotations'][0]['description']
                          /*  if($resp ){
                                $oknid=0;
                                if (strpos($request->ocrinfo,$request->nidno) !== false) {
                                    $oknid=1;
                                }

                            

                                        $merchantekyc = Merchantonboarding::where('id',$request->id)->first();
                                    
                                    $merchantekyc->niddatano=$request->nidno;
                                    $merchantekyc->nidownerpic = $imageName;
                                    $merchantekyc->niddetails = $request->ocrinfo;
                                    $merchantekyc->nidmatchauto=$oknid;

                                    $merchantekyc->ocrvarifiedat=$currentdtaetime; 

                                    $merchantekyc->updated_by = $request->user()->id;
                                    $merchantekyc->updated_at=date('Y-m-d H:i:s'); 
                                    $okfine=$merchantekyc->save();

                                if($okfine){


                                    return response()->json(['message'=>'ok'], 200);
                                }else{
                                    return response()->json([
                                        'response' => 'error',
                                        'message' => 'Update Failed'
                                    ], 400);
                                }

                            }else{
                                    return response()->json([
                                        'response' => 'error',
                                        'message' => 'Update Failed'
                                    ], 400);
                                }*/
                            
        }else{
            return response()->json([
                                        'response' => 'error',
                                        'message' => 'Update Failed'
                                    ], 400);
        }
                            
                            
        
    } 

    



     //BIN DATA UPLOAD
    public function documentUpload(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'ocrinfo'=> 'required',
            'nidpic' => 'max:10000000|mimes:png,jpg,jpeg',//max:10000000|mimes:png,jpg,jpeg
        ]);
        $imageName="";
        if($request->hasFile('nidpic')){
                $imageName = time().'.'.$request->nidpic->getClientOriginalExtension();
                $request->nidpic->move(public_path('uploads/outlet/vin'), $imageName);
                $image = Image::make(sprintf('uploads/outlet/vin/%s', $imageName))->orientate()
                    ->resize(null,1000, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save();
        }
        $userid=$request->user()->id;
        $verifiedmobilecode=$request->otpcode;
        $currentdtaetime=date("Y-m-d H:i:s");
        if($imageName){
          

                                    $merchantekyc = Merchantonboarding::where('id',$request->id)->first();
                                    $merchantekyc->businessdocpic = $imageName;
                                    $merchantekyc->businessdococr = $request->ocrinfo;

                                    $merchantekyc->businessdocpicat=$currentdtaetime; 

                                    $merchantekyc->updated_by = $request->user()->id;
                                    $merchantekyc->updated_at=date('Y-m-d H:i:s'); 
                                    $okfine=$merchantekyc->save();

                                if($okfine){


                                    return response()->json(['message'=>'ok'], 200);
                                }else{
                                    return response()->json([
                                        'response' => 'error',
                                        'message' => 'Update Failed'
                                    ], 400);
                                }

                            /*}else{
                                    return response()->json([
                                        'response' => 'error',
                                        'message' => 'Update Failed'
                                    ], 400);
                                }*/
                            
        }
                            
                            
        
    } 
     //Shop Inner Data UPLOAD
    public function shopInnerpic(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'nidpic' => 'max:10000000|mimes:png,jpg,jpeg',//max:10000000|mimes:png,jpg,jpeg
        ]);
        $imageName="";
        if($request->hasFile('nidpic')){
                $imageName = time().'.'.$request->nidpic->getClientOriginalExtension();
                $request->nidpic->move(public_path('uploads/outlet/inner'), $imageName);
                $image = Image::make(sprintf('uploads/outlet/inner/%s', $imageName))->orientate()
                    ->resize(null,1000, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save();
        }
        $userid=$request->user()->id;
        $verifiedmobilecode=$request->otpcode;
        $currentdtaetime=date("Y-m-d H:i:s");
        if($imageName){
          

                                    $merchantekyc = Merchantonboarding::where('id',$request->id)->first();
                                    $merchantekyc->innerimage = $imageName;

                                    $merchantekyc->innerimageat=$currentdtaetime; 

                                    $merchantekyc->updated_by = $request->user()->id;
                                    $merchantekyc->updated_at=date('Y-m-d H:i:s'); 
                                    $okfine=$merchantekyc->save();

                                if($okfine){


                                    return response()->json(['message'=>'ok'], 200);
                                }else{
                                    return response()->json([
                                        'response' => 'error',
                                        'message' => 'Update Failed'
                                    ], 400);
                                }
                            
        }
                            
                            
        
    } 
    //Shop Outer Data UPLOAD
    public function shopOuterpic(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'nidpic' => 'max:10000000|mimes:png,jpg,jpeg',//max:10000000|mimes:png,jpg,jpeg
        ]);
        $imageName="";
        if($request->hasFile('nidpic')){
                $imageName = time().'.'.$request->nidpic->getClientOriginalExtension();
                $request->nidpic->move(public_path('uploads/outlet/outer'), $imageName);
                $image = Image::make(sprintf('uploads/outlet/outer/%s', $imageName))->orientate()
                    ->resize(null,1000, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save();
        }
        $userid=$request->user()->id;
        $verifiedmobilecode=$request->otpcode;
        $currentdtaetime=date("Y-m-d H:i:s");
        if($imageName){
          

                                    $merchantekyc = Merchantonboarding::where('id',$request->id)->first();
                                    $merchantekyc->outerimage = $imageName;

                                    $merchantekyc->outerimageat=$currentdtaetime; 

                                    $merchantekyc->updated_by = $request->user()->id;
                                    $merchantekyc->updated_at=date('Y-m-d H:i:s'); 
                                    $okfine=$merchantekyc->save();

                                if($okfine){


                                    return response()->json(['message'=>'ok'], 200);
                                }else{
                                    return response()->json([
                                        'response' => 'error',
                                        'message' => 'Update Failed'
                                    ], 400);
                                }
                            
        }
                            
                            
        
    } 
    //KYC FORM UPLOAD
    public function kycformsubmitfnl(Request $request)
    {
        $this->validate($request, [
            'id' => 'required', 
            'outletcode' => 'required', 
            'mobileno' => 'required', 
            'name'=> 'required', 
            'father' => 'required',
        ]);




      
            $userid=$request->user()->id;
            $verifiedmobilecode=$request->otpcode;
            $currentdtaetime=date("Y-m-d H:i:s");
            $ekycys = Merchantkycform::where('outletcode',$request->outletcode)->get();

            if(count($ekycys) > 0){
                    $merchantekyc = Merchantkycform::where('outletcode',$request->outletcode)->where('mobileno',$request->mobileno)->first();
                                    $merchantekyc->refdtaid = $request->id;
                                    $merchantekyc->outletcode = $request->outletcode;
                                    $merchantekyc->mobileno = $request->mobileno;
                                    $merchantekyc->distributorcode = $request->distributorcode;
                                    $merchantekyc->name = $request->name;
                                    $merchantekyc->father = $request->father;
                                    $merchantekyc->spouse = $request->spouse;
                                    $merchantekyc->education = $request->education;
                                    $merchantekyc->presentaddr = $request->presentaddr;
                                    $merchantekyc->parmanentaddr = $request->parmanentaddr;
                                    $merchantekyc->alternative = $request->alternative;
                                    $merchantekyc->yrsexprnc = $request->yrsexprnc;
                                    $merchantekyc->monthincome = $request->monthincome;
                                    $merchantekyc->incomeother = $request->incomeother;
                                    $merchantekyc->businesstype = $request->businesstype;
                                    $merchantekyc->tradelicence = $request->tradelicence;
                                    $merchantekyc->tinbin = $request->tinbin;
                                    $merchantekyc->businessproperty = $request->businessproperty;
                                    $merchantekyc->warehouseproperty = $request->warehouseproperty;
                                    $merchantekyc->sisterbusiness = $request->sisterbusiness;
                                    $merchantekyc->businessphone = $request->businessphone;
                                    $merchantekyc->sameexperience = $request->sameexperience;
                                    $merchantekyc->totalexprc = $request->totalexprc;
                                    $merchantekyc->initialexprc = $request->initialexprc;

                                    $merchantekyc->updated_by = $request->user()->id;
                                    $merchantekyc->updated_at=date('Y-m-d H:i:s'); 
                                    $okfine=$merchantekyc->save();

            }else{
                $merchantekyc = new Merchantkycform;
                                    $merchantekyc->refdtaid = $request->id;
                                    $merchantekyc->outletcode = $request->outletcode;
                                    $merchantekyc->mobileno = $request->mobileno;
                                    $merchantekyc->distributorcode = $request->distributorcode;
                                    $merchantekyc->name = $request->name;
                                    $merchantekyc->father = $request->father;
                                    $merchantekyc->spouse = $request->spouse;
                                    $merchantekyc->education = $request->education;
                                    $merchantekyc->presentaddr = $request->presentaddr;
                                    $merchantekyc->parmanentaddr = $request->parmanentaddr;
                                    $merchantekyc->alternative = $request->alternative;
                                    $merchantekyc->yrsexprnc = $request->yrsexprnc;
                                    $merchantekyc->monthincome = $request->monthincome;
                                    $merchantekyc->incomeother = $request->incomeother;
                                    $merchantekyc->businesstype = $request->businesstype;
                                    $merchantekyc->tradelicence = $request->tradelicence;
                                    $merchantekyc->tinbin = $request->tinbin;
                                    $merchantekyc->businessproperty = $request->businessproperty;
                                    $merchantekyc->warehouseproperty = $request->warehouseproperty;
                                    $merchantekyc->sisterbusiness = $request->sisterbusiness;
                                    $merchantekyc->businessphone = $request->businessphone;
                                    $merchantekyc->sameexperience = $request->sameexperience;
                                    $merchantekyc->totalexprc = $request->totalexprc;
                                    $merchantekyc->initialexprc = $request->initialexprc;

                                    $merchantekyc->updated_by = $request->user()->id;
                                    $merchantekyc->updated_at=date('Y-m-d H:i:s'); 
                                    $okfine=$merchantekyc->save();
            }
          

                                    

                                if($okfine){


                                    return response()->json(['message'=>'ok'], 200);
                                }else{
                                    return response()->json([
                                        'response' => 'error',
                                        'message' => 'Update Failed'
                                    ], 400);
                                }
                            
        
                            
                            
        
    } 
    

}
