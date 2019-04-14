<?php
date_default_timezone_set('Asia/Dhaka');
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
/*.......CURL REQUEST.......*/
Route::get('/nwcurlv1/balancesync',  ['uses'=>'CurlrqstController@balancesync']);
Route::get('/nwcurlv1/repaymentsync',  ['uses'=>'CurlrqstController@repaymentAmntsync']);
    
    
     
Route::group(['middleware' => ['auth:api']], function()
{
	// ------------------user start------------------------
	Route::get('/user', function (Request $request) {
    	return $request->user();
	});
	Route::get('/user/all',  ['uses'=>'UserController@index']);
    Route::get('/user/group/all',  ['uses'=>'UserController@groupall']);
    Route::post('/user/add',['uses'=>'UserController@addUseradd']);

    Route::get('/mya/users/{id}',  ['uses'=>'UserController@getspecifcUsernw']);

    Route::post('/user/edit/final',['uses'=>'UserController@editUseradd']);

    

    Route::get('/user/all/array',['uses'=>'UserController@showallasarray']);
	// -------------------------user end-----------------------


//-------------USER WISE OTP SANCTION----------
    Route::post('/otpadd/marchantadd',['uses'=>'MarchantController@genarateotpMarchantws']);

//------------USER WISE OTP SANCTION DONE------






    // ------------------Customer start------------------------
    Route::get('/customer/all',  ['uses'=>'DistributorController@index']);
    Route::post('/customer/addsdsd',  ['uses'=>'DistributorController@addCustomer']); 
    Route::get('/customer/{id}',  ['uses'=>'DistributorController@gettheCustomer']);
    Route::post('/customer/edit',  ['uses'=>'DistributorController@editfnlCustomer']); 
    Route::get('/compallselectlst',  ['uses'=>'DistributorController@getAllcustomergnrl']);   
    Route::get('/countrylist',  ['uses'=>'DistributorController@countrylist']);   
    // ------------------Company Ends------------------------

    
    // ------------------ Distributor start------------------------
    Route::get('/distributor/company/all',  ['uses'=>'DistributorController@companyIndex']);
    Route::post('/distributor/company/add',  ['uses'=>'DistributorController@addCustomercompany']); 
    Route::get('/distributor/company/{id}',  ['uses'=>'DistributorController@gettheCompany']);
    Route::post('/distributor/company/editss',  ['uses'=>'DistributorController@editCustomercompany']); 
    Route::get('/distributor/all/companygt',  ['uses'=>'DistributorController@newSlctgetall']);

    Route::get('/distributor/all/inchargegt',  ['uses'=>'DistributorController@newInmanagergetall']);
    Route::get('/inchargeof/distributor/{id}',  ['uses'=>'DistributorController@inchargeCompany']);

    
    // ------------------Distributor Ends------------------------

 // ------------------Brand start------------------------
    Route::get('/brand/all',  ['uses'=>'BrandController@index']);
    Route::post('/brand/add',  ['uses'=>'BrandController@addBrand']); 
    Route::get('/brand/{id}',  ['uses'=>'BrandController@gettheBrand']);
    Route::post('/brand/edit',  ['uses'=>'BrandController@editfnlBrand']);  
  // ------------------Brand Ends------------------------   

 // ------------------Category start------------------------
    Route::get('/category/all',  ['uses'=>'CategoryController@index']);
    Route::post('/category/add',  ['uses'=>'CategoryController@addCategory']); 
    Route::get('/category/{id}',  ['uses'=>'CategoryController@gettheCategory']);
    Route::post('/category/edit',  ['uses'=>'CategoryController@editfnlCategory']);  
  // ------------------Category Ends------------------------ 


 // ------------------GroupType start------------------------
    Route::get('/types/all',  ['uses'=>'TypeController@index']);
    Route::post('/types/add',  ['uses'=>'TypeController@addTypes']); 
    Route::get('/types/{id}',  ['uses'=>'TypeController@gettheType']);
    Route::post('/types/edit',  ['uses'=>'TypeController@editfnlType']);  
  // ------------------GroupType Ends------------------------ 


    // ------------------Product start------------------------
    Route::get('/product/prereqst',  ['uses'=>'ProductController@getAllprerequsit']);

    Route::post('/product/add',  ['uses'=>'ProductController@store']); 
    Route::post('/product/edit',  ['uses'=>'ProductController@storeEditnw']); 


    Route::get('/product/all/active/{page}',  ['uses'=>'ProductController@getAllactiveitms']);
    Route::get('/product/getinfo/{id}',  ['uses'=>'ProductController@getDataaspcfinfo']);

    Route::get('/product/search/{nm}',  ['uses'=>'ProductController@searchIteminfo']);
    
    // ------------------Product  Ends------------------------




 // ------------------Marchant start------------------------
    Route::get('/outlet/all',  ['uses'=>'MarchantController@index']);
    Route::post('/outlet/add',  ['uses'=>'MarchantController@store']); 
    Route::get('/merchantnw/{id}',  ['uses'=>'MarchantController@getSpecificmerchantdatas']);
    Route::post('/outlet/edit',  ['uses'=>'MarchantController@editfnlCategory']);  


    Route::get('/merchantsearch',['uses'=>'MarchantController@searchmerchant']);

    
    /***UPDATE CREDIT LINE***/

    Route::post('/outlet/creditlineupdate',  ['uses'=>'MarchantController@fbCreditlineupdate']);  


    Route::get('/transaction/outlet',  ['uses'=>'MarchantController@getOutlettransaction']);
    Route::get('/transactionlastmnth/outlet',  ['uses'=>'MarchantController@getOutlettransactionlastmnth']);


    Route::get('/transactionfromto/outlet',  ['uses'=>'MarchantController@getOutlettransactionfromto']);


  // ------------------Marchant Ends------------------------ 

// ------------------Order All start------------------------
    Route::get('/mainorder/all',  ['uses'=>'UblorderController@getAllorders']);
    Route::get('/mainorder/outlet/{idoutlet}',  ['uses'=>'UblorderController@getOutletwise']);

    Route::post('/mainorder/sendsms',  ['uses'=>'OrdermanageController@manualSmsotpsend']);  


    Route::get('/mainorder/invoice',  ['uses'=>'UblorderController@getOutletinvoice']);


    //REPAY ORDER
    Route::get('/pendingpayment/all',  ['uses'=>'UblorderController@getOutletpaymentpendinginv']);
    //REPAY ORDER INVOICE
    Route::get('/pendingpayment/specific',  ['uses'=>'UblorderController@getOutletpaymentspecific']);

  // ------------------Order All Ends------------------------ 
//-----------------Bank Limit Request Start--------------------
    Route::get('/bankcreditrequest/all',  ['uses'=>'TransactionController@allcreditrequestset']);
    Route::get('/invoiceloanrqst/all',  ['uses'=>'TransactionController@allinvoiceloanrqst']);
    Route::get('/distributorreturn/all',  ['uses'=>'TransactionController@alldistributorreturnrqst']);
    Route::get('/distributorreturn/spcf/{id}',  ['uses'=>'TransactionController@allinvcloanrqstDistributor']);
    Route::get('/distributorreturn/datews/{date}',  ['uses'=>'TransactionController@alldatewisertnDistributor']);



    Route::get('/invoicerepaymentrqst/all',  ['uses'=>'TransactionController@allinvoicerepaymentqst']);


//-----------------Bank Limit Request Ends---------------------
 // ------------------MOBILE LOGIN PROCESS API start------------------------
    //For bps dashboard data
    Route::get('/mbv1/defaultdta',  ['uses'=>'MobileController@getmydashboard']);
    //For outlet dashboard data
    Route::get('/mbv1/outlet/{mobile}',  ['uses'=>'MobileController@getoutletdashboard']);

    //For all product list
    Route::get('/mbv1/productlist',  ['uses'=>'MobileController@getproductlist']);
  // ------------------MOBILE LOGIN PROCESS API  Ends------------------------ 








    // ------------------For Dashboard start------------------------
    Route::get('/admindashboard/all',  ['uses'=>'DashboardController@adminpanelDataall']);
    Route::get('/admindashboard/charts',  ['uses'=>'DashboardController@adminpanelChartprocess']);
    
    // ------------------Dashboard  Ends------------------------




   /*----------------FOR UBL API START---------------*/
   //Outlet Onboarding API
    Route::get('/ublv1/sojsohome',  ['uses'=>'OrdermanageController@myCollectiondtls']); 

   //Outlet Onboarding API
    Route::post('/ublv1/outlet',  ['uses'=>'MarchantController@onboardOutlet']); 
    //Outlet Fairbanc info API
    Route::get('/ublv1/outlet',  ['uses'=>'MarchantController@specificdOutletdata']); 

    //SO/SSO Order taking API
    Route::post('/ublv1/order',  ['uses'=>'OrdermanageController@sossoOrderadd']); 

    //Order taking API 
   // Route::post('/ublv1/order',  ['uses'=>'OrdermanageController@sossoOrderadd']); 


    //Order JSO API 
    // Route::post('/ublv1/order',  ['uses'=>'OrdermanageController@jsoOrderadd']);
    //Order JSO API 
    Route::post('/ublv1/jsoorder/{invoice}',  ['uses'=>'OrdermanageController@jsoOrderedit']);  
    //Order OTP API 
    Route::post('/ublv1/otp/{invoice}',  ['uses'=>'OrdermanageController@jsoOrdernoloan']);  //jsoOrderotp
    //Order OTP API  WITHOUT OTP AND LOAN
    Route::post('/ublv1/noloan/{invoice}',  ['uses'=>'OrdermanageController@jsoOrdernoloan']);  


    //Repayment Order  JSO API 
    Route::post('/ublv1/repayment/{invoice}',  ['uses'=>'UblorderController@repaymentpayOrderedit']);  

    /*----------------FOR UBL API ENDS----------------*/



///BANK EXTRA CHARGED SYNC API
    Route::post('/bankv1/chargesync',  ['uses'=>'CurlrqstController@bankchargeSync']);  
    


//BP DASHBOARD COLLECTED CASH
    Route::get('/bpdash/collectedcash',  ['uses'=>'OrdermanageController@mycollectedCash']); 







// ------------------EKYC Marchant start------------------------
    Route::get('/ekyc/merchant/search',  ['uses'=>'EkycController@searchForoutlet']);
    Route::get('/ekyc/merchant/otprequest',  ['uses'=>'EkycController@otpRequest']);

    //OTP DONE
    Route::post('/ekyc/merchant/otpvarify',  ['uses'=>'EkycController@otpVerify']);
    //NID UPLOAD
    Route::post('/ekyc/merchant/nidupdate',  ['uses'=>'EkycController@niddataUpdate']);
    //FACE UPLOAD UPLOAD
    Route::post('/ekyc/merchant/facedetect',  ['uses'=>'EkycController@facereconize']);


    //Document UPLOAD UPLOAD
    Route::post('/ekyc/merchant/documentup',  ['uses'=>'EkycController@documentUpload']);
    //SHOP INNER UPLOAD UPLOAD
    Route::post('/ekyc/merchant/shopinner',  ['uses'=>'EkycController@shopInnerpic']);


    //SHOP Outer UPLOAD UPLOAD
    Route::post('/ekyc/merchant/shopouter',  ['uses'=>'EkycController@shopOuterpic']);
    
    //SHOP OWNER KYC
    Route::post('/ekyc/merchant/kycformsubmit',  ['uses'=>'EkycController@kycformsubmitfnl']);

  // ------------------EKYC Marchant Ends------------------------ 


 // ------------------Marchant start------------------------
    Route::get('/onboardingoutlet/all',  ['uses'=>'OnboardmarchantController@index']);
    Route::get('/onboardingoutletsearch',['uses'=>'OnboardmarchantController@searchmerchant']);
    Route::get('/onboardingmerchantnw/{id}',  ['uses'=>'OnboardmarchantController@getSpecificmerchantdatas']);


    Route::get('/potraitimganalysis/{id}',  ['uses'=>'OnboardmarchantController@imageAnalysysowner']);

    Route::get('/logoimganalysis/{id}',  ['uses'=>'OnboardmarchantController@logoAnalysysowner']);

    Route::get('/outerlandimganalysis/{id}',  ['uses'=>'OnboardmarchantController@landmarkAnalysysowner']);
   /* Route::post('/outlet/add',  ['uses'=>'MarchantController@store']); 
    Route::get('/merchantnw/{id}',  ['uses'=>'MarchantController@getSpecificmerchantdatas']);
    Route::post('/outlet/edit',  ['uses'=>'MarchantController@editfnlCategory']);  


    


    Route::post('/outlet/creditlineupdate',  ['uses'=>'MarchantController@fbCreditlineupdate']);  


    Route::get('/transaction/outlet',  ['uses'=>'MarchantController@getOutlettransaction']);
    Route::get('/transactionlastmnth/outlet',  ['uses'=>'MarchantController@getOutlettransactionlastmnth']);


    Route::get('/transactionfromto/outlet',  ['uses'=>'MarchantController@getOutlettransactionfromto']);*/


  // ------------------Marchant Ends------------------------ 


    

});