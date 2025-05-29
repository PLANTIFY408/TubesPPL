<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $orders = auth()->user()->orders()
            ->with(['orderItems.product'])
            ->latest()
            ->get();

        return view('Order.index', compact('orders'));
    }

    public function checkout(Request $request)
    {
        \Log::info('Memulai proses checkout');
        $request->validate([
            'shipping_address' => 'required|string',
            'payment_method' => 'required|in:transfer,bank'
        ]);
        \Log::info('Validasi request checkout berhasil');

        $cartItems = Auth::user()->carts()->with('product')->get();
        \Log::info('Mengambil item keranjang', ['count' => $cartItems->count()]);
        
        if ($cartItems->isEmpty()) {
            \Log::warning('Keranjang kosong saat checkout');
            return response()->json([
                'success' => false,
                'message' => 'Keranjang belanja kosong'
            ], 400);
        }

        try {
            DB::beginTransaction();
            \Log::info('Transaksi database dimulai');

            // Buat order baru
            $order = Order::create([
                'user_id' => Auth::id(),
                'total_amount' => $cartItems->sum(function ($item) {
                    return $item->quantity * $item->product->price;
                }),
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'shipping_address' => $request->shipping_address
            ]);
            \Log::info('Order baru dibuat', ['order_id' => $order->id]);

            // Buat order items
            foreach ($cartItems as $item) {
                \Log::info('Memproses item keranjang', ['product_id' => $item->product_id, 'quantity' => $item->quantity]);
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                    'type' => $item->type,
                    'rent_period' => $item->rent_period
                ]);
                \Log::info('OrderItem dibuat', ['order_item_id' => $orderItem->id]);

                // Jika produk adalah 'Alat Ukur pH Tanah' (tipe rent), generate device_id
                if ($item->product->name === 'Alat Ukur pH Tanah' && $item->type === 'rent') {
                     // Generate unique device ID dengan awalan 'Device-' dan panjang yang lebih pendek
                     $deviceId = 'Device-' . substr(uniqid(), -8); // Menggunakan 8 karakter terakhir dari uniqid()
                     $orderItem->device_id = $deviceId;
                     $orderItem->save();
                     \Log::info('Device ID generated dan disimpan', ['device_id' => $orderItem->device_id]);
                }

                // Update stok produk
                $item->product->decrement('stock', $item->quantity);
                \Log::info('Stok produk diperbarui', ['product_id' => $item->product_id]);
            }

            // Hapus semua item di keranjang
            Auth::user()->carts()->delete();
            \Log::info('Keranjang dikosongkan');

            DB::commit();
            \Log::info('Transaksi database selesai (commit)');

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat',
                'order' => $order
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout Error: ' . $e->getMessage(), ['exception' => $e]); // Logging error lebih detail
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat pesanan'
            ], 500);
        }
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);
        
        $order->load(['orderItems.product']);
        
        return view('Order.show', compact('order'));
    }

    public function uploadPayment(Request $request, Order $order)
    {
        $this->authorize('update', $order);

        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            if ($request->hasFile('payment_proof')) {
                // Hapus bukti pembayaran lama jika ada
                if ($order->payment_proof) {
                    Storage::disk('public')->delete($order->payment_proof);
                }

                // Upload bukti pembayaran baru
                $path = $request->file('payment_proof')->store('payment-proofs', 'public');
                
                $order->update([
                    'payment_proof' => $path,
                    'status' => 'processing'
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Bukti pembayaran berhasil diunggah',
                    'data' => [
                        'payment_proof' => $path,
                        'status' => 'processing'
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan'
            ], 400);

        } catch (\Exception $e) {
            \Log::error('Upload Payment Error: ' . $e->getMessage());
            
            // Jika file sudah terupload tapi terjadi error saat update database
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengunggah bukti pembayaran'
            ], 500);
        }
    }
} 