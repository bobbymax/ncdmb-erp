<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array|\JsonSerializable|\Illuminate\Contracts\Support\Arrayable
    {
        //  return parent::toArray($request);
        return [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'middlename' => $this->middlename,
            'surname' => $this->surname,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'score' => $this->score,
            'isAdministrator' => $this->isAdministrator,
            'department_name' => $this->record->department->name,
            'department_code' => $this->record->department->code,
            'department_id' => $this->record->department->id,
            'gradeLevel' => $this->record->level->key,
//            'organization' => $this->record->organization,
            'mobile' => $this->record->mobile,
            'designation' => $this->record->designation,
            'location' => $this->record->location,
            'dob' => $this->record->dob,
            'date_joined' => $this->record->date_joined,
            'type' => $this->record->type,
            'status' => $this->record->status,
        ];
    }
}
