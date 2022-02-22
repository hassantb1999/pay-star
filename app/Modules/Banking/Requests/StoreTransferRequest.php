<?php

namespace App\Modules\Banking\Requests;

use App\Modules\Banking\Models\Transfer;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransferRequest extends FormRequest
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
        $isPaya = (str_starts_with(request('destinationNumber'), 'IR'));

        $account = $this->route()->parameter('account');


        return [
            'description' => [
                'required',
                'string',
                'max:30',
                function ($attribute, $value, $fail) {
                    if (str_contains($value, '--')) {
                        $fail('The ' . $attribute . ' can not contain more than one "-" Continuous!!');
                    }
                }
            ],
            'amount' => [
                'required',
                function ($attribute, $value, $fail) use ($isPaya) {
                    if ($isPaya && $value > Transfer::PAYA_TRANSFER_MAX_LIMIT) {
                        $fail('The ' . $attribute . ' is more than maximum amount of paya');
                    }
                },
                function ($attribute, $value, $fail) use ($account) {
                    if ($value >= $account->credit) {
                        $fail('The ' . $attribute . ' is more than your account');
                    }
                },
            ],
            'destinationFirstname' => 'required|string|min:2|max:33',
            'destinationLastname' => 'required|string|min:2|max:33',
            'destinationNumber' => 'required|string|max:26',
            'paymentNumber' => 'sometimes|string|max:30',
            'reasonDescription' => 'sometimes|integer|max:19',
            'secondPassword' => 'sometimes|string'
        ];
    }
}
