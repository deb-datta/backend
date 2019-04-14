<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Categories;
use App\Types;
use App\Brand;
use DateTime;
use DB;
use Validator;
use File;
use Image;
//use App\Http\Controllers\Image;

class ProductController extends Controller
{   
    public function getAllactiveitms(Request $request,$page)
    {
        $offset=($page-1)*8;
            $sumallls= DB::select( DB::raw("SELECT COUNT(a.id) as `totals` FROM productlist as a WHERE  a.active!='0' "));
            $allproducts= DB::select( DB::raw("SELECT a.id,a.skuid,a.name,a.categoryId,a.categoryName,a.typeId,a.typeName,a.brandid,a.brandname,a.unite,a.uniteprice,a.pictureas,a.active FROM productlist AS a WHERE  a.active!='0' ORDER BY a.id DESC LIMIT 8 OFFSET ".$offset.""));   
        if(count($allproducts) > 0){
            return response()->json([
                        'maindata' => $allproducts,
                        'alls' => $sumallls[0]->totals,
                        'pagenow' => ($page)
                        ], 200);
        }
        return response()->json([
            'response' => 'error',
            'message' => 'No Record Found'
        ], 400);
    }   
    public function searchIteminfo(Request $request,$nm)
    {
        if($nm){
            $getitemms = Itemmain::where("itemno",$nm)->get(['id','itemno']);
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


    public function getAllprerequsit()
    {
        
        $alltypes = Types::get(['id','name']);
        $allbrands = Brand::get(['id','name']);
        $alldatascatgr = Categories::get(['id','name']);
        if((count($allbrands) > 0) && (count($alldatascatgr) > 0)){
            return response()->json(['brands'=>$allbrands,'categories'=>$alldatascatgr,'types'=>$alltypes], 200);
        }
        return response()->json([
            'response' => 'error',
            'message' => 'No Record Found'
        ], 400);
    }
    
    public function store(Request $request)
    {
        $this->validate($request, [
            'skuid' => 'required|unique:productlist',
            'name'=> 'required',
            'categoryId'=> 'required',
            'typeId'=> 'required',
            'brandid'=> 'required',
            'unite'=> 'required',
            'uniteprice'=> 'required',
            'logo' => 'max:10000|mimes:png,jpg,jpeg',
        ]);
        $imageName="";
        if($request->hasFile('logo')){
                $imageName = time().'.'.$request->logo->getClientOriginalExtension();
                $request->logo->move(public_path('uploads/product'), $imageName);
                $image = Image::make(sprintf('uploads/product/%s', $imageName))
                    ->resize(null,300, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save();
        }
       /* $filedocName="";
        if($request->hasFile('docpdf')){
                $filedocName = time().'.'.$request->docpdf->getClientOriginalExtension();
                $request->docpdf->move(public_path('uploads/orderitemdtls'), $filedocName);
               
        }*/
            $category=Categories::where('id',$request->categoryId)->get(['name'])->first();
            $typen=Types::where('id',$request->typeId)->get(['name'])->first();
            $brandn=Brand::where('id',$request->brandid)->get(['name'])->first();
            $typestr = new Product;

            $typestr->skuid = $request->skuid;
            $typestr->name = $request->name;  
            $typestr->categoryId = $request->categoryId;
            $typestr->categoryName = $category->name;  
            $typestr->typeId = $request->typeId; 
            $typestr->typeName = $typen->name;  
            $typestr->brandid = $request->brandid;
            $typestr->brandname = $brandn->name;  
            $typestr->unite = $request->unite;     
            $typestr->uniteprice = $request->uniteprice;
            $typestr->pictureas = $imageName;
            
            $typestr->updated_by = $request->user()->id;
            $typestr->save();
        

        return response()->json([
            'message' => 'Product created successfully'
        ], 200);
    }    
    
    public function storeEditnw(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'orderno' => 'required',
            'orderqty' => 'required',
            'brandonly'=> 'required',
            'brandcutomerid' => 'required',
            'ordate' => 'required',
        ]);
        $imageName="";
        if($request->hasFile('logo')){
            if($request->logo){
                $imageName = time().'.'.$request->logo->getClientOriginalExtension();
                $request->logo->move(public_path('uploads/orderitem'), $imageName);
                $image = Image::make(sprintf('uploads/orderitem/%s', $imageName))
                    ->resize(null,300, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save();
            }
        }
       /* $filedocName="";
        if($request->hasFile('docpdf')){
                $filedocName = time().'.'.$request->docpdf->getClientOriginalExtension();
                $request->docpdf->move(public_path('uploads/orderitemdtls'), $filedocName);
               
        }*/
            $id=$request->id;
            $typestr = Itemmain::where("id",$id)->get()->first();

            $typestr->orderno = $request->orderno;
            $typestr->pino = $request->pino;
            if($imageName){
                $typestr->picksall = $imageName;
            }
            
            //$typestr->docpdffileatt = $filedocName;

            

            $typestr->details = $request->details;
            $typestr->orderqty = $request->orderqty;
            $typestr->unitepricer= $request->uniteprc;           
            $typestr->brandid = $request->brandonly;
            $typestr->brandcutomerid = $request->brandcutomerid;
            $typestr->incharge = $request->incharge;
            $typestr->packingtype = $request->packingtype;
            $typestr->productionline = $request->productionline;
            $typestr->shipmenttype = $request->shipmenttype;
            $typestr->orderdate = ($this->datechecker($request->ordate));
            $typestr->customerexptdate = ($this->datechecker($request->cedate));
            $typestr->ourexptdate = ($this->datechecker($request->eddate));
            $typestr->updated_by = $request->user()->id;
            $typestr->save();
        

        return response()->json([
            'message' => 'Menue created successfully'
        ], 200);
    } 
    
    

}
