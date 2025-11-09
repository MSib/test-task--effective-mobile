<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
    ];

    protected function title(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => strip_tags($value),
            set: fn(string $value) => strip_tags($value),
        );
    }

    protected function description(): Attribute
    {
        return Attribute::make(
            get: fn(string|null $value) => $value ? strip_tags($value) : null,
            set: fn(string|null $value) => $value ? strip_tags($value) : null,
        );
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn(int|null $value) => (bool) $value,
            set: fn(int|null $value) => $value ? 1 : 0,
        );
    }

    public static function rules(bool $forUpdate = false): array
    {
        $rules = [
            'title' => ($forUpdate ? 'sometimes|' : 'required|') . 'string|max:255|min:1',
            'description' => 'nullable|string|max:5000',
            'status' => 'boolean',
        ];
        return $rules;
    }

    public static function messages(): array
    {
        return [
            'title.required' => 'Task title is required',
            'title.string' => 'Task title must be a string',
            'title.min' => 'Task title is too short',
            'title.max' => 'Title should not exceed 255 characters',
            'description.max' => 'Description should not exceed 5000 characters',
            'status.boolean' => 'Status must be a boolean value',
        ];
    }
}
