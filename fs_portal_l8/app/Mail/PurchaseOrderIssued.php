<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PurchaseOrderIssued extends Mailable
{
    use Queueable, SerializesModels;
    public $po;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
         $this->po = $po;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
        {
            return $this->subject('Purchase Order Issued')
                        ->view('emails.po_issued')
                        ->with(['po' => $this->po]);

        }
}
