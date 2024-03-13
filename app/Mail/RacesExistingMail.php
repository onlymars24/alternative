<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RacesExistingMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    private $status;
    private $points;
    private $orderId;
    private $phone;

    public function __construct($status, $points, $orderId, $phone)
    {
        $this->status = $status;
        $this->points = $points;
        $this->orderId = $orderId;
        $this->phone = $phone;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: $this->status.' попытка найти рейс для точек '.$this->points.'; ID заказа: '.$this->orderId.'; телефон: '.$this->phone
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
            view: 'mail.races_existing',
            with: [
                'status' => $this->status,
                'points' => $this->points,
                'orderId' => $this->orderId,
                'phone' => $this->phone,
                'whatsLink' => 'https://wa.me/'.preg_replace('/\D/', '', $this->phone)
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
