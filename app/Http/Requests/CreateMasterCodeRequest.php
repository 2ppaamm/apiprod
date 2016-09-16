<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateMasterCodeRequest extends Request
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
            'house_id' => 'required',
            'user_id' => 'required'
        ];
    }

    public function response(array $errors) {
        return response()->json(['message' => $errors,'code'=>422], 422);
    }
}
