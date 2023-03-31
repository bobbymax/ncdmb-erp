<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TrackResource extends JsonResource
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
            'department_id' => $this->department_id,
            'department_name' => $this->department->name,
            'department_code' => $this->department->code,
            'stage_id' => $this->stage_id,
            'office' => $this->stage->office,
            'label' => $this->stage->label,
            'code' => $this->code,
            'parent' => $this->trackable->id,
            'codeId' => $this->trackable->code,
            'entries' => EntryResource::collection($this->entries),
            'trackable_type' => $this->trackable_type,
            'state' => $this->state,
            'closed' => $this->closed == 1,
            'created_at' => $this->created_at->format('d F, Y'),
            'updated_at' => $this->updated_at->format('d F, Y'),
        ];
    }
}
