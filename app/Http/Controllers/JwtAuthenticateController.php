<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Role;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Log;

class JwtAuthenticateController extends Controller
{

		public function __construct()
    {
        // Apply the jwt.auth middleware to all methods in this controller
        // except for the authenticated method. We don't want to prevent
        // the user from retrieving their token if they don't already have it
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);
    }

    public function index()
    {
    		return response()->json(['auth' => Auth::user(), 'users' => User::all()]);
    }

    public function authenticate(Request $request)
    {
    	$credentials = $request->only('email', 'password');

    	try {
    		// Verify the credentials and create a token for the user
    		if (!$token = JWTAuth::attempt($credentials)) {
    				return response()->json(['error' => 'invalid_credentials'], 401);
    		}
    	} catch (JWTException $e) {
    		// Something went wrong
    		return response()->json(['error' => 'could_not_create_token'], 500);
    	}

    	// If no errors are encoutnered we can return a JWT
    	return response()->json(compact('token'));
    }

    public function createRole(Request $request)
    {
    	$role = new Role();
    	$role->name = $request->input('name');
    	$role->save();

   		//$owner = new Role();
			// $owner->name         = 'owner';
			// $owner->display_name = 'Project Owner'; // optional
			// $owner->description  = 'User is the owner of a given project'; // optional
			// $owner->save();

    	return response()->json("created");
    }

    public function createPermission(Request $request)
    {
    	$viewUsers = new Permission();
    	$viewUsers->name = $request->input('name');
    	$viewUsers->save();

    	return response()->json("created");
    }

    public function assignRole(Request $request)
    {
    	$user = User::where('email', '=', $request->input('email'))->first();

    	$role = Role::where('name', '=', $request->input('role'))->first();
			$user->attachRole($request->input('role'));
			// $user->roles()->attach($role->id);

			return response()->json("created");
    }

    public function attachPermission(Request $request)
    {
    	$role = Role::where('name', '=', $request->input('role'))->first();
    	$permission = Permission::where('name', '=', $request->input('name'))->first();
    	$role->attachPermission($permission);

    	return response()->json("created");
    }
}
