<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SensorDataUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $land_id;
    public $ph_value;
    public $moisture_value;
    public $temperature;
    public $humidity;
    public $timestamp;

    /**
     * Create a new event instance.
     */
    public function __construct($land_id, $data)
    {
        $this->land_id = $land_id;
        $this->ph_value = $data['ph_value'];
        $this->moisture_value = $data['moisture_value'];
        $this->temperature = $data['temperature'];
        $this->humidity = $data['humidity'];
        $this->timestamp = $data['timestamp'];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('land-monitoring'),
        ];
    }

    /**
     * Get the broadcast event name.
     */
    public function broadcastAs(): string
    {
        return 'sensor-data-updated';
    }
} 