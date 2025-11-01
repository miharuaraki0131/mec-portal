<?php

namespace App\Mail;

use App\Models\WorkflowApproval;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApprovalNotification extends Mailable
{
    use Queueable, SerializesModels;

    public WorkflowApproval $approval;

    public function __construct(WorkflowApproval $approval)
    {
        $this->approval = $approval;
    }

    public function envelope(): Envelope
    {
        $requestType = $this->approval->request_type === 'expense' ? '経費申請' : '出張申請';
        return new Envelope(
            subject: '【mec-portal】' . $requestType . 'が承認されました',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.approval-notification',
            with: [
                'approval' => $this->approval,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

