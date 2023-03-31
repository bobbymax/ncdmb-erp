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
            'controller' => $this->controller->firstname . " " . $this->controller->surname,
            'department_id' => $this->department_id,
            'department_code' => $this->department->code,
            'department_name' => $this->department->name,
            'directorate' => $this->department->directorate(),
            'code' => $this->code,
            'sub_budget_head_code' => $this->sub_budget_head_code,
            'sub_budget_head_name' => $this->subBudgetHead()->name ?? "",
            'amount' => $this->amount,
            'approved_amount' => $this->approved_amount,
            'no_of_payments' => $this->no_of_payments,
            'expenditures' => ExpenditureResource::collection($this->expenditures),
            'demand' => $this->demand && ($this->demand->id > 0),
            'stage' => $this->stage,
            'status' => $this->status,
            'closed' => $this->closed,
            'track' => $this->track,
            'created_at' => $this->created_at->format('d F, Y'),
            'updated_at' => $this->updated_at->format('d F, Y')
        ];
    }
}
