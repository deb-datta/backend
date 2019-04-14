<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Product;
use App\Categories;
use App\user;
use DateTime;
use DB;
use Validator;
use File;
use Image;
//use App\Http\Controllers\Image;

class CategoryController extends Controller
{   
    public function index()
    {   
        $allexpence = Categories::select('id','name')->orderBy('id', 'desc')->paginate(15);
        if(count($allexpence) > 0){
            return response()->json($allexpence, 200);
        }
        return response()->json([
            'response' => 'error',
            'message' => 'No Record Found'
        ], 400);
    }
	
    public function addCategory(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:category',
        ]);
        
        $checkifs = Categories::where("name",$request->name)->get();
        if(count($checkifs) <= 0){
           $accout = new Categories;

            $accout->name = $request->name;

            $accout->updated_by = $request->user()->id;
            $datasaved=$accout->save(); 
        }
            


            

        return response()->json([
            'message' => 'Menue created successfully'
        ], 200);
    }

	
    public function editfnlCategory(Request $request)
    {
        $this->validate($request, [
            'id'=> 'required',
            'name' => 'required',
        ]);
            
            $id=$request->id;

            $accout = Categories::where("id",$id)->get()->first();

            $accout->name = $request->name;

            $accout->updated_by = $request->user()->id;
            $datasaved=$accout->save();
            
            $updateproduct=Product::where('categoryId',$id)->update(['categoryName'=>$request->name]);

           

        return response()->json([
            'message' => 'Menue created successfully'
        ], 200);
    }
	
    public function gettheCategory($id)
    {
        
        $alldatas = Categories::where("id",$id)->get();
        if(count($alldatas) > 0){
            return response()->json($alldatas, 200);
        }
        return response()->json([
            'response' => 'error',
            'message' => 'No Record Found'
        ], 400);
    }

   

}
