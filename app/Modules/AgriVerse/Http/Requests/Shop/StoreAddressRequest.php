<?php

namespace App\Modules\AgriVerse\Http\Requests\Shop;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'label' => 'required|string|max:50',
            'recipient_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'province' => 'required|string|max:100',
            'province_id' => 'nullable|integer',
            'district' => 'required|string|max:100',
            'district_id' => 'nullable|integer',
            'ghn_district_id' => 'nullable|integer',
            'ward' => 'required|string|max:100',
            'ward_code' => 'nullable|string|max:20',
            'ghn_ward_code' => 'nullable|string|max:20',
            'address_detail' => 'required|string|max:500',
            'is_default' => 'boolean',
        ];
    }
}
