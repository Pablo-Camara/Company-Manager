<?php

namespace App\Mail;

use App\Models\Anomaly;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AnomalyReport extends Mailable
{
    use Queueable, SerializesModels;

    public $anomaly;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Anomaly $anomaly)
    {
        $this->anomaly = $anomaly;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(__('New anomaly reported'))->markdown('emails.anomalies.new');
    }
}
