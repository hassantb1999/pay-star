<?php

namespace App\Modules\Banking\Requests;

use App\Modules\Banking\Models\Bank;
use Illuminate\Foundation\Http\FormRequest;

class StoreAccountRequest extends FormRequest
{


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'bank' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!in_array($value, Bank::getBanks())){
                        $fail('The ' . $attribute . ' is invalid');
                    }
                }
            ],
            'account_number' => 'required',
            'shaba_number' => 'required',
            'credit' => 'required|numeric|gt:0',
            'description' => 'sometimes',
        ];
    }
}
