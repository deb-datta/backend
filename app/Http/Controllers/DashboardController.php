<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use APP\User;
use App\Otpfailedoverned;
use App\Creditlimitlogs;
use App\Distributer;
use App\Dmsemployee;
use App\Marchantlist;
use App\Ordercollection;
use App\Loantracking;
use App\Repaymentcollection;
use DateTime;
use DB;
use Validator;
use File;
use Image;
//use App\Http\Controllers\Image;

class DashboardController extends Controller
{   

    
    public function adminpanelDataall(Request $request)
    {
        $totaldistributor = Distributer::where('activestate','1')->count();
        $totalemployeedis = Dmsemployee::where('activestate','1')->count();
        $totalmicromerchant = Marchantlist::select(DB::raw('count(*) as allmarchant, SUM(fbcreditlinebm) AS totalcrdt,SUM(currentavailable) AS totalavailable'))->where('activestate','1')->get();

        $currentorderbase = Ordercollection::select(DB::raw('count(*) as allorder, SUM(totalamount) AS totalorderprice,SUM(dueamount) AS totallockprice'))->where('activestate','1')->get();



        $initialcreditlock = Ordercollection::where('activestate','1')->where('okupdate','<','1')->where('currentstate','<','1')->sum('dueamount');
        $bfrotpverifycreditlock = Ordercollection::where('activestate','1')->where('okupdate','<','1')->where('verifiedmobilecode','!=','')->where('currentstate','>=','1')->sum('dueamount');
        $finalcreditlock = Ordercollection::where('activestate','1')->where('okupdate','>=','1')->sum('dueamount');



        $completevspendingtrns = Loantracking::select(DB::raw('count(*) as nooforder, SUM(loanamount) AS totalloan,banksink,bankpaidtocompany'))->whereDate('created_at', '=', date('Y-m-d'))->groupBy('bankpaidtocompany')->get();

        $repaymentreqst = Repaymentcollection::select(DB::raw('count(*) as nooforder, SUM(collectedamount) AS collectedamount,banksink'))->whereDate('created_at', '=', date('Y-m-d'))->groupBy('banksink')->get();

            if(count($totalmicromerchant) > 0){
                return response()->json([
                    'totaldistributor'=>$totaldistributor,
                    'totalemployee'=>$totalemployeedis,
                    'totalmicromerchant'=>$totalmicromerchant->first(),
                    'totalorders'=>$currentorderbase->first(),

                    'initialcreditlock'=>$initialcreditlock,
                    'bfrotpcreditlock'=>$bfrotpverifycreditlock,
                    'finalcreditlock'=>$finalcreditlock,

                    'banktransfers'=>$completevspendingtrns,
                    'bankrepayment'=>$repaymentreqst,
                ], 200);
            }
        return response()->json([
            'response' => 'error',
            'message' => 'No Record Found'
        ], 400);
    } 
    
   public function adminpanelChartprocess(Request $request)
    {
        $sevendaysbfr=date("Y-m-d", strtotime("-7 days"));
       // print_r($sevendaysbfr);die();
       /* $allorderchart = Ordercollection::select(DB::raw('SUM(totalamount) AS totalorder,SUM(dueamount) AS totalloan,finalorderdeliverydt'),DB::raw('DATE(finalorderdeliverydt) AS daten'))->where('activestate','1')->where('okupdate','>=','1')->whereDate('finalorderdeliverydt','>=', $sevendaysbfr)->groupBy('daten')->orderBy('finalorderdeliverydt')->get();

*/
       $allorderchart =DB::select( DB::raw("SELECT SUM(a.totalamount) AS totalorder,SUM(a.dueamount) AS totalloan,a.finalorderdeliverydt,DATE(a.finalorderdeliverydt) AS datadts FROM ordercollection AS a WHERE  a.okupdate>=1 AND DATE(a.finalorderdeliverydt)>='$sevendaysbfr' GROUP BY DATE(a.finalorderdeliverydt) ORDER BY a.id DESC  ")); // a.activestate='1' AND

       $allrepaymentchart =DB::select( DB::raw("SELECT SUM(a.collectedamount) AS totalorder,DATE(a.created_at) AS datadts FROM repaymentcollection AS a WHERE   DATE(a.created_at)>='$sevendaysbfr' GROUP BY DATE(a.created_at) ORDER BY a.id DESC  "));

            if(count($allorderchart) > 0){
                return response()->json(['orders'=>$allorderchart,'repayment'=>$allrepaymentchart], 200);
            }
        return response()->json([
            'response' => 'error',
            'message' => 'No Record Found'
        ], 400);
    } 
    

}
