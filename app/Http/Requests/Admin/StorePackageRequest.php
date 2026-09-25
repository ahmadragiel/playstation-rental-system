<?php

namespace App\Http\Requests\Admin;

use App\Models\Package;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StorePackageRequest extends FormRequest
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
            'facilities' => $this->normaliseFacilities($this->input('facilities')),
            'is_active' => $this->has('is_active')
                ? $this->boolean('is_active')
                : true,
            'is_featured' => $this->has('is_featured')
                ? $this->boolean('is_featured')
                : false,
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
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('packages', 'slug')->ignore($this->packageKey()),
            ],
            'duration_minutes' => ['required', 'integer', 'min:60', 'max:1440'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'description' => ['nullable', 'string', 'max:65535'],
            'facilities' => ['nullable', 'array', 'max:50'],
            'facilities.*' => ['string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
            'is_featured' => ['sometimes', 'boolean'],
        ];
    }

    protected function normaliseFacilities(mixed $facilities): mixed
    {
        if ($facilities === null || $facilities === '') {
            return null;
        }

        if (is_string($facilities)) {
            $facilities = preg_split('/[\r\n,]+/', $facilities) ?: [];
        }

        if (! is_array($facilities)) {
            return $facilities;
        }

        $items = [];
        $hasInvalidItem = false;
        foreach ($facilities as $facility) {
            if (! is_scalar($facility)) {
                $hasInvalidItem = true;

                continue;
            }

            $facility = trim((string) $facility);
            if ($facility !== '') {
                $items[] = $facility;
            }
        }

        if ($hasInvalidItem) {
            return $facilities;
        }

        return array_values(array_unique($items));
    }

    protected function packageKey(): int|string|null
    {
        foreach (['package', 'rentalPackage'] as $parameter) {
            $routeValue = $this->route($parameter);

            if ($routeValue instanceof Package || $routeValue instanceof Model) {
                return $routeValue->getKey();
            }

            if (is_numeric($routeValue)) {
                return $routeValue;
            }
        }

        return null;
    }
}
