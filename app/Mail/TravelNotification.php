<?php

namespace App\Mail;

use App\Models\TravelRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class TravelNotification extends Mailable
{
    use Queueable, SerializesModels;

    public TravelRequest $travelRequest;
    public string $excelPath;

    /**
     * Create a new message instance.
     */
    public function __construct(TravelRequest $travelRequest, string $excelPath)
    {
        $this->travelRequest = $travelRequest;
        $this->excelPath = $excelPath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '【mec-portal】新しい出張申請が届きました',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.travel-notification',
            with: [
                'travelRequest' => $this->travelRequest,
                'applicant' => $this->travelRequest->user,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromStorageDisk('public', $this->excelPath)
                ->as('出張申請書_' . $this->travelRequest->user->user_code . '_' . $this->travelRequest->departure_date->format('Ymd') . '.xlsx')
                ->withMime('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
        ];
    }
}

