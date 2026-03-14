@extends('layouts.app')

@section('title', 'メール認証案内画面')

@section('css')
    <link rel="stylesheet" href="{{ asset('/css/authentication.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/verify-email.css') }}">
@endsection

@section('content')
@include('components.header-simple')

<div class="authenticate verify">
    <p class="verify__message">
        登録していただいたメールアドレスに認証メールを送付しました。<br>
        メール認証を完了してください。
    </p>

    @if (session('status') === 'verification-link-sent')
        <p class="verify__status">
            認証メールを送信しました。
        </p>
    @endif

    <form method="POST" action="{{ route('verification.send') }}" class="verify__primary-form">
        @csrf
        <button type="submit" class="verify__primary-btn">
            認証はこちらから
        </button>
    </form>

    <form method="POST" action="{{ route('verification.send') }}" class="verify__resend-form">
        @csrf
        <button type="submit" class="verify__resend-link">
            認証メールを再送する
        </button>
    </form>
</div>
@endsection
