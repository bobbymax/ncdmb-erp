<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JoiningResource extends JsonResource
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
            'training_id' => $this->training_id,
            'qualification_id' => $this->qualification_id,
            'learning_category_id' => $this->learning_category_id,
            'title' => $this->training->title,
            'course' => $this->learningCategory->name,
            'qualification' => $this->qualification->type,
            'start' => $this->start->format('d F, Y'),
            'end' => $this->end->format('d F, Y'),
            'facilitator' => $this->facilitator,
            'location' => $this->location,
            'category' => $this->category,
            'type' => $this->type,
            'resident' => $this->resident,
            'certificate' => $this->certificate,
            'status' => $this->status,
            'attended' => $this->attended == 1,
            'isArchived' => $this->isArchived == 1,
            'attendees' => UserResource::collection($this->staff),
            'created_at' => $this->created_at->format('d F, Y'),
            'updated_at' => $this->updated_at->format('d F, Y'),
        ];
    }
}
