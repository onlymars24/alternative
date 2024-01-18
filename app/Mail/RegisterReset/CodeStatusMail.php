<?php

namespace App\Mail\RegisterReset;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CodeStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $phone;
    protected $action;
    protected $correct;
    public function __construct($phone, $action, $correct)
    {
        $this->phone = $phone;
        $this->action = $action;
        $this->correct = $correct;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: $this->correct ? 
            ($this->action.' для '.$this->phone.' прошла успешно!') :
            ($this->action.' для '.$this->phone.' не прошла! Код неверный!')
            ,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'mail.RegisterReset.code_status',
            with: [
                'phone' => $this->phone,
                'action' => $this->action,
                'correct' => $this->correct,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}