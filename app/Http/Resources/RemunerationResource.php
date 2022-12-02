<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RemunerationResource extends JsonResource
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
            'name' => $this->name,
            'label' => $this->label,
            'parentId' => $this->parentId,
            'type' => $this->type,
            'category' => $this->category,
            'parent_name' => $this->parentId > 0 ? $this->parent->name : "",
            'children' => $this->children,
            'settlements' => $this->settlements,
            'no_of_days' => $this->no_of_days,
            'isDeactivated' => $this->isDeactivated,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
