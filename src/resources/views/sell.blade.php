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
        <label class="form__label">カテゴリー</label>
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

        <label for="condition" class="form__label">商品の状態</label>

            <div class="custom-select">
                <select name="condition_id" id="condition" class="custom-select__real">
                    <option value="" selected disabled>選択してください</option>
                    @foreach($conditions as $condition)
                        <option value="{{ $condition->id }}">{{ $condition->condition }}</option>
                    @endforeach
                </select>

                <button type="button" class="custom-select__trigger">
                    <span class="custom-select__label">選択してください</span>
                    <span class="select-arrow">
                        @include('components.svg.arrow')
                    </span>
                </button>

                <ul class="custom-select__options">
                    @foreach($conditions as $condition)
                        <li class="custom-select__option"
                            data-value="{{ $condition->id }}">
                            {{ $condition->condition }}
                        </li>
                    @endforeach
                </ul>
            </div>

            @error('condition_id')
                <div class="form__error">{{ $message }}</div>
            @enderror

        <h2 class="heading__name">商品名と説明</h2>

        <label for="name" class="form__label">商品名</label>
        <input type="text" name="name" id="name" class="input">
            @error('name')
                <div class="form__error">{{ $message }}</div>
            @enderror

        <label for="brand" class="form__label">ブランド名</label>
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
        <div class="price-wrapper">
            <span class="yen">¥</span>
            <input type="number" name="price" id="price" class="input price-input">
        </div>
            @error('price')
                <div class="form__error">{{ $message }}</div>
            @enderror

        <button type="submit" class="form__btn">出品する</button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const fileInput      = document.getElementById('target');
            const previewImage   = document.getElementById('previewImage');
            const previewWrapper = document.querySelector('.sell__img-preview');
            const imgInner       = document.querySelector('.sell__img-inner');

            if (fileInput) {
                fileInput.addEventListener('change', (e) => {
                    const file = e.target.files[0];

                    if (!file) {
                        previewImage.src = '';
                        previewImage.classList.add('is-hidden');
                        previewWrapper.classList.remove('has-image');
                        imgInner.classList.remove('has-image');
                        return;
                    }

                    const reader = new FileReader();

                    reader.onload = (event) => {
                        previewImage.src = event.target.result;
                        previewImage.classList.remove('is-hidden');

                        previewWrapper.classList.add('has-image');
                        imgInner.classList.add('has-image');
                    };

                    reader.readAsDataURL(file);
                }, false);
            }

            const customSelects = document.querySelectorAll('.custom-select');

            customSelects.forEach((cs) => {
                const trigger    = cs.querySelector('.custom-select__trigger');
                const label      = cs.querySelector('.custom-select__label');
                const options    = cs.querySelectorAll('.custom-select__option');
                const realSelect = cs.querySelector('.custom-select__real');

                trigger.addEventListener('click', () => {
                    cs.classList.toggle('is-open');
                });

                options.forEach((opt) => {
                    opt.addEventListener('click', () => {
                        const value = opt.dataset.value;
                        const text  = opt.textContent.trim();

                        label.textContent = text;

                        options.forEach(o => o.classList.remove('is-selected'));
                        opt.classList.add('is-selected');

                        realSelect.value = value;
                        realSelect.dispatchEvent(new Event('change'));

                        cs.classList.remove('is-open');
                    });
                });
            });

                    document.addEventListener('click', (e) => {
                        customSelects.forEach((cs) => {
                            if (!cs.contains(e.target)) {
                                cs.classList.remove('is-open');
                            }
                        });
                    });
                });
    </script>
</div>
@endsection