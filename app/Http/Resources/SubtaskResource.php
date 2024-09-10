<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubtaskResource extends JsonResource
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
            'task_id' => (string) $this->task_id,
            'title' => $this->title,
            'description' => $this->description,
            'assigned_to' => (string) $this->assigned_to,
            'developed_by' => (string) $this->developed_by,
            'tested_by' => (string) $this->tested_by,
            'due_date' => $this->due_date,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
