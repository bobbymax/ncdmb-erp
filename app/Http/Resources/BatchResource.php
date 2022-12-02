<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BatchResource extends JsonResource
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
            'department_id' => $this->department_id,
            'department_code' => $this->department->code,
            'code' => $this->code,
            'sub_budget_head_code' => $this->sub_budget_head_code,
            'amount' => $this->amount,
            'approved_amount' => $this->approved_amount,
            'no_of_payments' => $this->no_of_payments,
            'expenditures' => $this->expenditures,
            'stage' => $this->stage,
            'status' => $this->status,
            'closed' => $this->closed,
            'created_at' => $this->created_at->format('d F, Y'),
            'updated_at' => $this->updated_at->format('d F, Y')
        ];
    }
}
