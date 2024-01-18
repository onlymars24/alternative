<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReturnMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $tickets;

    public function __construct($tickets)
    {
        $this->tickets = $tickets;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Возврат '.$this->tickets[0]->order_id.': '.$this->tickets[0]->raceName.' '.$this->tickets[0]->dispatchDate,
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
            view: 'mail.return',
            with: [
                'tickets' => $this->tickets
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
        $tempFiles = [];
        foreach($this->tickets as $ticket){
            $tempFiles[] = Attachment::fromPath('tickets/'.$ticket->hash.'_r.pdf')->as('Место '.$ticket->seat.'.pdf')
            ->withMime('application/pdf');
        }
        return $tempFiles;
    }
}
