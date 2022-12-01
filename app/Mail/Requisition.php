<?php

namespace App\Mail;

use App\Models\Requisition as ModelsRequisition;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Requisition extends Mailable
{
    use Queueable, SerializesModels;

    public $requisition;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ModelsRequisition $requisition)
    {
        $this->requisition = $requisition;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(__('New requisition'))->markdown('emails.requisitions.new');
    }
}
