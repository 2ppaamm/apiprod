<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class StoreMasterCodeRequest extends Request
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
            'firstname' => 'string | required',
            'lastname'=> 'string | required',
            'mastercode' => 'numeric | required',
            'date_of_birth' => 'required | date | before: yesterday'
        ];
    }

    public function response(array $errors) {
        return response()->json(['message' => $errors,'code'=>422], 422);
    }
}
