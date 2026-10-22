<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReminderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'event_id' => $this->event_id,
            'remind_at' => $this->remind_at,
            'channel' => $this->channel,
            'status' => $this->status,
            'sent_at' => $this->sent_at,
            'event' => EventResource::make($this->whenLoaded('event')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
