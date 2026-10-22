<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImportantDateResource extends JsonResource
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
            'contact_id' => $this->contact_id,
            'title' => $this->title,
            'date' => $this->date?->toDateString(),
            'repeat_yearly' => $this->repeat_yearly,
            'contact' => ContactResource::make($this->whenLoaded('contact')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
