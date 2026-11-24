<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Check who is allowed to manage products
    private function authorizeProductManagement($user)
    {
        if (!in_array($user->role, ['platform_admin', 'owner'])) {
            abort(403, 'Unauthorized');
        }
    }

    // Create product
    public function create(Request $request)
    {
        $this->authorizeProductManagement($request->user());

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'is_active' => 'sometimes|boolean',
        ]);

        $product = Product::create($validated);

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product
        ], 201);
    }

    // View all products
    public function index(Request $request)
    {
        $this->authorizeProductManagement($request->user());

        $products = Product::latest()->paginate(15);

        return response()->json($products);
    }

    // View one product
    public function show(Request $request, Product $product)
    {
        $this->authorizeProductManagement($request->user());

        return response()->json([
            'product' => $product
        ]);
    }

    // Update product
    public function update(Request $request, Product $product)
    {
        $this->authorizeProductManagement($request->user());

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'unit' => 'sometimes|required|string|max:50',
            'is_active' => 'sometimes|boolean',
        ]);

        $product->update($validated);

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product->fresh()
        ]);
    }

    // Delete product
    public function destroy(Request $request, Product $product)
    {
        $this->authorizeProductManagement($request->user());

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }
}
