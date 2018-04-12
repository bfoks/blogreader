<?php

namespace App\Events;

use App\BlogIndexingProgress;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class BlogIndexingProgressUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $percentageIndexingProgress;

    /**
     * Create a new event instance.
     *
     * @param BlogIndexingProgress $blogIndexingProgress
     */
    public function __construct(BlogIndexingProgress $blogIndexingProgress)
    {
        $this->percentageIndexingProgress = $blogIndexingProgress->getPercentageProgress();
    }

    public function broadcastAs()
    {
        return 'status-updated';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('blog-indexing');
    }
}
