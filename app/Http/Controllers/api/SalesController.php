<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    // create a sale
    /*'user_id',
        'farm_id',
        'customer_name',
        'customer_phone',
        'total_amount',* */

   // Check who is allowed to sales 
    private function authorizeSaleManagement($user)
    {
        if (!in_array($user->role, ['owner','staff','manager'])) {
            abort(403, 'Unauthorized');
        }
    }
    public function create(Request $request)
    {
        $staff = $request->user();
        $this->authorizeSaleManagement($staff);
        $request->validate([
            'customer_name'=>'required',
            'customer_phone'=>'required',
            'total_amount' =>'required|numeric|min:0'
        ]);
        // create sale
        $sale = Sale::create([
            'customer_name' => $request->customer_name,
            'customer_phone'=>$request->customer_phone,
            'total_amount'  =>$request->total_amount,
            'farm_id'       =>$staff->farm_id,
            'user_id'       =>$staff->id,
        ]);
        // response
        return response()->json($sale);
    }
    // sales
  public function index(Request $request)
    {
        $staff = $request->user();
        $this->authorizeSaleManagement($staff);
        $sales = Sale::where('farm_id',$staff->farm_id)->get();
        return response()->json($sales->load('user'));
    }
}
