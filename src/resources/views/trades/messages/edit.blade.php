@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/trades/edit-message.css') }}">
@endsection

@section('content')

@include('components.header')
<div class="trade-message-edit">
    <div class="trade-message-edit__inner">
        <h1 class="trade-message-edit__title">メッセージ編集</h1>

        <form action="{{ route('trade.messages.update', $message) }}" method="post" enctype="multipart/form-data" class="trade-message-edit__form">
            @csrf
            @method('PUT')

            <div class="trade-message-edit__group">
                <label for="message">メッセージ</label>
                <textarea name="message" id="message" rows="6">{{ old('message', $message->message) }}</textarea>
                @error('message')
                    <p class="trade-message-edit__error">{{ $message }}</p>
                @enderror
            </div>

            <div class="trade-message-edit__group">
                <label for="image">画像</label>
                <input type="file" name="image" id="image">
                @if ($message->image_path)
                    <div class="trade-message-edit__preview">
                        <img src="{{ asset('storage/' . $message->image_path) }}" alt="現在の画像">
                    </div>
                @endif
                @error('image')
                    <p class="trade-message-edit__error">{{ $message }}</p>
                @enderror
            </div>

            <div class="trade-message-edit__actions">
                <a href="{{ route('trades.show', $message->trade) }}" class="trade-message-edit__back">戻る</a>
                <button type="submit" class="trade-message-edit__submit">更新する</button>
            </div>
        </form>
    </div>
</div>
@endsection