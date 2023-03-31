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
            'role_name' => $this->role->name,
            'office' => $this->office,
            'label' => $this->label,
            'canEdit' => $this->canEdit == 1,
            'canQuery' => $this->canQuery == 1,
            'accounts' => $this->accounts == 1,
            'action' => $this->action,
            'order' => $this->order
        ];
    }
}
