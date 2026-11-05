<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
   /**
     * Menampilkan daftar semua pengguna yang pernah berinteraksi (Inbox)
     */
    public function index()
    {
        $authId = Auth::id();

        // Ambil daftar ID user lawan bicara (pengirim atau penerima pesan)
        $userIds = Message::where('sender_id', $authId)
            ->pluck('receiver_id')
            ->merge(
                Message::where('receiver_id', $authId)->pluck('sender_id')
            )
            ->unique();

        // Ambil data User lawan bicara beserta pesan terakhirnya
        $conversations = User::whereIn('id', $userIds)
            ->get()
            ->map(function ($user) use ($authId) {
                // Ambil pesan terakhir antara user login dan user ini
                $lastMessage = Message::where(function ($q) use ($authId, $user) {
                    $q->where('sender_id', $authId)->where('receiver_id', $user->id);
                })->orWhere(function ($q) use ($authId, $user) {
                    $q->where('sender_id', $user->id)->where('receiver_id', $authId);
                })->latest()->first();

                $user->last_message = $lastMessage;
                return $user;
            });

        return view('chat.index', compact('conversations'));
    }

    /**
     * Menampilkan ruang obrolan dengan satu pengguna spesifik
     */
    public function show(User $user)
    {
        $authId = Auth::id();

        // Ambil riwayat chat antara dua user
        $messages = Message::where(function ($query) use ($authId, $user) {
            $query->where('sender_id', $authId)->where('receiver_id', $user->id);
        })->orWhere(function ($query) use ($authId, $user) {
            $query->where('sender_id', $user->id)->where('receiver_id', $authId);
        })->orderBy('created_at', 'asc')->get();

        // Tandai pesan dari lawan bicara sebagai sudah dibaca
        Message::where('sender_id', $user->id)
            ->where('receiver_id', $authId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('chat.show', compact('user', 'messages'));
    }

    /**
     * Menyimpan dan mengirim pesan baru
     */
    public function store(Request $request, User $user)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $user->id,
            'message' => $request->message,
        ]);

        // Siarkan event untuk real-time jika broadcasting diaktifkan
        if (class_exists(MessageSent::class)) {
            broadcast(new MessageSent($message->load('sender')))->toOthers();
        }

        return redirect()->route('chat.show', $user->id);
    }
}
