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
            'id' => (string) $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'assigned_to' => (string) $this->assigned_to,
            'created_by' => (string) $this->created_by,
            'developed_by' => (string) $this->developed_by,
            'tested_by' => (string) $this->tested_by,
            'parent_id' => (string) $this->parent_id,
            'due_date' => $this->due_date,
            'status' => $this->status,
            'logs' => TaskLogResource::collection($this->logs),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
