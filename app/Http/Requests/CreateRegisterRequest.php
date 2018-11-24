<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateRegisterRequest extends Request
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
            'role' => 'required|exists:roles,role',
            'transaction_id' => 'required',
            'places_alloted' => 'required|integer',
            'amount_paid' => 'required|regex:/^\d*(\.\d{2})?$/',
            'currency_code' => 'required|min:3|max:3',
            'house_id' => 'required|exists:houses,id'
        ];
    }

    public function response(array $errors) {
        return response()->json(['message' => $errors,'code'=>422], 422);
    }

}
