<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubBudgetHeadResource extends JsonResource
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
            "id" => $this->id,
            "budget_head_id" => $this->budget_head_id,
            "budget_head_name" => $this->budgetHead->name,
            "budget_head_code" => $this->budgetHead->budgetId,
            "department_id" => $this->department_id,
            "budget_owner" => $this->department->code,
            "code" => $this->code,
            "name" => $this->name,
            "label" => $this->label,
            "type" => $this->type,
            "status" => $this->status,
            "logistics" => $this->logistics,
            "approved_amount" => $this->fund->approved_amount,
            "booked_expenditure" => $this->fund->booked_expenditure,
            "actual_expenditure" => $this->fund->actual_expenditure,
            "booked_balance" => $this->fund->booked_balance,
            "actual_balance" => $this->fund->actual_balance,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
