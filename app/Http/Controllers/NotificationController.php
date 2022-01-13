<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->where('type', '<>', 'App\Notifications\ProjectInvite')->markAsRead();

        return redirect()->back();
    }
}
