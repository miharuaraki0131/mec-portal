<?php

namespace App\Mail;

use App\Models\Expense;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ExpenseNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Expense $expense,
        public string $excelPath
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $requestType = $this->expense->isTransportation() ? '交通費申請' : '経費申請';
        return new Envelope(
            subject: '【mec-portal】新しい' . $requestType . 'が届きました',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.expense-notification',
            with: [
                'expense' => $this->expense,
                'applicant' => $this->expense->user,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        // Excelファイルは添付せず、ポータル上でダウンロードできるようにする
        return [];
    }
}
