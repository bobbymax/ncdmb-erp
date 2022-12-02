<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpenditureResource extends JsonResource
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
            'sub_budget_head_id' => $this->sub_budget_head_id,
            'department_id' => $this->department_id,
            'sub_budget_head_code' => $this->subBudgetHead->code,
            'owners' => $this->department->code,
            'controller' => $this->controller->record->staffId,
            'beneficiary' => $this->beneficiary,
            'cash_advance_id' => $this->cash_advance_id,
            'batch_id' => $this->batch_id,
            'amount' => $this->amount,
            'approved_amount' => $this->approved_amount,
            'description' => $this->description,
            'additional_info' => $this->additional_info,
            'type' => $this->type,
            'payment_type' => $this->payment_type,
            'status' => $this->status,
            'closed' => $this->closed,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
