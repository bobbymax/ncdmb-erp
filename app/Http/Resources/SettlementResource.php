<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SettlementResource extends JsonResource
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
            'remuneration_id' => $this->remuneration_id,
            'grade_level_id' => $this->grade_level_id,
            'amount' => $this->amount,
            'grade_level_name' => $this->gradeLevel->key,
            'remuneration_name' => $this->remuneration->name
        ];
    }
}
