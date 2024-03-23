<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class SendGeneratedExportFile extends Notification implements ShouldQueue
{
    use Queueable;

    protected $link;
    protected $subject;
    protected $mail_description;
    protected $app_description;
    public $file_name;
    protected $header;
    protected $export_as;
    public $directory_name;

    /**
     * Create a new notification instance.
     *
     * @param array $data
     */
    public function __construct($data)
    {
        $this->directory_name = $data['directory_name'];
        $this->file_name = $data['file_name'];
        $this->subject = $data['subject'];
        $this->header = $data['header'];
        $this->export_as = $data['export_as'];
        $this->link = '';
        $this->app_description = $data['app_description'];
        $this->mail_description = $data['mail_description'];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        $via = [];

        switch ($notifiable->pref_notification_type) {
            case 'scheduled':
            case 'instant':
                $via = ['database', 'mail'];
                break;
        }

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        $file = Storage::disk('public')->path($this->directory_name . '/' .$this->file_name);

        $mime = 'application/pdf';
        if ($this->export_as == 'csv') {
            $mime = 'text/csv';
        }

        return (new MailMessage)
            ->subject($this->subject)
            ->view('export.send-report', [
                'header' => $this->header,
                'to_name' => $notifiable->name,
                'description' => $this->mail_description
            ])
            ->attach($file, [
                'as' => $this->file_name,
                'mime' => $mime,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        return [
            'subject' => $this->subject,
            'directory_name' => $this->directory_name,
            'file_name' => $this->file_name,
            'app_description' => $this->app_description,
            'mail_description' => $this->mail_description,
            'notified_to' => $notifiable->id,
            'link' => $this->link,
            'reference_type_id' => \App\Models\Notification::REFERENCE_TYPE_ID_SEND_REPORT,
            'reference_key' => null,
        ];
    }
}
