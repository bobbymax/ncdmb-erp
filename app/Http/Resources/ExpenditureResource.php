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
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'sub_budget_head_id' => $this->sub_budget_head_id,
            'department_id' => $this->department_id,
            'sub_budget_head_code' => $this->subBudgetHead->code,
            'sub_budget_head_name' => $this->subBudgetHead->name,
            'owners' => $this->department->code,
            'controller' => $this->controller->staff_no,
            'beneficiary' => $this->beneficiary,
            'claim_id' => $this->claim_id,
            'staffId' => $this->claim_id > 0 ? $this->claim->staff->staff_no : "",
            'batch_id' => $this->batch_id,
            'amount' => $this->amount,
            'approved_amount' => $this->approved_amount,
            'description' => $this->description,
            'additional_info' => $this->additional_info,
            'type' => $this->type,
            'payment_type' => $this->payment_type,
            'status' => $this->status,
            'approval_status' => $this->approval_status,
            'paid' => $this->refunds->count() > 0 ? $this->refunds->sum('amount') : 0,
            'stage' => $this->stage,
            'remark' => $this->remark,
            'closed' => $this->closed,
            'refunds' => $this->refunds && $this->refunds->sum('amount') == $this->amount,
            'created_at' => $this->created_at->format('d F, Y'),
            'updated_at' => $this->updated_at->format('d F, Y'),
            'fund' => new SubBudgetHeadResource($this->subBudgetHead)
        ];
    }
}
