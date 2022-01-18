<?php

namespace App\Notifications;

use App\Models\ProjectMember;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ProjectInvite extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ProjectMember $member)
    {
        $this->member = $member;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'sender'    => [
                'name'  => Auth::user()->name,
                'profile_photo_url' => Auth::user()->profile_photo_url,
            ],
            'message'       => 'invited you to join the <strong>'. $this->member->project->title .' project</strong>',
            'deny_link'      => route('members.deny', ['member'  => $this->member, 'notification' => $this->id]),
            'join_link'     => route('members.joinByMail', ['member'  => $this->member, 'notification' => $this->id]),
        ];
    }

    public function toBroadcast($notifiable)
    {
        $data = [
            'sender'    => [
                'name'  => Auth::user()->name,
                'profile_photo_url' => Auth::user()->profile_photo_url,
            ],
            'message'       => 'invited you to join the <strong>'. $this->member->project->title .' project</strong>',
            'created_at'    => now()->diffForHumans(),
            'deny_link'      => route('members.deny', ['member'  => $this->member, 'notification' => $this->id]),
            'join_link'     => route('members.joinByMail', ['member'  => $this->member, 'notification' => $this->id]),
        ];
        return (new BroadcastMessage($data));
    }
}
