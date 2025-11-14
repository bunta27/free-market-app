<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:1'],
            'description' => ['required', 'string'],
            'img_url' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:10240'],
            'condition_id' => ['required', 'exists:conditions,id'],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['integer', 'exists:categories,id'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '商品名を入力してください',
            'name.max'      => '商品名は255文字以内で入力してください',

            'price.required' => '販売価格を入力してください',
            'price.integer'  => '販売価格は数値で入力してください',
            'price.min'      => '販売価格は1円以上で入力してください',

            'description.required' => '商品説明を入力してください',

            'img_url.required' => '商品画像を選択してください',
            'img_url.image'    => '画像ファイルを選択してください',
            'img_url.mimes'    => '画像はjpeg、png、jpg形式でアップロードしてください',
            'img_url.max'      => '画像サイズは10MB以下にしてください',

            'condition_id.required' => '商品状態を選択してください',
            'condition_id.exists'   => '商品状態が正しく選択されていません。',

            'categories.required' => 'カテゴリーを選択してください',
            'categories.array'    => 'カテゴリーの指定が不正です',
            'categories.min'      => 'カテゴリーは1つ以上選択してください',
            'categories.*.exists' => '選択したカテゴリーが不正です',
        ];
    }
}
