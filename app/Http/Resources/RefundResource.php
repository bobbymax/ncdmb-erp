<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RefundResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array|\JsonSerializable|\Illuminate\Contracts\Support\Arrayable
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'department_id' => $this->department_id,
            'expenditure_id' => $this->expenditure_id,
            'controller' => $this->controller->firstname . " " . $this->controller->surname,
            'department_name' => $this->department->name,
            'department_code' => $this->department->code,
            'sub_budget_head_id' => $this->sub_budget_head_id,
            'sub_budget_head_code' => $this->sub_budget_head_id > 0 ? $this->subBudgetHead->code : "",
            'sub_budget_head_name' => $this->sub_budget_head_id > 0 ? $this->subBudgetHead->name : "",
            'amount' => $this->expenditure->amount,
            'beneficiary' => $this->expenditure->beneficiary,
            'description' => $this->description,
            'remark' => $this->remark,
            'status' => $this->status,
            'closed' => $this->closed,
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
        ];
    }
}
