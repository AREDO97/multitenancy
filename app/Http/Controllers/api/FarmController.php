<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use Illuminate\Http\Request;

class FarmController extends Controller
{
    // create farm 
    public function create(Request $request)
    {
        $user = $request->user();
        // validation
        $validated = $request->validate([
            'name'=>'required',
            'phone'=>'required',
            'email'=>'nullable',
            'address'=>'required',
            'status'=>'active'
        ]);
        // create 
        $farm = Farm::create($validated);
        // update user
        $user->update([
            'role'=>'owner',
            'farm_id'=>$farm->id
        ]);
        // response
        return response()->json($farm);
    }
    // update farm info
    public function update(Request $request,Farm $farm)
    {
         $user = $request->user();  
        if($user->farm_id !== $farm->id)
            {
                abort(403,'Unauthorised');
            }

             // validation
        $validated = $request->validate([
            'name'=>'nullable|max:45',
            'phone'=>'nullable|max:15',
            'email'=>'nullable',
            'address'=>'nullable|max:50',
        ]);
            // update 
            $farm->update([
            'name'=>$request->name ?? $farm->name,
            'phone'=>$request->phone ?? $farm->phone,
            'email'=>$request->email ?? $farm->email,
            'address'=>$request->address ?? $farm->address,
            ]);
            // response
            return response()->json($farm);
    }
    // user farm
    public function show(Request $request)
{
    return response()->json([
        'farm' => $request->user()->farm
    ]);
}

    // view all farms
public function index(Request $request)
{
    if ($request->user()->role !== 'platform_admin') {
        abort(403, 'Unauthorized');
    }

    $farms = Farm::latest()->paginate(15);

    return response()->json($farms->load('user'));
}

// Suspend farm
public function destroy(Request $request, Farm $farm)
{
    $user = $request->user();

    if ($user->role !== 'platform_admin') {
        abort(403, 'Unauthorized');
    }

    $farm->update([
        'status' => 'suspended'
    ]);

    return response()->json([
        'message' => 'Farm suspended successfully',
        'farm'    => $farm,
    ]);
}
// unsuspend farm
public function unsuspendFarm(Request $request, Farm $farm)
{
    $user = $request->user();

    if ($user->role !== 'platform_admin') {
        abort(403, 'Unauthorized');
    }

    $farm->update([
        'status' => 'active'
    ]);

    return response()->json([
        'message' => 'Farm account activated',
        'farm'    =>$farm
    ]);
}
}
