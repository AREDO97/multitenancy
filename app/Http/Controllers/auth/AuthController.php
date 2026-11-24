<?php

namespace App\Http\Controllers\auth;
use App\Models\User;
use App\Models\AuditLog;
use App\Http\Controllers\Controller;
use App\Mail\WelcomeEmail;
use App\Models\Farm;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str; 
use Illuminate\Support\Facades\Password;
class AuthController extends Controller
{
     // create user
public function register(Request $request)
{
    //  Validation
    $request->validate([
        'name'     => ['required', 'string', 'max:255'],
        'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
        'password' => ['required', 'min:6'], 
    ]);

    // Insert User into db
    $user = User::create([
        'name'     => $request->name,
        'email'    => strtolower(trim($request->email)),
        'password' => Hash::make($request->password)
    ]);

   
    // email verification
    // $user->sendEmailVerificationNotification();
    // Issue Token 
    $token = $user->createToken('auth-token');

    // response
    return response()->json([
        'message' => 'Account created successfully',
        'user'    => $user,
        'token'   => $token,
    ], 201);
}


    // login
    public function login(Request $request)
{
    //  Validation
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
        'remember' =>'sometimes|boolean'
    ]);

    //  Fetch user from DB
$user = User::where('email', $request->email)->first();

//  Checking if user exists
if (!$user) {
    return response()->json([
        'message' => 'Invalid credentials',
    ], 401);
    
}
if ($user->role !== 'platform_admin')
    {

if($user->is_active !== 1)
    {
       return response()->json([
        'message' => 'account suspended ,contact admins',
    ], 401);  
    }
    // check if farm is still active
    $farm = Farm::where('id',$user->farm_id)->first();
    if($farm->status !== 'active')
        {
            return response()->json([
        'message' => 'Farm not active,contact admins',
    ], 401);  
        }
    }
    //  Checking user existence AND verifying password
    if (!$user || !Hash::check($request->password, $user->password)) {

        return response()->json([
            'message' => 'Invalid email or password'
        ], 401);
    }
    // generate remember me token
    $expiresAt=$request->boolean('remember')
    ?  now()->addDays(30)
    : now()->addMinutes(30);

    // Issue token to user
    $token = $user->createToken('auth-token'
    ,
    [],
    $expiresAt );

    //response
    return response()->json([
        'token' => $token,
        'user'  => $user,
    ], 200);
}
    // logout logic
    public function logout(Request $request)
{
    $user=$request->user();
 
    // delete current user token
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'message' => 'Logged out successfully',
    ], 200);
}

// get the current logged in user
public function getMe(Request $request)
{
    $user=$request->user();
    // response
    return response()->json($user);
}

}




