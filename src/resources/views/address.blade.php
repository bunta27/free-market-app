@extends('layouts.app')

@section('title','住所変更')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/address.css') }}">
@endsection

@section('content')

@include('components.header')
<div class="container">
    <h1 class="container__title">住所の変更</h1>
    <form action="/purchase/address/{{$item->id}}" method="post" class="address">
        @csrf
        <label for="postcode" class="form__label">郵便番号</label>
        <input type="text" name="postcode" id="postcode" class="form__input" value="{{ $user->profile->postcode }}">
            @error('postcode')
                {{ $message }}
            @enderror

        <label for="address" class="form__label">住所</label>
        <input type="text" name="address" id="address" class="form__input" value="{{ $user->profile->address }}">
            @error('address')
                {{ $message }}
            @enderror

        <label for="building" class="form__label">建物名</label>
        <input type="text" name="building" id="building" class="form__input" value="{{ $user->profile->building }}">
            @error('building')
                {{ $message }}
            @enderror

        <button type="submit" class="form__btn">更新する</button>
    </form>
</div>
@endsection