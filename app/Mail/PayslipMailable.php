<?php

namespace MiSAKACHi\VERACiTY\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PayslipMailable extends Mailable implements ShouldQueue {
    use Queueable, SerializesModels;

    protected $attachmentData,
              $paySlipData;

    public function __construct( array $passedData, string $attachmentData ) {
        $this->attachmentData = $attachmentData;
        $this->paySlipData = $passedData;
    }

    public function build() {
        $fileName = 'PAYSLIP-' . $this->paySlipData['employeeNumber'] . '.pdf';

        return $this->subject( 'Employee Payslip' )
            ->view( 'Mail.PaySlipFormat' )
            ->with( 'paySlipData', $this->paySlipData )
            ->attach( $this->attachmentData, ['as' => $fileName, 'mime' => 'application/pdf'] );
    }
}
