@extends('layouts.app')

@section('title','マイページ')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/index.css') }}">
<link rel="stylesheet" href="{{ asset('/css/mypage.css') }}">
@endsection

@section('content')

@include('components.header')

<div class="container">
    <div class="user">
        <div class="user__info">
            <div class="user__img">
                @if($user->profile && $user->profile->img_url)
                <img class="user__icon" src="{{ Storage::url($user->profile->img_url) }}" alt="ユーザー画像">
                @else
                <img id="myImage" class="user__icon" src="{{ asset('/img/icon.png') }}" alt="ユーザー画像">
                @endif
            </div>

            <div class="user__meta">
                <p class="user__name">{{ $user->name }}</p>

                <div class="user__rating">
                    @php
                    $rounded = $avgRating ? round($avgRating) : 0;
                    @endphp

                    @for($i = 1; $i <= 5; $i++)
                        <span class="user__star {{ $i <= $rounded ? 'user__star--active' : '' }}">★</span>
                        @endfor
                </div>
            </div>
        </div>

        <div class="mypage__user-btn">
            <a href="{{ route('mypage.profile') }}" class="btn2">プロフィールを編集</a>
        </div>
    </div>

    <div class="border">
        <ul class="border__list">
            <li class="{{ $tab === 'sell' ? 'active' : '' }}">
                <a href="{{ route('mypage', ['page' => 'sell']) }}">出品した商品</a>
            </li>

            <li class="{{ $tab === 'buy' ? 'active' : '' }}">
                <a href="{{ route('mypage', ['page' => 'buy']) }}">購入した商品</a>
            </li>

            <li class="{{ $tab === 'trade' ? 'active' : '' }}">
                <a href="{{ route('mypage', ['page' => 'trade']) }}">
                    取引中の商品
                    @if($tradeCount > 0)
                    <span class="border__badge">{{ $tradeCount }}</span>
                    @endif
                </a>
            </li>
        </ul>
    </div>

    @if($tab === 'trade')
    <div class="items">
        @forelse($trades as $trade)
        <div class="item">
            <a href="{{ route('trades.show', $trade->id) }}">
                <div class="item__img">
                    <img src="{{ asset($trade->item->img_url) }}" alt="商品画像">
                    @if($trade->unread_count > 0)
                    <span class="item__badge">{{ $trade->unread_count }}</span>
                    @endif
                </div>
                <p class="item__name">{{ $trade->item->name }}</p>
            </a>
        </div>
        @empty
        <p class="items__empty">取引中の商品はありません。</p>
        @endforelse
    </div>
    @else
    <div class="items">
        @foreach($items as $item)
        @php
        $itemLink = $item->trade
        ? route('trades.show', $item->trade->id)
        : route('items.detail', $item->id);
        @endphp
        <div class="item">
            <a href="{{ $itemLink }}">
                @if($item->sold())
                <div class="item__img sold">
                    <img src="{{ asset($item->img_url) }}" alt="商品画像">
                </div>
                @else
                <div class="item__img">
                    <img src="{{ asset($item->img_url) }}" alt="商品画像">
                </div>
                @endif
                <p class="item__name">{{ $item->name }}</p>
            </a>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection