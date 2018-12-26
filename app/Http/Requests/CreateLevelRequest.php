<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateLevelRequest extends FormRequest
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
            'level'=>'required',
            'description'=>'required',
            'age'=>'required|digits_between:0,999',
            'start_maxile_level'=>'required|integer|between:0,9999',
            'end_maxile_level'=>'required|integer|between:100,9999|gt:start_maxile_level'
        ];
    }

    
    public function response(array $errors) {
        return response()->json(['message' => $errors,'code'=>422], 422);
    }
}
