<?php

namespace App\Http\Requests\Admin;

use App\Enums\UnitStatus;
use App\Models\PlaystationUnit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePlaystationUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $code = $this->input('code');
        $name = $this->input('name');
        $condition = $this->input('condition', 'Good');

        $this->merge([
            'code' => is_string($code) ? trim($code) : $code,
            'name' => is_string($name) ? trim($name) : $name,
            'condition' => is_string($condition) && trim($condition) !== ''
                ? trim($condition)
                : 'Good',
            'status' => $this->input('status', UnitStatus::Available->value),
            'is_active' => $this->has('is_active')
                ? $this->boolean('is_active')
                : true,
        ]);
    }

    public function rules(): array
    {
        return [
            'playstation_type_id' => [
                'required',
                'integer',
                Rule::exists('playstation_types', 'id'),
            ],
            'code' => [
                'required',
                'string',
                'max:32',
                Rule::unique('playstation_units', 'code')->ignore($this->unitKey()),
            ],
            'name' => ['required', 'string', 'max:255'],
            'condition' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::enum(UnitStatus::class)],
            'location' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:65535'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    protected function unitKey(): int|string|null
    {
        foreach (['unit', 'playstationUnit', 'playstation_unit'] as $parameter) {
            $routeValue = $this->route($parameter);

            if ($routeValue instanceof PlaystationUnit || $routeValue instanceof Model) {
                return $routeValue->getKey();
            }

            if (is_numeric($routeValue)) {
                return $routeValue;
            }
        }

        return null;
    }
}
