<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RequisitionResource extends JsonResource
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
            'approving_officer_id' => $this->approving_officer_id,
            'department_id' => $this->department_id,
            'department' => $this->department->code,
            'no_of_items' => $this->no_of_items,
            'status' => $this->status,
            'isArchived' => $this->isArchived,
            'created_at' => $this->created_at->format('d F, Y'),
            'updated_at' => $this->updated_at->format('d F, Y'),
            'requisitor_staff_no' => $this->requisitor->record->staffId,
            'requisitor_staff_name' => $this->requisitor->firstname . " " . $this->requisitor->surname,
            'manager_staff_no' => $this->approving_officer_id > 0 ? $this->lineManager->record->staffId : "",
            'manager_staff_name' => $this->approving_officer_id > 0 ? $this->lineManager->firstname . " " . $this->lineManager->surname : "",
            'store' => new StoreResource($this->stored),
            'items' => ItemResource::collection($this->items)
        ];
    }
}
