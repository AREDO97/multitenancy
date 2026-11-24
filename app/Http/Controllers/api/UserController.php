<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str; 
use Illuminate\Support\Facades\Password;

class UserController extends Controller
{

     // Check who is allowed to manage products
    private function authorizeProductManagement($user)
    {
        if (!in_array($user->role, ['owner'])) {
            abort(403, 'Unauthorized');
        }
    }
    // check role again
    private function authorizeUserManagement($user)
    {
        if (!in_array($user->role, ['owner','manager'])) {
            abort(403, 'Unauthorized');
        }
    }
    // add user to farm
    public function create(Request $request)
{
    $manager = $request->user();
    $this->authorizeProductManagement($manager);
    //  Validation
    $request->validate([
        'name'     => ['required', 'string', 'max:255'],
        'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
        'password' => ['required', 'min:6'], 
        'role'     => ['required'],
    ]);
    // check number of accounts
    $count = User::where('farm_id',$manager->farm_id)
    ->where('is_active',true)->count();
    if($count>3)
        {
            abort(403,'You redeemed all your accounts');
        }
    // Insert User into db
    $user = User::create([
        'name'     => $request->name,
        'email'    => strtolower(trim($request->email)),
        'password' => Hash::make($request->password),
        'farm_id'  => $manager->farm_id,
    ]);

    // response
    return response()->json([
        'message' => 'Account created successfully',
        'user'    => $user,
    ], 201);
}
// change user role
public function updateUserRole(Request $request,User $user)
{
    $owner = $request->user();
    $this->authorizeProductManagement($owner);
    if($owner->farm_id !== $user->farm_id)
        {
            abort(403,'Unauthorised action');
        }
    // validation
    $request->validate([
        'role'=>'required'
    ]);
    $user->update([
        'role'=>$request->role
    ]);
    // response
    return response()->json([
        'message'=>'User role updated successifully',
        'user'   =>$user
    ]);
}
// all users of a farm
public function index(Request $request)
{
     $owner = $request->user();
    $this->authorizeProductManagement($owner);
    $users=User::where('farm_id',$owner->farm_id)->get();
    return response()->json($users); 
}
// soft delete user
public function destroy(Request $request,User $user)
{
     $owner = $request->user();
    $this->authorizeUserManagement($owner);
     if($owner->farm_id !== $user->farm_id)
        {
            abort(403,'Unauthorised action');
        }
    $user->update([
        'is_active'=>false
    ]);
    return response()->json([
        'message'=>'user account suspended',
        'user'   =>$user
    ]);
}
}
