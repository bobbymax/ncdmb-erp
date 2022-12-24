<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
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
            'claim_id' => $this->claim_id,
            'remuneration_id' => $this->remuneration_id,
            'remuneration' => $this->remuneration->name,
            'remuneration_child_id' => $this->remuneration_child_id,
            'category' => $this->remuneration_child_id > 0 ? $this->category->name : "",
            'from' => $this->from->format('Y-m-d'),
            'to' => $this->to->format('Y-m-d'),
            'description' => $this->description,
            'amount' => $this->amount,
        ];
    }
}
