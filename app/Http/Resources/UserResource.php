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
        return [
            'id' => $this->id,
            'staff_no' => $this->staff_no,
            'firstname' => $this->firstname,
            'middlename' => $this->middlename,
            'surname' => $this->surname,
            'name' => $this->firstname . " " . $this->surname,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'score' => $this->score,
            'isAdministrator' => $this->isAdministrator == 1,
            'department_name' => $this->department->name,
            'department_code' => $this->department->code,
            'department_id' => $this->department->id,
            'grade_level_id' => $this->grade_level_id,
            'gradeLevel' => $this->level->key,
            'mobile' => $this->mobile,
            'designation' => $this->designation,
            'location' => $this->location,
            'dob' => $this->dob,
            'roles' => $this->roles->pluck('label')->toArray(),
            'date_joined' => $this->date_joined,
            'type' => $this->type,
            'status' => $this->status,
        ];
    }
}
