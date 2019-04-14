<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Product;
use App\Brand;
use App\Loginfoall;
use App\user;
use App\Ordercollection;
use App\Dailycollection;
use App\Marchantlist;
use DateTime;
use DB;
use Validator;
use File;
use Image;
//use App\Http\Controllers\Image;

class MobileController extends Controller
{   
    public function getmydashboard(Request $request)
    {   

        if($request->user()->group_id>=5){
            $myid=$request->user()->id;
            $distributorid=$request->user()->distributerid;

            $mymobileno=$request->user()->mobileno;
            $myname=$request->user()->name;

           $logdatain= new Loginfoall;
           $logdatain->userid=$myid;
           $logdatain->logfor='Login';
           $logdatain->logdetails="Login occurred from mobile by ".$mymobileno;
           $logdatain->updated_by=$myid;
           $logdatain->save();
           $orderno=0;$collection=0;
           $currentdate=date("Y-m-d"); 
           $orderno = Ordercollection::where("sossoid",$myid)->where("orderdate",$currentdate)->get(['id'])->count();
           $collection = Dailycollection::where("collectedby",$myid)->whereDate("created_at",'=',$currentdate)->get(['id'])->count();
           if($myname){
                return response()->json([
                    'response' => 'ok',
                    'name'=>$myname,
                    'mobile'=>$mymobileno,
                    'distributor'=>$distributorid,
                    'totalorder'=>$orderno,
                    'totaltk'=>$collection,
                ], 200);
            }else{
                return response()->json([
                    'response' => 'error',
                    'message' => 'No Record Found'
                ], 400);
            }
       }else{
             return response()->json([
                'response' => 'error',
                'message' => 'You are not authorize here'
            ], 400);
        }
	
    }
    
   //Outlet Mobile Dashboard Data
    public function getoutletdashboard(Request $request,$mobile)
    {   

       if($request->user()->group_id>=5){
           $myid=$request->user()->id;
           $currentdate=date("Y-m-d"); 
           $outletdata = Marchantlist::where("mobileno",$mobile)->where("currentstate","!=",'20')->get(['id','outletName','outletcode','mobileno','address','fbcreditlinebm','currentavailable']);
            if(count($outletdata)>0){
                $finalsenddata=$outletdata[0];
                return response()->json([
                    'response' => 'ok',
                    'outletdata'=>$finalsenddata,
                    'dates'=>$currentdate,
                ], 200);
            }
            return response()->json([
                'response' => 'error',
                'message' => 'No Record Found'
            ], 400);

      }else{
             return response()->json([
                'response' => 'error',
                'message' => 'You are not authorize here'
            ], 400);
        }
    
    }
    //All product List
    public function getproductlist(Request $request)
    {   

        if($request->user()->group_id>=5){
           $myid=$request->user()->id;
           $currentdate=date("Y-m-d"); 
           $productdata = Product::where("active",1)->get(['id','skuid','name','categoryName','typeName','brandname','unite','uniteprice','pictureas']);
            if(count($productdata)>0){
                return response()->json([
                    'response' => 'ok',
                    'productdata'=>$productdata,
                    'date'=>$currentdate,
                ], 200);
            }
            return response()->json([
                'response' => 'error',
                'message' => 'No Record Found'
            ], 400);

       }else{
             return response()->json([
                'response' => 'error',
                'message' => 'You are not authorize here'
            ], 400);
        }
    
    }

}
