<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
//        return parent::toArray($request);
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'storeable_id' => $this->storeable_id,
            'storeable_type' => $this->storeable_type,
            'closed' => $this->closed,
            'store_manager_id' => $this->storeManager->record->staffId,
            'store_manager_name' => $this->storeManager->firstname . " " . $this->storeManager->surname,
            'created_at' => $this->created_at->format('d F, Y'),
            'updated_at' => $this->updated_at->format('d F, Y')
        ];
    }
}
