<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;

class SendDocument extends Mailable
{
    use Queueable, SerializesModels;

    protected $type;

    protected $info;

    protected $model;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($info, $type, Model $model)
    {
        $this->info = $info;
        $this->model = $model;
        $this->type = $type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $settings = settings()->getMany([
            'sent_from_name', 'sent_from_email',
            'global_bcc_email'
        ]);

        $bcc = [$this->info['bcc']];

        if(!is_null($settings['global_bcc_email'])) {
            $bcc[] = $settings['global_bcc_email'];
        }

        $pdf = pdfRaw('docs.'.$this->type, $this->model);

        $fileName = time().'-'.$this->model->number.'.pdf';

        return $this
            ->from($settings['sent_from_email'], $settings['sent_from_name'])
            ->subject($this->info['subject'])
            ->to($this->info['to'])
            ->bcc($bcc)
            ->view('email.document', [
                'data' => $this->info
            ])
            ->attachData($pdf->Output('', 'S'), $fileName,[
                'mime' => 'application/pdf',
            ]);
    }
}
