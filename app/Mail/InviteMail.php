<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InviteMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    private $data = [];

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $project_title = $this->data['project']['title'];
        $inviter = $this->data['inviter'];

        return $this->view('emails.send.invite', ['data'  => $this->data])
            ->subject($inviter .' invited you to join the project "'. $project_title .'" on '. config('app.name'));
    }
}
