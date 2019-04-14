<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\user;
use App\Groupsall;
use DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $users = DB::table('users')->leftJoin('group', 'users.group_id', '=', 'group.id')->orderBy('users.group_id', 'asc')->orderBy('users.id', 'asc')->get(['users.id','users.name','users.username','users.email','users.group_id','users.alowaccess as allowed','group.name as groupname']);//->orderBy('users.group_id', 'desc');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addUseradd(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'username' => 'required|unique:users',
            'useremail' => 'required|email|unique:users,email',
            'password' => 'required|alphaNum|min:3',
            'usertypes' => 'required',
        ]);
        
        

            $mainorder = new User;
            $mainorder->name = $request->name;
            $mainorder->username = $request->username;
            $mainorder->group_id = $request->usertypes;            
            $mainorder->alowaccess = $request->allowedaccess;
            $mainorder->email = $request->useremail;
            $mainorder->password = Hash::make($request->password);
            $mainorder->active = '1';
            $mainorder->updated_by = $request->user()->id;
            $mainorder->save();


        return response()->json([
            'message' => 'User created successfully'
        ], 200);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function groupall()
    {
        return $users = Groupsall::all();
    }

    public function getspecifcUsernw($id)
    {   $allexpence = User::where('id',$id)->where('companyid','0')->get(['name','username','group_id','email','alowaccess'])->first();
        if(count($allexpence) > 0){
            return response()->json($allexpence, 200);
        }
        return response()->json([
            'response' => 'error',
            'message' => 'No Record Found'
        ], 400);
    }

    public function editUseradd(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'name' => 'required',
            'useremail' => 'required|email',
            'password' => 'required|alphaNum|min:3',
            'usertypes' => 'required',
        ]);
        
        
        $id=$request->id;
            $mainorder = User::where('id',$id)->where('companyid','0')->get()->first();
            $mainorder->name = $request->name;
            $mainorder->group_id = $request->usertypes;            
            $mainorder->alowaccess = $request->allowedaccess;
            $mainorder->email = $request->useremail;
            $mainorder->password = Hash::make($request->password);
            $mainorder->active = '1';
            $mainorder->updated_by = $request->user()->id;
            $mainorder->save();


        return response()->json([
            'message' => 'User updated successfully'
        ], 200);
    }
    public function showallasarray()
    {   $users = User::all();
        $array = array();
        foreach ($users as $key => $value) {
           $array[$value->id] = $value->name;
        }        
        return response()->json($array, 200);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function add()
    {
        //
        return view('users.adduser');
        
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
