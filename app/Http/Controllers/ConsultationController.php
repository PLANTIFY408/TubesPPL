<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConsultationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'ahli') {
            // Untuk Ahli: Tampilkan daftar user yang pernah chat dengannya
            $conversations = Chat::where('sender_id', $user->id)
                                 ->orWhere('receiver_id', $user->id)
                                 ->with(['sender', 'receiver'])
                                 ->get()
                                 ->map(function ($chat) use ($user) {
                                     // Dapatkan user lawan bicara
                                     return $chat->sender->id === $user->id ? $chat->receiver : $chat->sender;
                                 })
                                 ->unique('id'); // Hanya tampilkan satu entri per user lawan bicara

            return view('Consultation.expert_conversations', compact('conversations'));

        } else {
            // Untuk User Biasa:
            $currentUser = Auth::user();

            // Ambil percakapan terakhir dengan setiap ahli
            $activeConversations = Chat::where('sender_id', $currentUser->id)
                ->orWhere('receiver_id', $currentUser->id)
                ->orderBy('created_at', 'desc') // Urutkan dari yang terbaru
                ->get()
                ->unique(function ($chat) use ($currentUser) {
                    // Unik berdasarkan ID lawan bicara
                    return $chat->sender_id === $currentUser->id ? $chat->receiver_id : $chat->sender_id;
                });

            // Ambil ID ahli dari percakapan aktif
            $chattedExpertIds = $activeConversations->pluck($currentUser->role === 'user' ? 'receiver_id' : 'sender_id')->unique()->toArray();

            // Ambil semua ahli
            $allExperts = User::where('role', 'ahli')->get();

            // Pisahkan ahli yang sudah chat dan belum
            $chattedExperts = $allExperts->filter(function ($expert) use ($chattedExpertIds) {
                return in_array($expert->id, $chattedExpertIds);
            })->map(function($expert) use ($activeConversations, $currentUser) {
                // Tambahkan percakapan terakhir ke objek ahli
                 $lastMessage = $activeConversations->firstWhere(function($chat) use ($expert, $currentUser) {
                     return ($chat->sender_id === $currentUser->id && $chat->receiver_id === $expert->id) ||
                            ($chat->sender_id === $expert->id && $chat->receiver_id === $currentUser->id);
                 });
                $expert->last_message = $lastMessage ? $lastMessage->message : ''; // Ambil teks pesan terakhir
                $expert->last_message_time = $lastMessage ? $lastMessage->created_at : null; // Ambil waktu pesan terakhir
                 return $expert;
            });

            $nonChattedExperts = $allExperts->reject(function ($expert) use ($chattedExpertIds) {
                return in_array($expert->id, $chattedExpertIds);
            });

            // Urutkan percakapan aktif berdasarkan waktu pesan terakhir
             $chattedExperts = $chattedExperts->sortByDesc('last_message_time');

            return view('Consultation.index', compact('chattedExperts', 'nonChattedExperts'));
        }
    }

    public function showChat($userIdOrExpertId, Request $request)
    {
        $currentUser = Auth::user();
        $otherUser = User::findOrFail($userIdOrExpertId);

        // Query dasar untuk mengambil pesan antara user saat ini dan user lainnya
        $messagesQuery = Chat::where(function($query) use ($currentUser, $otherUser) {
            $query->where('sender_id', $currentUser->id)
                  ->where('receiver_id', $otherUser->id);
        })->orWhere(function($query) use ($currentUser, $otherUser) {
            $query->where('sender_id', $otherUser->id)
                  ->where('receiver_id', $currentUser->id);
        })->with(['sender', 'receiver']); // Muat relasi sender dan receiver

        // Jika ini request AJAX (untuk auto-refresh), ambil pesan yang lebih baru
        if ($request->ajax()) {
            $lastMessageId = $request->query('last_message_id');
            if ($lastMessageId) {
                $messagesQuery->where('id', '>', $lastMessageId);
            }
            $messages = $messagesQuery->orderBy('created_at')->get();
            // Format waktu pesan untuk ditampilkan di frontend
            $messages->each(function($message) {
                 $message->formatted_created_at = $message->created_at->format('H:i');
            });

            return response()->json(['messages' => $messages]);
        }

        // Jika bukan request AJAX, ambil semua pesan dan tampilkan view
        $messages = $messagesQuery->orderBy('created_at')->get();

        // Menentukan nama lawan bicara
        $chatPartnerName = $otherUser->name;

        return view('Consultation.chat', compact('otherUser', 'messages', 'chatPartnerName'));
    }

    public function sendMessage(Request $request, $userIdOrExpertId)
    {
        $request->validate([
            'message' => 'nullable|string', // Pesan bisa kosong jika hanya mengirim gambar
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Gambar opsional
        ]);

        $currentUser = Auth::user();
        $otherUser = User::findOrFail($userIdOrExpertId);

        $chatData = [
            'sender_id' => $currentUser->id,
            'receiver_id' => $otherUser->id,
            'message' => $request->message,
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('chat_images', 'public');
            $chatData['image'] = $imagePath;
        }

        Chat::create($chatData);

        // Jika request datang dari AJAX, kembalikan pesan baru
        if ($request->ajax()) {
             // Ambil pesan yang baru saja dibuat dengan relasi sender
            $newMessage = Chat::with('sender')->find(Chat::latest()->first()->id); // Ambil pesan terakhir

            // Format waktu pesan untuk ditampilkan di frontend (jika diperlukan)
             $newMessage->formatted_created_at = $newMessage->created_at->format('H:i');

            return response()->json(['success' => true, 'message' => $newMessage]);
        }

        return back()->with('success', 'Pesan terkirim!');
    }
}
