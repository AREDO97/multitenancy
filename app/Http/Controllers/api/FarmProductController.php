<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\FarmProduct;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FarmProductController extends Controller
{
    // Check who is allowed to manage farm products
    private function authorizeProductManagement($user)
    {
        if (!in_array($user->role, ['owner', 'manager', 'staff'])) {
            abort(403, 'Unauthorized');
        }

        if (!$user->farm_id) {
            abort(403, 'Unauthorised action.');
        }
    }

    // Create farm product
    public function create(Request $request)
    {
        $user = $request->user();

        $this->authorizeProductManagement($user);

        $validated = $request->validate([
            'product_id' => ['required','exists:products,id',
            Rule::unique('farm_products', 'product_id')->where('farm_id', $user->farm_id),],
            'selling_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|numeric|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        $farmProduct = FarmProduct::create([
            ...$validated,
            'farm_id' => $user->farm_id,
        ]);

        return response()->json([
            'message' => 'Farm product created successfully',
            'farm_product' => $farmProduct
        ], 201);
    }

    // View all products belonging to my farm
    public function index(Request $request)
    {
        $user = $request->user();

        $this->authorizeProductManagement($user);

        $farmProducts = FarmProduct::where('farm_id', $user->farm_id)
            ->with('product')
            ->latest()
            ->paginate(15);

        return response()->json($farmProducts);
    }

    // View one farm product
    public function show(Request $request, $id)
    {
        $user = $request->user();

        $this->authorizeProductManagement($user);

        $farmProduct = FarmProduct::where('farm_id', $user->farm_id)
            ->with('product')
            ->findOrFail($id);

        return response()->json([
            'farm_product' => $farmProduct
        ]);
    }

    // Update farm product
    public function update(Request $request, $id)
    {
        $user = $request->user();

        $this->authorizeProductManagement($user);

        $farmProduct = FarmProduct::where('farm_id', $user->farm_id)
            ->findOrFail($id);

        $validated = $request->validate([
            'selling_price' => 'sometimes|required|numeric|min:0',
            'stock_quantity' => 'sometimes|required|numeric|min:0',
            'is_active' => 'sometimes|required|boolean',
        ]);

        $farmProduct->update($validated);

        return response()->json([
            'message' => 'Farm product updated successfully',
            'farm_product' => $farmProduct->fresh()->load('product')
        ]);
    }

    // Delete farm product
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        $this->authorizeProductManagement($user);

        $farmProduct = FarmProduct::where('farm_id', $user->farm_id)
            ->findOrFail($id);

        // Deactivate instead of permanently deleting
        $farmProduct->update([
            'is_active' => false
        ]);

        return response()->json([
            'message' => 'Farm product deactivated successfully'
        ]);
    }
}
