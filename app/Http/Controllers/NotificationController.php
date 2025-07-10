<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        NotificationService::markAsRead($notification);
        
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        NotificationService::markAllAsRead(auth()->user());
        
        return redirect()->route('notifications.index')
            ->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }

    public function destroy(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->delete();
        
        return redirect()->route('notifications.index')
            ->with('success', 'Notification supprimée.');
    }

    public function getUnreadCount()
    {
        $count = auth()->user()->unreadNotificationsCount();
        
        return response()->json(['count' => $count]);
    }

    public function getUnreadNotifications()
    {
        $notifications = auth()->user()->unreadNotifications()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        return response()->json($notifications);
    }
}
