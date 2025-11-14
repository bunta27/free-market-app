@extends('layouts.app')

@section('title','プロフィール設定')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/profile.css') }}">
@endsection

@section('content')

@include('components.header')
<div class="container">
    <h1 class="container__title">プロフィール設定</h1>
    <form action="{{ route('profile.update') }}" method="post" class="profile" enctype="multipart/form-data">
        @csrf
        <div class="user">
            <div class="user__img">
                @if($profile && $profile->img_url)
                    <img id="myImage" class="user__icon" src="{{ Storage::url($profile->img_url) }}" alt="プロフィール画像">
                @else
                    <img id="myImage" class="user__icon" src="{{ asset('/img/icon.png') }}" alt="デフォルト画像">
                @endif
            </div>
            <div class="profile__user-btn">>
                <label class="btn2">画像を選択する
                    <input type="file" name="img_url" id="target" class="btn2-input" accept="image/jpeg,image/png,image/jpg">
                </label>
                @error('img_url')
                    {{ $message }}
                @enderror
            </div>
        </div>

        <label for="name" class="form__label">ユーザー名</label>
        <input type="text" name="name" id="name" class="form__input" value="{{ Auth::user()->name }}">
            @error('name')
                {{ $message }}
            @enderror

        <label for="postcode" class="form__label">郵便番号</label>
        <input type="postcode" name="postcode" id="postcode" class="form__input" value="{{ $profile ? $profile->postcode : '' }}">
            @error('postcode')
                {{ $message }}
            @enderror

        <label for="address" class="form__label">住所</label>
        <input type="text" name="address" id="address" class="form__input" value="{{ $profile ? $profile->address : '' }}">
            @error('address')
                {{ $message }}
            @enderror

        <label for="building" class="form__label">建物名</label>
        <input type="text" name="building" id="building" class="form__input" value="{{ $profile ? $profile->building : '' }}">

        <button type="submit" class="form__btn">更新する</button>
    </form>

    <script>
        const fileInput = document.getElementById('target');

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('myImage');
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        },false);
    </script>
</div>
@endsection