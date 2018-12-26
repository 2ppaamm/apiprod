<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateDifficultyRequest extends FormRequest
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
            'difficulty'=>'required',
            'short_description'=>'required',
            'description'=>'required'
        ];
    }

    
    public function response(array $errors) {
        return response()->json(['message' => $errors,'code'=>422], 422);
    }
}
