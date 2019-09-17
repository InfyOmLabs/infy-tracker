<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StartTimer implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $timerData;

    /**
     * Create a new event instance.
     *
     * @param  array  $timerData
     */
    public function __construct($timerData = [])
    {
        $this->timerData = $timerData;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('stopwatch-event.'.getLoggedInUserId());
    }

    /**
     * @return array
     */
    public function broadcastWith()
    {
        return $this->timerData;
    }
}
