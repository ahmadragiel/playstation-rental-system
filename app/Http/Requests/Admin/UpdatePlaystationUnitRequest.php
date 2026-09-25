<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class UpdatePlaystationUnitRequest extends StorePlaystationUnitRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['code'] = [
            'required',
            'string',
            'max:32',
            Rule::unique('playstation_units', 'code')->ignore($this->unitKey()),
        ];

        return $rules;
    }
}
