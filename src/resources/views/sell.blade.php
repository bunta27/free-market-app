@extends('layouts.app')

@section('title','商品出品ページ')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/sell.css') }}">
@endsection

@section('content')

@include('components.header')
<div class="container">
    <h1 class="container__title">商品の出品</h1>
    <form action="{{ route('items.sell.create') }}" method="post" class="sell" enctype="multipart/form-data">
        @csrf
        <label for="img_url" class="form__label">商品画像</label>
        <div class="sell__img">
            <div class="sell__img-inner">
                <label class="btn2">画像を選択する
                    <input type="file" name="img_url" id="target"
                        class="btn2__input" accept="image/jpeg,image/png,image/jpg">
                </label>

                <div class="sell__img-preview">
                    <img id="previewImage" class="sell__img-preview-image is-hidden" src="" alt="プレビュー画像">
                </div>
            </div>
        </div>

        @error('img_url')
            <div class="form__error">{{ $message }}</div>
        @enderror

        <h2 class="heading__name">商品の詳細</h2>
        <label for="category" class="form__label">カテゴリー</label>
        <div class="sell__categories">
            @foreach($categories as $category)
            <div class="sell__category">
                <input class="sell__check" type="checkbox" name="categories[]" value="{{ $category->id }}" id="{{ $category->id }}">
                <label for="{{ $category->id }}" class="sell__check-label">{{ $category->category }}</label>
            </div>
            @endforeach
        </div>
            @error('categories')
                <div class="form__error">{{ $message }}</div>
            @enderror

        <label for="status" class="form__label">商品の状態</label>
        <select name="condition_id" id="status" class="sell__select input">
            <option hidden>選択してください</option>
            @foreach($conditions as $condition)
            <option value="{{ $condition->id }}">{{ $condition->condition }}</option>
            @endforeach
        </select>
            @error('condition_id')
                <div class="form__error">{{ $message }}</div>
            @enderror

        <h2 class="heading__name">商品名と説明</h2>

        <label for="name" class="form__label">商品名</label>
        <input type="text" name="name" id="name" class="input">
            @error('name')
                <div class="form__error">{{ $message }}</div>
            @enderror

        <label for="name" class="form__label">ブランド名</label>
        <input type="text" name="brand" id="brand" class="input">
            @error('brand')
                <div class="form__error">{{ $message }}</div>
            @enderror

        <label for="description" class="form__label">商品の説明</label>
        <textarea name="description" id="description" class="textarea"></textarea>
            @error('description')
                <div class="form__error">{{ $message }}</div>
            @enderror

        <label for="price" class="form__label">販売価格</label>
        <input type="number" name="price" id="price" class="input">
            @error('price')
                <div class="form__error">{{ $message }}</div>
            @enderror

        <button type="submit" class="form__btn">出品する</button>
    </form>

    <script>
        const fileInput = document.getElementById('target');
        const previewImage = document.getElementById('previewImage');

        fileInput.addEventListener('change', function (e) {
            const file = e.target.files[0];

            if (!file) {
                previewImage.src = '';
                previewImage.classList.add('is-hidden');
                return;
            }

            const reader = new FileReader();

            reader.onload = function (e) {
                previewImage.src = e.target.result;
                previewImage.classList.remove('is-hidden');

                document.querySelector('.sell__img-preview').classList.add('has-image');
                document.querySelector('.sell__img-inner').classList.add('has-image');
            };

            reader.readAsDataURL(file);
        }, false);
    </script>
</div>
@endsection