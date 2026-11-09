<?php

namespace App\Mail;

use App\Models\Intervention;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InterventionAssignee extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Intervention $intervention)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Nouvelle intervention attribuée');
    }

    public function content(): Content
    {
        return new Content(text: 'emails.intervention-assignee');
    }
}