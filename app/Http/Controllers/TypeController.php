<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Product;
use App\Types;
use App\user;
use DateTime;
use DB;
use Validator;
use File;
use Image;
//use App\Http\Controllers\Image;

class TypeController extends Controller
{   
    public function index()
    {   
        $allexpence = Types::select('id','name')->orderBy('id', 'desc')->paginate(15);
        if(count($allexpence) > 0){
            return response()->json($allexpence, 200);
        }
        return response()->json([
            'response' => 'error',
            'message' => 'No Record Found'
        ], 400);
    }
	
    public function addTypes(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:types',
        ]);
        
        $checkifs = Types::where("name",$request->name)->get();
        if(count($checkifs) <= 0){
           $accout = new Types;

            $accout->name = $request->name;

            $accout->updated_by = $request->user()->id;
            $datasaved=$accout->save(); 
        }
            


            

        return response()->json([
            'message' => 'Menue created successfully'
        ], 200);
    }

	
    public function editfnlType(Request $request)
    {
        $this->validate($request, [
            'id'=> 'required',
            'name' => 'required',
        ]);
        
            $id=$request->id;
            $accout = Types::where("id",$id)->get()->first();

            $accout->name = $request->name;

            $accout->updated_by = $request->user()->id;
            $datasaved=$accout->save();

            $updateproduct=Product::where('typeId',$id)->update(['typeName'=>$request->name]);

           

        return response()->json([
            'message' => 'Menue created successfully'
        ], 200);
    }
	
    public function gettheType($id)
    {
        
        $alldatas = Types::where("id",$id)->get();
        if(count($alldatas) > 0){
            return response()->json($alldatas, 200);
        }
        return response()->json([
            'response' => 'error',
            'message' => 'No Record Found'
        ], 400);
    }

   

}
