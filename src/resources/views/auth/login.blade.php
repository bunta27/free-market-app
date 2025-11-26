@extends('layouts.app')

@section('title','ログイン')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/authentication.css') }}">
@endsection

@section('content')

@include('components.header')
<div class="container">
    <h1 class="container__title">ログイン</h1>
    <form action="{{ route('login') }}" method="post" class="authenticate" novalidate>
        @csrf
        <div class="form__group">
            <label for="email" class="form__label">メールアドレス</label>
            <input type="text" name="email" id="email" class="form__input">
                @if($errors->has('email'))
                    <div class="form__error">{{ $errors->first('email') }}</div>
                @endif
        </div>

        <div class="form__group">
            <label for="password" class="form__label">パスワード</label>
            <input type="password" name="password" id="password" class="form__input">
                @error('password')
                    <div class="form__error">{{ $message }}</div>
                @enderror
        </div>

        <button type="submit" class="form__btn">ログインする</button>

        <a href="{{ route('register') }}" class="link">会員登録はこちら</a>
    </form>