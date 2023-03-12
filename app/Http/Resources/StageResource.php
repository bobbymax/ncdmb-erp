<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'process_id' => $this->process_id,
            'role_id' => $this->role_id,
            'canEdit' => $this->canEdit == 1,
            'canQuery' => $this->canQuery == 1,
            'action' => $this->action,
            'order' => $this->order
        ];
    }
}
