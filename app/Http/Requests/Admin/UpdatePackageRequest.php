<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class UpdatePackageRequest extends StorePackageRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['slug'] = [
            'required',
            'string',
            'max:255',
            Rule::unique('packages', 'slug')->ignore($this->packageKey()),
        ];

        return $rules;
    }
}
