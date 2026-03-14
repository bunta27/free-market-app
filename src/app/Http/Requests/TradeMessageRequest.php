<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TradeMessageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'message' => ['required', 'string', 'max:400'],
            'image'   => ['nullable', 'image', 'mimes:jpeg,png', 'max:2048'],
        ];
    }

    public function messages()
    {
        return [
            'message.required' => '本文を入力してください。',
            'message.max'      => '本文は400文字以内で入力してください。',
            'image.image'      => '画像ファイルを選択してください。',
            'image.mimes'      => '「.png」または「.jpeg」形式でアップロードしてください',
            'image.max'        => '画像サイズは2MB以下にしてください',
        ];
    }
}