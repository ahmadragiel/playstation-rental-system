<?php

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:100'],
            'customer_whatsapp' => ['required', 'string', 'regex:/^(?:\+?62|0)\d{8,14}$/'],
            'customer_email' => ['nullable', 'email', 'max:150'],
            'package_id' => [
                'required',
                Rule::exists('packages', 'id')->where('is_active', true),
            ],
            'playstation_unit_id' => [
                'required',
                Rule::exists('playstation_units', 'id')->where('is_active', true),
            ],
            'booking_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'duration_minutes' => ['nullable', 'integer', 'min:60', 'max:1440'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'customer_name' => 'nama pelanggan',
            'customer_whatsapp' => 'nomor WhatsApp',
            'customer_email' => 'email',
            'package_id' => 'paket',
            'playstation_unit_id' => 'unit PlayStation',
            'booking_date' => 'tanggal',
            'start_time' => 'jam mulai',
            'duration_minutes' => 'durasi',
            'notes' => 'catatan',
        ];
    }
}
