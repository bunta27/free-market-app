@extends('layouts.app')

@section('title','会員登録')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/authentication.css') }}">
@endsection

@section('content')

@include('components.header')
<div class="container">
    <h1 class="container__title">会員登録</h1>
    <form action="{{ route('register') }}" method="post" class="authenticate">
        @csrf
        <label for="name" class="form__label">ユーザー名</label>
        <input type="text" name="name" id="name" class="form__input">
            @error('name')
                {{ $message }}
            @enderror

        <label for="email" class="form__label">メールアドレス</label>
        <input type="email" name="email" id="email" class="form__input">
            @error('email')
                {{ $message }}
            @enderror

        <label for="password" class="form__label">パスワード</label>
        <input type="password" name="password" id="password" class="form__input">
            @error('password')
                {{ $message }}
            @enderror

        <label for="password-confirm" class="form__label">確認用パスワード</label>
        <input type="password" name="password_confirmation" id="password-confirm" class="form__input">
            @error('password_confirmation')
                {{ $message }}
            @enderror

        <button type="submit" class="form__btn">登録する</button>

        <a href="{{ route('login') }}" class="link">ログインはこちら</a>
    </form>
</div>
@endsection