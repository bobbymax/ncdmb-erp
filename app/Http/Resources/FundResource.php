<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FundResource extends JsonResource
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
            'sub_budget_head_id' => $this->sub_budget_head_id,
            'sub_budget_head_name' => $this->subBudgetHead->name,
            'sub_budget_head_code' => $this->subBudgetHead->code,
            'budget_owner' => $this->subBudgetHead->department->code,
            'type' => $this->subBudgetHead->type,
            'logistics' => $this->subBudgetHead->logistics,
            'approved_amount' => $this->approved_amount,
            'booked_expenditure' => $this->booked_expenditure,
            'booked_balance' => $this->booked_balance,
            'actual_expenditure' => $this->actual_expenditure,
            'actual_balance' => $this->actual_balance,
            'exhausted' => $this->exhausted,
            'year' => $this->year,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
