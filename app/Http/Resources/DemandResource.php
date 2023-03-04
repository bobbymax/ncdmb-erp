<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DemandResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array|\JsonSerializable|\Illuminate\Contracts\Support\Arrayable
    {
//        return parent::toArray($request);

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'department_id' => $this->department_id,
            'batch_id' => $this->batch_id,
            'controller' => $this->controller->firstname . " " . $this->controller->surname,
            'department_name' => $this->department->name,
            'department_code' => $this->department->code,
            'batch_no' => $this->batch->code,
            'amount' => $this->batch->amount,
            'description' => $this->description,
            'remark' => $this->remark,
            'status' => $this->status,
            'isArchived' => $this->isArchived == 1,
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
        ];
    }
}
