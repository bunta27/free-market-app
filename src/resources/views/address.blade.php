@extends('layouts.app')

@section('title','住所変更')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/address.css') }}">
@endsection

@section('content')

@include('components.header')
<div class="container">
    <h1 class="container__title">住所の変更</h1>
    <form action="{{ route('purchase.address.update', ['item_id' => $item_id]) }}" method="post" class="address">
        @csrf

        <input type="hidden" name="item_id" value="{{ $item_id }}">

        <div class="form__group form__group--postcode">
            <label for="postcode" class="form__label">郵便番号</label>
            <input type="text" name="postcode" id="postcode" class="form__input" value="{{ $user->profile->postcode }}">
            @error('postcode')
                <div class="form__error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form__group form__group--address">
            <label for="address" class="form__label">住所</label>
            <input type="text" name="address" id="address" class="form__input" value="{{ $user->profile->address }}">
            @error('address')
                <div class="form__error">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form__group form__group--building">
            <label for="building" class="form__label">建物名</label>
            <input type="text" name="building" id="building" class="form__input" value="{{ $user->profile->building }}">
            @error('building')
                <div class="form__error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="form__btn">更新する</button>
    </form>
</div>
@endsection