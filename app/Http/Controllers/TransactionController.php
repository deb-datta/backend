<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Marchantlist;
use APP\User;
use App\Otpfailedoverned;
use App\Creditlimitlogs;
use App\Loantracking;
use App\Repaymentcollection;
use DateTime;
use DB;
use Validator;
use File;
use Image;
//use App\Http\Controllers\Image;

class TransactionController extends Controller
{   

    
    public function allcreditrequestset(Request $request)
    {
        $getoutlets = Creditlimitlogs::select()->leftJoin('marchantlist', 'creditlimitlogs.setformerchanid', '=', 'marchantlist.id')->where('banksink','!=','3')->paginate(15);

            if(count($getoutlets) > 0){
                return response()->json($getoutlets, 200);
            }
        return response()->json([
            'response' => 'error',
            'message' => 'No Record Found'
        ], 400);
    } 
    
    public function allinvoiceloanrqst(Request $request)
    {
        $getoutlets = Loantracking::select(['marchantlist.outletName','marchantlist.mobileno','marchantlist.address','loantracking.invoiceid','loantracking.outletcode','loantracking.loanamount','loantracking.totalbill'
            ,'loantracking.startingdate','loantracking.adjustedamount','loantracking.lastadjustmentdate','loantracking.banksink'])->leftJoin('marchantlist', 'loantracking.outletid', '=', 'marchantlist.id')->where('loantracking.banksink','!=','3')->paginate(15);

            if(count($getoutlets) > 0){
                return response()->json($getoutlets, 200);
            }
        return response()->json([
            'response' => 'error',
            'message' => 'No Record Found'
        ], 400);
    } 
    
    public function allinvoicerepaymentqst(Request $request)
    {
        $getoutlets = Repaymentcollection::select(['marchantlist.outletName','marchantlist.mobileno','marchantlist.address','repaymentcollection.invoiceid','repaymentcollection.outletcode','repaymentcollection.loanamount','repaymentcollection.totalbill'
            ,'repaymentcollection.startingdate','repaymentcollection.collectedamount','repaymentcollection.moneyreceptno','repaymentcollection.updated_at','repaymentcollection.banksink','repaymentcollection.bankloantransactionid'])->leftJoin('marchantlist', 'repaymentcollection.outletid', '=', 'marchantlist.id')->where('repaymentcollection.banksink','!=','4')->paginate(15);

            if(count($getoutlets) > 0){
                return response()->json($getoutlets, 200);
            }
        return response()->json([
            'response' => 'error',
            'message' => 'No Record Found'
        ], 400);
    } 
    public function alldistributorreturnrqst(Request $request)
    {

        $getoutlets = Loantracking::select(['distributer.name','distributer.compdistributorid','distributer.address','loantracking.distributorid',DB::raw('count(loantracking.id) as numberofinvoice, SUM(loantracking.loanamount) AS totalloan,SUM(loantracking.totalbill) AS totalbill,SUM(loantracking.adjustedamount) AS totaladjustment')])->leftJoin('distributer', 'loantracking.distributorid', '=', 'distributer.id')->where('loantracking.banksink','!=','3')->groupBy('loantracking.distributorid')->paginate(15);

            if(count($getoutlets) > 0){
                return response()->json($getoutlets, 200);
            }
        return response()->json([
            'response' => 'error',
            'message' => 'No Record Found'
        ], 400);
    } 


    public function allinvcloanrqstDistributor(Request $request,$id)
    {
        $getoutlets = Loantracking::select(['marchantlist.outletName','marchantlist.mobileno','marchantlist.address','loantracking.invoiceid','loantracking.outletcode','loantracking.loanamount','loantracking.totalbill'
            ,'loantracking.startingdate','loantracking.adjustedamount','loantracking.lastadjustmentdate','loantracking.banksink'])->leftJoin('marchantlist', 'loantracking.outletid', '=', 'marchantlist.id')->where('loantracking.banksink','!=','3')->where('loantracking.distributorid',$id)->get();

            if(count($getoutlets) > 0){
                return response()->json($getoutlets, 200);
            }
        return response()->json([
            'response' => 'error',
            'message' => 'No Record Found'
        ], 400);
    } 
    public function alldatewisertnDistributor(Request $request,$date)
    {
        $getoutlets = Loantracking::select(['marchantlist.outletName','marchantlist.mobileno','marchantlist.address','loantracking.invoiceid','loantracking.outletcode','loantracking.loanamount','loantracking.totalbill'
            ,'loantracking.startingdate','loantracking.adjustedamount','loantracking.lastadjustmentdate','loantracking.banksink'])->leftJoin('marchantlist', 'loantracking.outletid', '=', 'marchantlist.id')->where('loantracking.banksink','!=','3')->where('loantracking.distributorid',$id)->get();

            if(count($getoutlets) > 0){
                return response()->json($getoutlets, 200);
            }
        return response()->json([
            'response' => 'error',
            'message' => 'No Record Found'
        ], 400);
    } 


}
