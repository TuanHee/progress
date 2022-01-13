<?php

namespace App\Http\Middleware;

use Auth;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        return array_merge(parent::share($request), [
            'flash' => function () use ($request) {
                return [
                    'success' => $request->session()->get('success'),
                    'warning' => $request->session()->get('warning'),
                    'error' => $request->session()->get('error'),
                ];
            },
            'notifications' => Auth::check()
                ? Auth::user()->unreadNotifications()->get()->map(function($notification) {
                    if ($notification->type == 'App\Notifications\ProjectInvite') {
                        return [
                            'id'            => $notification->id,
                            'sender'        => $notification->data['sender'],
                            'message'       => $notification->data['message'],
                            'type'          => $notification->type,
                            'join_link'     => $notification->data['join_link'],
                            'deny_link'     => $notification->data['deny_link'],
                            'read'          => $notification->readed_at != null,
                            'created_at'    => $notification->created_at->diffForHumans(),
                        ];
                    }
                    return [
                        'id'        => $notification->id,
                        'sender'    => $notification->data['sender'],
                        'message'   => $notification->data['message'],
                        'link'      => $notification->data['link'],
                        'type'      => $notification->type,
                        'read'      => $notification->readed_at != null,
                        'created_at' => $notification->created_at->diffForHumans(),
                    ];
                })
                : null,
        ]);
    }
}
