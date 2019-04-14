<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Ordercollection;
use DB;
use Validator;
use DateTime;
use File;
use Image;
//use App\Http\Controllers\Image;

class MainorderController extends Controller
{   
   

    public function getAllorders(Request $request,$id)
    {
        if($request->user()->group_id!=5){
            $orderdtls=Ordercollection::where('okupdate','!=','1')->get();
            //$customers=Company::get(['id','name']);
            //$brandsall=Brand::get(['id','name']);
            if(count($customers) > 0){
                return response()->json([
                'allorder' => $orderdtls,
            ], 200);
            }
        }
        return response()->json([
            'response' => 'error',
            'message' => 'No Record Found'
        ], 400);
    }

}