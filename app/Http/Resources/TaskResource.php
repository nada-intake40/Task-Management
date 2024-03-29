<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id ,
            "title" => $this->title ,
            "description" => $this->description ,
            "status" => $this->status ,
            "due_date" => $this->due_date->format('Y-m-d H:i:s'),
            "assignee" => $this->employee ? [
                "id"  => $this->employee->id ,
                "full_name" => $this->employee->full_name ,
                "email" => $this->employee->email ,
            ] : null,

            "dependencies" => $this->taskDependencies ,
        ];
    }
}
