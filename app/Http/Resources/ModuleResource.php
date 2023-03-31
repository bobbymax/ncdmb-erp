<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ModuleResource extends JsonResource
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
            "name" => $this->name,
            "label" => $this->label,
            "code" => $this->code,
            "icon" => $this->icon,
            "url" => $this->url,
            "parentId" => $this->parentId,
            "parent" => $this->parentId > 0 ? $this->parent->label : "none",
            "type" => $this->type,
            "children" => $this->children,
            "roles" => $this->roles->pluck('label')->toArray(),
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at
        ];
    }
}
