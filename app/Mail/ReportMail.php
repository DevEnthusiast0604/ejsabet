<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

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
        return $this->view('emails.report')->from(config('mail.from.address'), config('mail.from.name'))
            ->subject($this->data['subject'])
            ->attach($this->data['attachment'], [
                'as' =>  $this->data['file_name'],
                'mime' => 'application/pdf',
            ]);
    }
}
