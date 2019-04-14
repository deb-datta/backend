<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Menues;
use App\Dmsemployee;
use App\user;
use App\Distributer;
use App\Raffregister;
use DateTime;
use DB;
use Validator;
use File;
use Image;
//use App\Http\Controllers\Image;

class DistributorController extends Controller
{   
    public function index()
    {   
        $allexpence = Dmsemployee::select('id','name','mobileno','email','distributername','distributerid','emptype','username','password')->orderBy('id', 'desc')->paginate(15);
        if(count($allexpence) > 0){
            return response()->json($allexpence, 200);
        }
        return response()->json([
            'response' => 'error',
            'message' => 'No Record Found'
        ], 400);
    }
    public function newSlctgetall(Request $request)
    {   
        if($request->user()->group_id==5){
            $clientids=$request->user()->distributerid;
            
            $compusername=$request->user()->name;
            $allexpence = Distributer::where('id',$clientids)->get(['id','name']);
        }else if($request->user()->group_id==4){
            $clientids=$request->user()->distributerid;
            
            $compusername=$request->user()->name;
            $allexpence = Distributer::where('id',$clientids)->get(['id','name']);
        }else{
            $allexpence = Distributer::get(['id','name']);
        }
        if(count($allexpence) > 0){
            return response()->json($allexpence, 200);
        }

        return response()->json([
            'response' => 'error',
            'message' => 'No Record Found'
        ], 400);
    }
    public function newInmanagergetall(Request $request)
    {   
        if($request->user()->group_id==5){
            $clientids=$request->user()->distributerid;
            
            $compusername=$request->user()->name;
            $allexpence = Dmsemployee::where('distributerid',$clientids)->get(['id','name']);
        }else if($request->user()->group_id==4){
            $clientids=$request->user()->distributerid;
            
            $compusername=$request->user()->name;
            $allexpence = Dmsemployee::where('distributerid',$clientids)->get(['id','name']);
        }else{
            $allexpence = Dmsemployee::get(['id','name']);
        }
        if(count($allexpence) > 0){
            return response()->json($allexpence, 200);
        }

        return response()->json([
            'response' => 'error',
            'message' => 'No Record Found'
        ], 400);
    }
	public function companyIndex()
    {   
        $allexpence = Distributer::select('id','name','address','email','contactno')->orderBy('id', 'desc')->paginate(15);
        if(count($allexpence) > 0){
            return response()->json($allexpence, 200);
        }
        return response()->json([
            'response' => 'error',
            'message' => 'No Record Found'
        ], 400);
    }    
    public function addCustomercompany(Request $request)
    {
        $this->validate($request, [            
            'name' => 'required|unique:distributer',
            'email' => 'required',
            'contactno' => 'required',
            'ublcode'=> 'required',
        ]);
        
        
            $accout = new Distributer;

            $accout->name = $request->name;
            $accout->email = $request->email;
            $accout->address = $request->address;            
            $accout->contactno = $request->contactno;
            $accout->compdistributorid =$request->ublcode;

            $accout->updated_by = $request->user()->id;
            $datasaved=$accout->save();

        return response()->json([
            'message' => 'Menue created successfully'
        ], 200);
    }

    public function editCustomercompany(Request $request)
    {
        $this->validate($request, [            
            'id'=> 'required',
            'name' => 'required',
            'email' => 'required',
            'contactno' => 'required',
        ]);       
        $id=$request->id;
            $accout = Distributer::where("id",$id)->get()->first();
            $accout->name = $request->name;
            $accout->email = $request->email;
            $accout->address = $request->address;           
            $accout->contactno = $request->contactno;

            $accout->updated_by = $request->user()->id;
            $datasaved=$accout->save();

        return response()->json([
            'message' => 'Menue created successfully'
        ], 200);
    }

    public function gettheCompany($id)
    {
        
        $alldatas = Distributer::where("id",$id)->get();
        if(count($alldatas) > 0){
            return response()->json($alldatas, 200);
        }
        return response()->json([
            'response' => 'error',
            'message' => 'No Record Found'
        ], 400);
    }
    public function addCustomer(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'mobileno' => 'required|unique:users',
            'password' => 'required',
            'username' => 'required',
            'distributerid'=>'required',
        ]);
        $compida=$request->distributerid;
        $getdatassdsd=Distributer::where("id",$compida)->get()->first();
        if($getdatassdsd){
            $companyid=$request->distributerid;
            $accout = new Dmsemployee;

            $accout->name = $request->name;
            $accout->mobileno = $request->mobileno;
            $accout->email = $request->email;
            $accout->distributerid = $request->distributerid;
            $accout->distributername= $getdatassdsd->name;
            $accout->username = $request->username;
            $accout->password = $request->password;
            $accout->emptype= $request->employeetype;

            $accout->updated_by = $request->user()->id;
            $datasaved=$accout->save();


            if($datasaved){
                $getidnooa =Dmsemployee::where('name',$request->name)->where('mobileno',$request->mobileno)->first();
                if($getidnooa ){
                  $mainorder = new User;
                    $mainorder->name = $request->name;
                    $mainorder->username = $request->username;
                    $mainorder->group_id = 5;
                    $mainorder->email = $request->email;
                    $mainorder->mobileno= $request->mobileno;
                    $mainorder->password = Hash::make($request->password);
                    $mainorder->clientid=$getidnooa->id;
                    $mainorder->distributerid=$companyid;
                    $mainorder->active = '1';
                    $mainorder->updated_by = $request->user()->id;
                    $mainorder->save();  
                }
                


            }
        }
        return response()->json([
            'message' => 'Menue created successfully'
        ], 200);
    }

	
    public function editfnlCustomer(Request $request)
    {
        $this->validate($request, [
            'id'=> 'required',
            'name' => 'required',
            'password' => 'required',
            'distributerid'=>'required',
        ]);
        $compida=$request->companyid;
        $getdatassdsd=Distributer::where("id",$compida)->get()->first();
        if($getdatassdsd){
            $id=$request->id;
            $companyid=$request->companyid;
            $accout = Dmsemployee::where("id",$id)->get()->first();
            //$accout->name = $request->name;
            $accout->name = $request->name;
                $accout->username = $request->username;
                $accout->group_id = 5;
                $accout->email = $request->email;
                $accout->password = Hash::make($request->password);
                $accout->clientid=$getidnooa->id;
                $accout->distributerid=$companyid;
                $accout->active = '1';
                $accout->updated_by = $request->user()->id;

            $accout->updated_by = $request->user()->id;
            $datasaved=$accout->save();


            if($datasaved){
                //$getidnooa =Customer::where('name',$request->name)->where('mobileno',$request->mobile)->first();

                $mainorder = User::where("clientid",$id)->get()->first();
               // $mainorder->name = $request->name;
                $mainorder->group_id = 5;
                $mainorder->email = $request->email;                
                $mainorder->distributerid=$companyid;
                $mainorder->password = Hash::make($request->password);
                $mainorder->active = '1';
                $mainorder->updated_by = $request->user()->id;
                $mainorder->save();
            }
        }
        return response()->json([
            'message' => 'Menue created successfully'
        ], 200);
    }
	
    public function gettheCustomer($id)
    {
        
        $alldatas = Dmsemployee::where("id",$id)->get();
        if(count($alldatas) > 0){
            return response()->json($alldatas, 200);
        }
        return response()->json([
            'response' => 'error',
            'message' => 'No Record Found'
        ], 400);
    }

    public function getAllcustomergnrl()
    {
        
        $alldatas = Distributer::get(['id','name']);
        if(count($alldatas) > 0){
            return response()->json($alldatas, 200);
        }
        return response()->json([
            'response' => 'error',
            'message' => 'No Record Found'
        ], 400);
    }
    public function inchargeCompany($id){
        $alldatas = Dmsemployee::where('distributerid',$id)->get(['id','name']);
        if(count($alldatas) > 0){
            return response()->json($alldatas, 200);
        }
        return response()->json([
            'response' => 'error',
            'message' => 'No Record Found'
        ], 400);
    }
    public function countrylist(){
        $datajson='[{"name":"Afghanistan","code":"AF"},{"name":"Åland Islands","code":"AX"},{"name":"Albania","code":"AL"},{"name":"Algeria","code":"DZ"},{"name":"American Samoa","code":"AS"},{"name":"AndorrA","code":"AD"},{"name":"Angola","code":"AO"},{"name":"Anguilla","code":"AI"},{"name":"Antarctica","code":"AQ"},{"name":"Antigua and Barbuda","code":"AG"},{"name":"Argentina","code":"AR"},{"name":"Armenia","code":"AM"},{"name":"Aruba","code":"AW"},{"name":"Australia","code":"AU"},{"name":"Austria","code":"AT"},{"name":"Azerbaijan","code":"AZ"},{"name":"Bahamas","code":"BS"},{"name":"Bahrain","code":"BH"},{"name":"Bangladesh","code":"BD"},{"name":"Barbados","code":"BB"},{"name":"Belarus","code":"BY"},{"name":"Belgium","code":"BE"},{"name":"Belize","code":"BZ"},{"name":"Benin","code":"BJ"},{"name":"Bermuda","code":"BM"},{"name":"Bhutan","code":"BT"},{"name":"Bolivia","code":"BO"},{"name":"Bosnia and Herzegovina","code":"BA"},{"name":"Botswana","code":"BW"},{"name":"Bouvet Island","code":"BV"},{"name":"Brazil","code":"BR"},{"name":"British Indian Ocean Territory","code":"IO"},{"name":"Brunei Darussalam","code":"BN"},{"name":"Bulgaria","code":"BG"},{"name":"Burkina Faso","code":"BF"},{"name":"Burundi","code":"BI"},{"name":"Cambodia","code":"KH"},{"name":"Cameroon","code":"CM"},{"name":"Canada","code":"CA"},{"name":"Cape Verde","code":"CV"},{"name":"Cayman Islands","code":"KY"},{"name":"Central African Republic","code":"CF"},{"name":"Chad","code":"TD"},{"name":"Chile","code":"CL"},{"name":"China","code":"CN"},{"name":"Christmas Island","code":"CX"},{"name":"Cocos (Keeling) Islands","code":"CC"},{"name":"Colombia","code":"CO"},{"name":"Comoros","code":"KM"},{"name":"Congo","code":"CG"},{"name":"Congo,The Democratic Republic of the","code":"CD"},{"name":"Cook Islands","code":"CK"},{"name":"Costa Rica","code":"CR"},{"name":"Cote D Ivoire","code":"CI"},{"name":"Croatia","code":"HR"},{"name":"Cuba","code":"CU"},{"name":"Cyprus","code":"CY"},{"name":"Czech Republic","code":"CZ"},{"name":"Denmark","code":"DK"},{"name":"Djibouti","code":"DJ"},{"name":"Dominica","code":"DM"},{"name":"Dominican Republic","code":"DO"},{"name":"Ecuador","code":"EC"},{"name":"Egypt","code":"EG"},{"name":"El Salvador","code":"SV"},{"name":"Equatorial Guinea","code":"GQ"},{"name":"Eritrea","code":"ER"},{"name":"Estonia","code":"EE"},{"name":"Ethiopia","code":"ET"},{"name":"Falkland Islands (Malvinas)","code":"FK"},{"name":"Faroe Islands","code":"FO"},{"name":"Fiji","code":"FJ"},{"name":"Finland","code":"FI"},{"name":"France","code":"FR"},{"name":"French Guiana","code":"GF"},{"name":"French Polynesia","code":"PF"},{"name":"French Southern Territories","code":"TF"},{"name":"Gabon","code":"GA"},{"name":"Gambia","code":"GM"},{"name":"Georgia","code":"GE"},{"name":"Germany","code":"DE"},{"name":"Ghana","code":"GH"},{"name":"Gibraltar","code":"GI"},{"name":"Greece","code":"GR"},{"name":"Greenland","code":"GL"},{"name":"Grenada","code":"GD"},{"name":"Guadeloupe","code":"GP"},{"name":"Guam","code":"GU"},{"name":"Guatemala","code":"GT"},{"name":"Guernsey","code":"GG"},{"name":"Guinea","code":"GN"},{"name":"Guinea-Bissau","code":"GW"},{"name":"Guyana","code":"GY"},{"name":"Haiti","code":"HT"},{"name":"Heard Island and Mcdonald Islands","code":"HM"},{"name":"Holy See (Vatican City State)","code":"VA"},{"name":"Honduras","code":"HN"},{"name":"Hong Kong","code":"HK"},{"name":"Hungary","code":"HU"},{"name":"Iceland","code":"IS"},{"name":"India","code":"IN"},{"name":"Indonesia","code":"ID"},{"name":"Iran, Islamic Republic Of","code":"IR"},{"name":"Iraq","code":"IQ"},{"name":"Ireland","code":"IE"},{"name":"Isle of Man","code":"IM"},{"name":"Israel","code":"IL"},{"name":"Italy","code":"IT"},{"name":"Jamaica","code":"JM"},{"name":"Japan","code":"JP"},{"name":"Jersey","code":"JE"},{"name":"Jordan","code":"JO"},{"name":"Kazakhstan","code":"KZ"},{"name":"Kenya","code":"KE"},{"name":"Kiribati","code":"KI"},{"name":"Korea, Democratic Peoples Republic of","code":"KP"},{"name":"Korea, Republic of","code":"KR"},{"name":"Kuwait","code":"KW"},{"name":"Kyrgyzstan","code":"KG"},{"name":"Lao Peoples Democratic Republic","code":"LA"},{"name":"Latvia","code":"LV"},{"name":"Lebanon","code":"LB"},{"name":"Lesotho","code":"LS"},{"name":"Liberia","code":"LR"},{"name":"Libyan Arab Jamahiriya","code":"LY"},{"name":"Liechtenstein","code":"LI"},{"name":"Lithuania","code":"LT"},{"name":"Luxembourg","code":"LU"},{"name":"Macao","code":"MO"},{"name":"Macedonia, The Former Yugoslav Republic of","code":"MK"},{"name":"Madagascar","code":"MG"},{"name":"Malawi","code":"MW"},{"name":"Malaysia","code":"MY"},{"name":"Maldives","code":"MV"},{"name":"Mali","code":"ML"},{"name":"Malta","code":"MT"},{"name":"Marshall Islands","code":"MH"},{"name":"Martinique","code":"MQ"},{"name":"Mauritania","code":"MR"},{"name":"Mauritius","code":"MU"},{"name":"Mayotte","code":"YT"},{"name":"Mexico","code":"MX"},{"name":"Micronesia, Federated States of","code":"FM"},{"name":"Moldova, Republic of","code":"MD"},{"name":"Monaco","code":"MC"},{"name":"Mongolia","code":"MN"},{"name":"Montserrat","code":"MS"},{"name":"Morocco","code":"MA"},{"name":"Mozambique","code":"MZ"},{"name":"Myanmar","code":"MM"},{"name":"Namibia","code":"NA"},{"name":"Nauru","code":"NR"},{"name":"Nepal","code":"NP"},{"name":"Netherlands","code":"NL"},{"name":"Netherlands Antilles","code":"AN"},{"name":"New Caledonia","code":"NC"},{"name":"New Zealand","code":"NZ"},{"name":"Nicaragua","code":"NI"},{"name":"Niger","code":"NE"},{"name":"Nigeria","code":"NG"},{"name":"Niue","code":"NU"},{"name":"Norfolk Island","code":"NF"},{"name":"Northern Mariana Islands","code":"MP"},{"name":"Norway","code":"NO"},{"name":"Oman","code":"OM"},{"name":"Pakistan","code":"PK"},{"name":"Palau","code":"PW"},{"name":"Palestinian Territory, Occupied","code":"PS"},{"name":"Panama","code":"PA"},{"name":"Papua New Guinea","code":"PG"},{"name":"Paraguay","code":"PY"},{"name":"Peru","code":"PE"},{"name":"Philippines","code":"PH"},{"name":"Pitcairn","code":"PN"},{"name":"Poland","code":"PL"},{"name":"Portugal","code":"PT"},{"name":"Puerto Rico","code":"PR"},{"name":"Qatar","code":"QA"},{"name":"Reunion","code":"RE"},{"name":"Romania","code":"RO"},{"name":"Russian Federation","code":"RU"},{"name":"RWANDA","code":"RW"},{"name":"Saint Helena","code":"SH"},{"name":"Saint Kitts and Nevis","code":"KN"},{"name":"Saint Lucia","code":"LC"},{"name":"Saint Pierre and Miquelon","code":"PM"},{"name":"Saint Vincent and the Grenadines","code":"VC"},{"name":"Samoa","code":"WS"},{"name":"San Marino","code":"SM"},{"name":"Sao Tome and Principe","code":"ST"},{"name":"Saudi Arabia","code":"SA"},{"name":"Senegal","code":"SN"},{"name":"Serbia and Montenegro","code":"CS"},{"name":"Seychelles","code":"SC"},{"name":"Sierra Leone","code":"SL"},{"name":"Singapore","code":"SG"},{"name":"Slovakia","code":"SK"},{"name":"Slovenia","code":"SI"},{"name":"Solomon Islands","code":"SB"},{"name":"Somalia","code":"SO"},{"name":"South Africa","code":"ZA"},{"name":"South Georgia and the South Sandwich Islands","code":"GS"},{"name":"Spain","code":"ES"},{"name":"Sri Lanka","code":"LK"},{"name":"Sudan","code":"SD"},{"name":"Suriname","code":"SR"},{"name":"Svalbard and Jan Mayen","code":"SJ"},{"name":"Swaziland","code":"SZ"},{"name":"Sweden","code":"SE"},{"name":"Switzerland","code":"CH"},{"name":"Syrian Arab Republic","code":"SY"},{"name":"Taiwan, Province of China","code":"TW"},{"name":"Tajikistan","code":"TJ"},{"name":"Tanzania, United Republic of","code":"TZ"},{"name":"Thailand","code":"TH"},{"name":"Timor-Leste","code":"TL"},{"name":"Togo","code":"TG"},{"name":"Tokelau","code":"TK"},{"name":"Tonga","code":"TO"},{"name":"Trinidad and Tobago","code":"TT"},{"name":"Tunisia","code":"TN"},{"name":"Turkey","code":"TR"},{"name":"Turkmenistan","code":"TM"},{"name":"Turks and Caicos Islands","code":"TC"},{"name":"Tuvalu","code":"TV"},{"name":"Uganda","code":"UG"},{"name":"Ukraine","code":"UA"},{"name":"United Arab Emirates","code":"AE"},{"name":"United Kingdom","code":"GB"},{"name":"United States","code":"US"},{"name":"United States Minor Outlying Islands","code":"UM"},{"name":"Uruguay","code":"UY"},{"name":"Uzbekistan","code":"UZ"},{"name":"Vanuatu","code":"VU"},{"name":"Venezuela","code":"VE"},{"name":"Viet Nam","code":"VN"},{"name":"Virgin Islands, British","code":"VG"},{"name":"Virgin Islands, U.S.","code":"VI"},{"name":"Wallis and Futuna","code":"WF"},{"name":"Western Sahara","code":"EH"},{"name":"Yemen","code":"YE"},{"name":"Zambia","code":"ZM"},{"name":"Zimbabwe","code":"ZW"}]';
        return response()->json($datajson, 200);
    }
   

}
