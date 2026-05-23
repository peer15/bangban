<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * User inbox - list all chat conversations
     */
    public function userInbox()
    {
        $pesanans = Pesanan::where('user_id', auth()->id())
            ->whereNotNull('mitra_id')
            ->with(['mitra.user'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($pesanan) {
                $lastChat = Chat::where('pesanan_id', $pesanan->id)->latest()->first();
                $unread = Chat::where('pesanan_id', $pesanan->id)
                    ->where('sender_role', 'mitra')
                    ->where('is_read', false)
                    ->count();
                $pesanan->last_chat = $lastChat;
                $pesanan->unread_count = $unread;
                return $pesanan;
            })
            ->sortByDesc(function ($p) {
                return $p->last_chat ? $p->last_chat->created_at : $p->created_at;
            });

        return view('user.chat-list', compact('pesanans'));
    }

    /**
     * Mitra inbox - list all chat conversations
     */
    public function mitraInbox()
    {
        $mitra = auth()->user()->mitra;
        $pesanans = Pesanan::where('mitra_id', $mitra->id)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($pesanan) {
                $lastChat = Chat::where('pesanan_id', $pesanan->id)->latest()->first();
                $unread = Chat::where('pesanan_id', $pesanan->id)
                    ->where('sender_role', 'user')
                    ->where('is_read', false)
                    ->count();
                $pesanan->last_chat = $lastChat;
                $pesanan->unread_count = $unread;
                return $pesanan;
            })
            ->sortByDesc(function ($p) {
                return $p->last_chat ? $p->last_chat->created_at : $p->created_at;
            });

        return view('mitra.chat-list', compact('pesanans'));
    }

    /**
     * Chat page for user
     */
    public function userChat(Pesanan $pesanan)
    {
        abort_if($pesanan->user_id !== auth()->id(), 403);
        abort_if(!$pesanan->mitra_id, 404, 'Mitra belum ditemukan');

        // Mark mitra messages as read
        Chat::where('pesanan_id', $pesanan->id)
            ->where('sender_role', 'mitra')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $chats = Chat::where('pesanan_id', $pesanan->id)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        $pesanan->load('mitra.user');

        return view('user.chat', compact('pesanan', 'chats'));
    }

    /**
     * Chat page for mitra
     */
    public function mitraChat(Pesanan $pesanan)
    {
        $mitra = auth()->user()->mitra;
        abort_if($pesanan->mitra_id !== $mitra->id, 403);

        // Mark user messages as read
        Chat::where('pesanan_id', $pesanan->id)
            ->where('sender_role', 'user')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $chats = Chat::where('pesanan_id', $pesanan->id)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        $pesanan->load('user');

        return view('mitra.chat', compact('pesanan', 'chats'));
    }

    /**
     * Send message (user)
     */
    public function userSend(Request $request, Pesanan $pesanan)
    {
        abort_if($pesanan->user_id !== auth()->id(), 403);

        $request->validate(['message' => 'required|string|max:1000']);

        $chat = Chat::create([
            'pesanan_id' => $pesanan->id,
            'sender_id' => auth()->id(),
            'sender_role' => 'user',
            'message' => $request->message,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'chat' => $chat]);
        }

        return back();
    }

    /**
     * Send message (mitra)
     */
    public function mitraSend(Request $request, Pesanan $pesanan)
    {
        $mitra = auth()->user()->mitra;
        abort_if($pesanan->mitra_id !== $mitra->id, 403);

        $request->validate(['message' => 'required|string|max:1000']);

        $chat = Chat::create([
            'pesanan_id' => $pesanan->id,
            'sender_id' => auth()->id(),
            'sender_role' => 'mitra',
            'message' => $request->message,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'chat' => $chat]);
        }

        return back();
    }

    /**
     * API: Get new messages (for polling)
     */
    public function getMessages(Pesanan $pesanan)
    {
        $user = auth()->user();

        // Verify access
        if ($user->role === 'user') {
            abort_if($pesanan->user_id !== $user->id, 403);
            // Mark mitra messages as read
            Chat::where('pesanan_id', $pesanan->id)
                ->where('sender_role', 'mitra')
                ->where('is_read', false)
                ->update(['is_read' => true]);
        } else {
            $mitra = $user->mitra;
            abort_if($pesanan->mitra_id !== $mitra->id, 403);
            // Mark user messages as read
            Chat::where('pesanan_id', $pesanan->id)
                ->where('sender_role', 'user')
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        $chats = Chat::where('pesanan_id', $pesanan->id)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($chat) {
                return [
                    'id' => $chat->id,
                    'message' => $chat->message,
                    'sender_role' => $chat->sender_role,
                    'sender_name' => $chat->sender->name,
                    'time' => $chat->created_at->format('H:i'),
                    'is_read' => $chat->is_read,
                ];
            });

        return response()->json($chats);
    }

    /**
     * Unread count for specific pesanan
     */
    public function unreadCount(Pesanan $pesanan)
    {
        $user = auth()->user();
        $role = $user->role === 'user' ? 'mitra' : 'user';

        $count = Chat::where('pesanan_id', $pesanan->id)
            ->where('sender_role', $role)
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Global unread count (all active pesanan) - for navbar badge
     */
    public function globalUnread()
    {
        $user = auth()->user();

        if ($user->role === 'user') {
            // Count unread messages from mitra in all active pesanan
            $activePesananIds = Pesanan::where('user_id', $user->id)
                ->whereNotIn('status', ['selesai', 'dibatalkan'])
                ->whereNotNull('mitra_id')
                ->pluck('id');

            $count = Chat::whereIn('pesanan_id', $activePesananIds)
                ->where('sender_role', 'mitra')
                ->where('is_read', false)
                ->count();

            // Get pesanan_id for redirect
            $pesananId = Chat::whereIn('pesanan_id', $activePesananIds)
                ->where('sender_role', 'mitra')
                ->where('is_read', false)
                ->latest()
                ->value('pesanan_id');
        } else {
            // Mitra: count unread from users
            $mitra = $user->mitra;
            $activePesananIds = Pesanan::where('mitra_id', $mitra?->id)
                ->whereNotIn('status', ['selesai', 'dibatalkan'])
                ->pluck('id');

            $count = Chat::whereIn('pesanan_id', $activePesananIds)
                ->where('sender_role', 'user')
                ->where('is_read', false)
                ->count();

            $pesananId = Chat::whereIn('pesanan_id', $activePesananIds)
                ->where('sender_role', 'user')
                ->where('is_read', false)
                ->latest()
                ->value('pesanan_id');
        }

        return response()->json(['count' => $count, 'pesanan_id' => $pesananId]);
    }
}
