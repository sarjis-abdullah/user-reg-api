<?php

namespace App\Notifications;

use App\Models\Stock;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StockAlert extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Stock
     */
    protected $stock;
    /**
     * @var int|mixed|string
     */
    protected $product;
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
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Stock $stock)
    {
        $this->stock = $stock;
        $this->product = $stock->product;
        $this->subject = 'Product Stock Alert';
        $this->link = sprintf('%s/products/product-details/?id=%s', config('app.frontend_url'), $this->product->id);

        $this->mailDescription = '';
        $this->appDescription = sprintf('%s has currently %s quantity at %s', $this->product->name, $stock->quantity, $stock->branch->name);
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
            ->view('email_templates.stock-alert', [
                'header' => 'Stock Alert',
                'to_name' => $notifiable->name,
                'quantity' => $this->stock->quantity,
                'product_name' => $this->product->name,
                'branch_name' => $this->stock? $this->stock->branch->name : '',
                'description' => $this->mailDescription,
                'frontend_url' => $this->link,
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
            'reference_type_id' => \App\Models\Notification::REFERENCE_TYPE_ID_STOCK,
            'reference_key' => $this->stock->id,
        ];
    }
}
