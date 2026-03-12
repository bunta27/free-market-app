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
            'message' => ['nullable', 'string', 'max:1000', 'required_without:image'],
            'image'   => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048', 'required_without:message'],
        ];
    }

    public function messages()
    {
        return [
            'message.required_without' => 'メッセージまたは画像を入力してください。',
            'message.max'              => 'メッセージは1000文字以内で入力してください。',
            'image.required_without'   => 'メッセージまたは画像を入力してください。',
            'image.image'              => '画像ファイルを選択してください。',
            'image.mimes'              => '画像は jpeg / png / jpg / gif 形式でアップロードしてください。',
            'image.max'                => '画像サイズは2MB以下にしてください。',
        ];
    }
}