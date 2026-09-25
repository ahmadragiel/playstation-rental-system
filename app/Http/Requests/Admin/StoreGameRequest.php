<?php

namespace App\Http\Requests\Admin;

use App\Models\Game;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreGameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $name = $this->input('name');
        $slug = $this->input('slug');
        $slugSource = is_string($slug) && trim($slug) !== ''
            ? trim($slug)
            : (is_string($name) ? trim($name) : $name);

        $this->merge([
            'name' => is_string($name) ? trim($name) : $name,
            'slug' => is_scalar($slugSource) ? Str::slug((string) $slugSource) : $slugSource,
            'is_active' => $this->has('is_active')
                ? $this->boolean('is_active')
                : true,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('games', 'slug')->ignore($this->gameKey()),
            ],
            'genre' => ['nullable', 'string', 'max:255'],
            'platform' => ['nullable', 'string', 'max:255'],
            'player_count' => ['nullable', 'string', 'max:32'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'description' => ['nullable', 'string', 'max:65535'],
            'unit_ids' => ['nullable', 'array', 'max:100'],
            'unit_ids.*' => ['integer', 'exists:playstation_units,id'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    protected function gameKey(): int|string|null
    {
        foreach (['game', 'title'] as $parameter) {
            $routeValue = $this->route($parameter);

            if ($routeValue instanceof Game || $routeValue instanceof Model) {
                return $routeValue->getKey();
            }

            if (is_numeric($routeValue)) {
                return $routeValue;
            }
        }

        return null;
    }
}
