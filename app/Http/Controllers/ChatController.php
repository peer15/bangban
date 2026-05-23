<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendChatRequest;
use App\Models\Chat;
use App\Models\Pesanan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function userInbox(): View
    {
        $pesanans = $this->getInboxPesanans(
            query: Pesanan::where('user_id', auth()->id())->whereNotNull('mitra_id'),
            unreadRole: 'mitra',
            relations: ['mitra.user']
        );

        return view('user.chat-list', compact('pesanans'));
    }

    public function mitraInbox(): View
    {
        $mitra = auth()->user()->mitra;

        $pesanans = $this->getInboxPesanans(
            query: Pesanan::where('mitra_id', $mitra->id),
            unreadRole: 'user',
            relations: ['user']
        );

        return view('mitra.chat-list', compact('pesanans'));
    }

    public function userChat(Pesanan $pesanan): View
    {
        abort_if($pesanan->user_id !== auth()->id(), 403);
        abort_if(!$pesanan->mitra_id, 404, 'Mitra belum ditemukan');

        $this->markAsRead($pesanan->id, 'mitra');
        $chats = $this->getChatMessages($pesanan->id);
        $pesanan->load('mitra.user');

        return view('user.chat', compact('pesanan', 'chats'));
    }

    public function mitraChat(Pesanan $pesanan): View
    {
        $mitra = auth()->user()->mitra;
        abort_if($pesanan->mitra_id !== $mitra->id, 403);

        $this->markAsRead($pesanan->id, 'user');
        $chats = $this->getChatMessages($pesanan->id);
        $pesanan->load('user');

        return view('mitra.chat', compact('pesanan', 'chats'));
    }

    public function userSend(SendChatRequest $request, Pesanan $pesanan): JsonResponse|RedirectResponse
    {
        abort_if($pesanan->user_id !== auth()->id(), 403);

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

    public function mitraSend(SendChatRequest $request, Pesanan $pesanan): JsonResponse|RedirectResponse
    {
        $mitra = auth()->user()->mitra;
        abort_if($pesanan->mitra_id !== $mitra->id, 403);

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

    public function getMessages(Pesanan $pesanan): JsonResponse
    {
        $user = auth()->user();

        if ($user->role === 'user') {
            abort_if($pesanan->user_id !== $user->id, 403);
            $this->markAsRead($pesanan->id, 'mitra');
        } else {
            abort_if($pesanan->mitra_id !== $user->mitra?->id, 403);
            $this->markAsRead($pesanan->id, 'user');
        }

        $chats = Chat::where('pesanan_id', $pesanan->id)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn (Chat $chat) => [
                'id' => $chat->id,
                'message' => $chat->message,
                'sender_role' => $chat->sender_role,
                'sender_name' => $chat->sender->name,
                'time' => $chat->created_at->format('H:i'),
                'is_read' => $chat->is_read,
            ]);

        return response()->json($chats);
    }

    public function unreadCount(Pesanan $pesanan): JsonResponse
    {
        $user = auth()->user();
        $role = $user->role === 'user' ? 'mitra' : 'user';

        $count = Chat::where('pesanan_id', $pesanan->id)
            ->where('sender_role', $role)
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    public function globalUnread(): JsonResponse
    {
        $user = auth()->user();

        if ($user->role === 'user') {
            $activePesananIds = Pesanan::where('user_id', $user->id)
                ->whereNotIn('status', ['selesai', 'dibatalkan'])
                ->whereNotNull('mitra_id')
                ->pluck('id');
            $senderRole = 'mitra';
        } else {
            $activePesananIds = Pesanan::where('mitra_id', $user->mitra?->id)
                ->whereNotIn('status', ['selesai', 'dibatalkan'])
                ->pluck('id');
            $senderRole = 'user';
        }

        $count = Chat::whereIn('pesanan_id', $activePesananIds)
            ->where('sender_role', $senderRole)
            ->where('is_read', false)
            ->count();

        $pesananId = Chat::whereIn('pesanan_id', $activePesananIds)
            ->where('sender_role', $senderRole)
            ->where('is_read', false)
            ->latest()
            ->value('pesanan_id');

        return response()->json(['count' => $count, 'pesanan_id' => $pesananId]);
    }

    // ─── Private Helpers ─────────────────────────────────────

    private function getInboxPesanans($query, string $unreadRole, array $relations)
    {
        return $query->with($relations)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function (Pesanan $pesanan) use ($unreadRole) {
                $pesanan->last_chat = Chat::where('pesanan_id', $pesanan->id)->latest()->first();
                $pesanan->unread_count = Chat::where('pesanan_id', $pesanan->id)
                    ->where('sender_role', $unreadRole)
                    ->where('is_read', false)
                    ->count();
                return $pesanan;
            })
            ->sortByDesc(fn ($p) => $p->last_chat?->created_at ?? $p->created_at);
    }

    private function markAsRead(int $pesananId, string $senderRole): void
    {
        Chat::where('pesanan_id', $pesananId)
            ->where('sender_role', $senderRole)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    private function getChatMessages(int $pesananId)
    {
        return Chat::where('pesanan_id', $pesananId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();
    }
}
