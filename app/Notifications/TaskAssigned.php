<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskAssigned extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
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
                // 'id'    => Auth::id(),
                'name'  => Auth::user()->name,
                'profile_photo_url' => Auth::user()->profile_photo_url,
            ],
            'message'   => 'add you to the task <strong>'. $this->task->title .'</strong> on <strong>'. $this->task->taskList->project->title .'</strong>',
            'link'      => route('tasks.show', ['task'  => $this->task->id]),
        ];
    }

    public function toBroadcast($notifiable)
    {
        $data = [
            'sender'    => [
                'name'  => Auth::user()->name,
                'profile_photo_url' => Auth::user()->profile_photo_url,
            ],
            'message'   => 'add you to the task <strong>'. $this->task->title .'</strong> on <strong>'. $this->task->taskList->project->title .'</strong>',
            'created_at'=> now()->diffForHumans(),
            'link'      => route('tasks.show', ['task'  => $this->task->id]),
        ];
        return (new BroadcastMessage($data));
    }
}
