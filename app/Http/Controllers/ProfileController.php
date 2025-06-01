<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Muat relasi pesanan, item pesanan, dan produk
        $user->load(['orders.orderItems.product']);

        // Hitung jumlah alat yang dibeli user
        $totalTools = 0;
        foreach ($user->orders as $order) {
            // Hanya hitung dari pesanan yang sudah selesai (completed)
            if ($order->status === 'completed') {
                foreach ($order->orderItems as $item) {
                    // Hitung jika kategori produk mengandung kata 'alat' (case-insensitive)
                    if ($item->product && stripos($item->product->category, 'alat') !== false) {
                        $totalTools += $item->quantity;
                    }
                }
            }
        }

        // Hitung jumlah ahli unik yang pernah diajak chat
        $chattedExpertsCount = \App\Models\Chat::where('sender_id', $user->id)
                                          ->orWhere('receiver_id', $user->id)
                                          ->get()
                                          ->map(function ($chat) use ($user) {
                                              return $chat->sender_id === $user->id ? $chat->receiver_id : $chat->sender_id;
                                          })
                                          ->unique()
                                          ->count();

        return view('profile', compact('user', 'totalTools', 'chattedExpertsCount'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'profile_photo' => 'nullable|image|max:2048',
            'current_password' => 'nullable|required_with:new_password|current_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        // Update data dasar
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? $user->phone;
        $user->address = $validated['address'] ?? $user->address;

        // Update password jika diisi
        if ($request->filled('new_password')) {
            $user->password = Hash::make($validated['new_password']);
        }

        // Update foto profil jika diupload
        if ($request->hasFile('profile_photo')) {
            // Hapus foto lama jika ada
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            
            // Simpan foto baru
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui');
    }
} 