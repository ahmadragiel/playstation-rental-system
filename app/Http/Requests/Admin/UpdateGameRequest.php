<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class UpdateGameRequest extends StoreGameRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['slug'] = [
            'required',
            'string',
            'max:255',
            Rule::unique('games', 'slug')->ignore($this->gameKey()),
        ];

        return $rules;
    }
}
