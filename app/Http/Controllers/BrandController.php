<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Product;
use App\Brand;
use App\user;
use DateTime;
use DB;
use Validator;
use File;
use Image;
//use App\Http\Controllers\Image;

class BrandController extends Controller
{   
    public function index()
    {   
        $allexpence = Brand::select('id','name')->orderBy('id', 'desc')->paginate(15);
        if(count($allexpence) > 0){
            return response()->json($allexpence, 200);
        }
        return response()->json([
            'response' => 'error',
            'message' => 'No Record Found'
        ], 400);
    }
	
    public function addBrand(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:brands',
        ]);
        
        
            $accout = new Brand;

            $accout->name = $request->name;

            $accout->updated_by = $request->user()->id;
            $datasaved=$accout->save();


            

        return response()->json([
            'message' => 'Menue created successfully'
        ], 200);
    }

	
    public function editfnlBrand(Request $request)
    {
        $this->validate($request, [
            'id'=> 'required',
            'name' => 'required',
        ]);
        
            $id=$request->id;
            $accout = Brand::where("id",$id)->get()->first();

            $accout->name = $request->name;

            $accout->updated_by = $request->user()->id;
            $datasaved=$accout->save();

            $updateproduct=Product::where('brandid',$id)->update(['brandname'=>$request->name]);
           

        return response()->json([
            'message' => 'Menue created successfully'
        ], 200);
    }
	
    public function gettheBrand($id)
    {
        
        $alldatas = Brand::where("id",$id)->get();
        if(count($alldatas) > 0){
            return response()->json($alldatas, 200);
        }
        return response()->json([
            'response' => 'error',
            'message' => 'No Record Found'
        ], 400);
    }

   

}
