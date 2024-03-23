<?php

namespace App\Notifications;

use App\Models\StockTransfer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProductTransfer extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var string
     */
    protected $link;
    /**
     * @var string
     */
    protected $subject;
    /**
     * @var string
     */
    protected $mailDescription;
    /**
     * @var string
     */
    protected $appDescription;
    /**
     * @var array
     */
    protected $products;
    /**
     * @var mixed
     */
    protected $fromBranch;
    /**
     * @var mixed
     */
    protected $toBranch;
    /**
     * @var StockTransfer
     */
    protected $stockTransfer;

    /**
     * Create a new notification instance.
     *
     * @param StockTransfer $stockTransfer
     * @param array $data
     */
    public function __construct(StockTransfer $stockTransfer, array $data = [])
    {
        $this->stockTransfer = $stockTransfer;
        $this->products = $stockTransfer->stockTransferProducts;
        $this->subject = $data['subject'];
        $this->link =config('app.frontend_url'). $data['link'];
        $this->fromBranch = $data['fromBranch'];
        $this->toBranch = $data['toBranch'];
        $this->mailDescription = $data['mailDescription'];
        $this->appDescription = $data['appDescription'];
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
        return (new MailMessage)
            ->subject($this->subject)
            ->view('email_templates.stock-transfer', [
                'header' => $this->subject,
                'to_name' => $notifiable->name,
                'products' => $this->products,
                'from_branch' => $this->fromBranch,
                'to_branch' => $this->toBranch,
                'description' => $this->mailDescription,
                'frontend_url' => $this->link
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
            'app_description' => $this->appDescription,
            'mail_description' => $this->mailDescription,
            'notified_to' => $notifiable->id,
            'link' => $this->link,
            'reference_type_id' => \App\Models\Notification::REFERENCE_TYPE_ID_STOCK_TRANSFER,
            'reference_key' => $this->stockTransfer->id,
        ];
    }
}
