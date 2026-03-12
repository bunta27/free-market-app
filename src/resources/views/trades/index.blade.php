@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/trades/index.css') }}">
@endsection

@section('content')

@include('components.header')
<div class="trades-index">
    <div class="trades-index__inner">
        <h1 class="trades-index__title">取引一覧</h1>

        @if (session('success'))
            <div class="trades-index__alert trades-index__alert--success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="trades-index__alert trades-index__alert--error">
                {{ session('error') }}
            </div>
        @endif

        @if ($trades->isEmpty())
            <p class="trades-index__empty">取引はありません。</p>
        @else
            <div class="trades-index__list">
                @foreach ($trades as $trade)
                    @php
                        $partner = $trade->seller_id === auth()->id() ? $trade->buyer : $trade->seller;
                    @endphp

                    <a href="{{ route('trades.show', $trade) }}" class="trades-index__card">
                        <div class="trades-index__image">
                            <img src="{{ asset('storage/' . $trade->item->img_url) }}" alt="{{ $trade->item->name }}">
                        </div>
                        <div class="trades-index__body">
                            <p class="trades-index__item-name">{{ $trade->item->name }}</p>
                            <p class="trades-index__partner">{{ $partner->name }} さんとの取引</p>
                            <p class="trades-index__status">{{ $trade->status }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection