<?php

namespace App\Mail;

use App\Models\Persona;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistroConfirmacionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Persona $persona,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bienvenido a ' . config('app.name', 'Control de Accesos'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registro-confirmacion',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
