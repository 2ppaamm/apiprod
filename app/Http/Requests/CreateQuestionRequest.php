<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateQuestionRequest extends Request
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
            'skill_id' => 'required',
            'difficulty_id'=>'required',
            'question'=> 'required_without:question_image',
            'question_image'=> 'required_without:question|mimes:png,jpeg,gif',
            'answer0_image' => 'required_without:answer0',
            'answer1_image' => 'required_without:answer1',
            'answer2_image' => 'required_without:answer2',
            'answer3_image' => 'required_without:answer3',
            'answer3' => 'required_without:answer3_image',
            'answer2' => 'required_without:answer2_image',
            'answer1' => 'required_without:answer1_image',
            'answer0' => 'required_without:answer0_image',
            'correct_answer' => 'required'
        ];
    }

    public function response(array $errors){
        return response()->json(['message' => $errors,'code'=>422], 422);
    }
}
