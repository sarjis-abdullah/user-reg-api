<?php

namespace App\Notifications;

use App\Models\Branch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProductExpired extends Notification implements ShouldQueue
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
     * @var Branch
     */
    protected $branch;
    /**
     * @var array
     */
    protected $products;

    /**
     * Create a new notification instance.
     *
     * @param Branch $branch
     * @param $products
     * @param string $day
     */
    public function __construct(Branch $branch, $products, string $day)
    {
        $this->branch = $branch;
        $this->products = $products;
        $this->subject = 'Products Date Expiration';
        $this->link = sprintf('%s/products-expiration-report/', config('app.frontend_url'));

        $this->mailDescription = sprintf('Your branch has %s Products, which will be expired in next %s days.', count($products), $day);
        $this->appDescription = $this->mailDescription;
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
            ->view('email_templates.stock-expired', [
                'header' => $this->subject,
                'to_name' => $notifiable->name,
                'products' => $this->products,
                'branch_name' => $this->branch->name,
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
            'reference_type_id' => \App\Models\Notification::REFERENCE_TYPE_ID_PRODUCTS_EXPIRED,
            'reference_key' => $this->branch->id,
        ];
    }
}
