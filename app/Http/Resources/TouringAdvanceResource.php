<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TouringAdvanceResource extends JsonResource
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
            'claim_id' => $this->claim_id,
            'beneficiary' => $this->claim->staff->firstname . " " . $this->claim->staff->surname,
            'reference_no' => $this->claim->reference_no,
            'title' => $this->claim->title,
            'start' => $this->claim->start->format('Y-m-d'),
            'end' => $this->claim->end->format('Y-m-d'),
            'status' => $this->status,
            'total_amount' => $this->claim->total_amount,
            'spent_amount' => $this->claim->spent_amount,
            'retired' => $this->claim->retired == 1,
            'claim_status' => $this->claim->status,
            'created_at' => $this->created_at->format('d F, Y'),
            'updated_at' => $this->updated_at->format('d F, Y')
        ];
    }
}
