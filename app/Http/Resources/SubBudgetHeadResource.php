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
            "approved_amount" => $this->fund !== null ? $this->fund->approved_amount : 0,
            "booked_expenditure" => $this->fund !== null ? $this->fund->booked_expenditure : 0,
            "actual_expenditure" => $this->fund !== null ? $this->fund->actual_expenditure : 0,
            "booked_balance" => $this->fund !== null ? $this->fund->booked_balance : 0,
            "actual_balance" => $this->fund !== null ? $this->fund->actual_balance : 0,
            "created_at" => $this->created_at->format('d F, Y'),
            "updated_at" => $this->updated_at->format('d F, Y'),
        ];
    }
}
