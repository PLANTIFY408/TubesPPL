<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Product::query();

            // Filter berdasarkan tipe (sale/rent)
            if ($request->has('type') && $request->type !== 'all') {
                $query->where('type', $request->type);
            }

            // Pencarian
            if ($request->filled('search')) {
                $search = trim($request->search);
                \Log::info('Search term: ' . $search); // Debug log

                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('category', 'like', "%{$search}%");
                });
            }

            // Sorting
            $sort = $request->get('sort', 'created_at');
            $order = $request->get('order', 'desc');
            $query->orderBy($sort, $order);

            // Debug log untuk query
            \Log::info('SQL Query: ' . $query->toSql());
            \Log::info('Query Bindings: ' . json_encode($query->getBindings()));

            // Pagination
            $products = $query->paginate(12)->withQueryString();

            // Debug log untuk jumlah hasil
            \Log::info('Number of products found: ' . $products->count());

            return view('Product.products', compact('products'));
        } catch (\Exception $e) {
            \Log::error('Error in ProductController@index: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat produk');
        }
    }

    public function show(Product $product)
    {
        try {
            // Load reviews dengan eager loading
            $product->load(['reviews.user']);
            
            return view('Product.detail', compact('product'));
        } catch (\Exception $e) {
            \Log::error('Error in ProductController@show: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat detail produk');
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'type' => 'required|in:sale,rent',
                'rent_period' => 'required_if:type,rent|nullable|string',
                'image' => 'required|image|max:2048',
                'category' => 'nullable|string|max:255',
                'stock' => 'required|integer|min:0',
            ]);

            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('products', 'public');
            }

            $product = Product::create($validated);

            // Clear cache
            Cache::tags(['products'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan',
                'product' => $product
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menambahkan produk'
            ], 500);
        }
    }

    public function update(Request $request, Product $product)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'type' => 'required|in:sale,rent',
                'rent_period' => 'required_if:type,rent|nullable|string',
                'image' => 'nullable|image|max:2048',
                'category' => 'nullable|string|max:255',
                'stock' => 'required|integer|min:0',
                'is_active' => 'boolean'
            ]);

            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('products', 'public');
            }

            $product->update($validated);

            // Clear cache
            Cache::tags(['products'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil diperbarui',
                'product' => $product
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui produk'
            ], 500);
        }
    }

    public function destroy(Product $product)
    {
        try {
            $product->delete();

            // Clear cache
            Cache::tags(['products'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus produk'
            ], 500);
        }
    }
} 