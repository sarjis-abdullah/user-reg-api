<?php

namespace App\Events\Attachment;

use App\Models\Attachment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AttachmentCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Attachment
     */
    public Attachment $attachment;
    public array $options;

    /**
     * Create a new event instance.
     *
     * @param Attachment $attachment
     * @param array $options
     * @return void
     */
    public function __construct(Attachment $attachment, array $options = [])
    {
        $this->attachment = $attachment;
        $this->options = $options;
    }
}
