@extends('layouts.app')

@section('title','商品詳細ページ')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/detail.css') }}">
@endsection

@section('content')

@include('components.header')
<div class="container">
    <div class="item">
        @if($item->sold())
            <div class="item__img sold">
                <img src="{{ Storage::url($item->img_url) }}" alt="商品画像">
            </div>
        @else
            <div class="item__img">
                <img src="{{ Storage::url($item->img_url) }}" alt="商品画像">
            </div>
        @endif
        <div class="item__info">
            <h2 class="item__name">{{ $item->name }}</h2>
            <p class="item__price">¥{{ number_format($item->price) }}</p>
            <div class="item__form">
                @if($item->liked())
                <form action="/item/unlike/{{$item->id}}" method="post" class="item__like">
                    @csrf
                    <button type="submit" class="item__like-btn liked">♥</button>
                    <span class="like-count">{{ $item->likes->count() }}</span>
                </form>
                @else
                <form action="/item/like/{{$item->id}}" method="post" class="item__like">
                    @csrf
                    <button type="submit" class="item__like-btn">♡</button>
                    <span class="like-count">{{ $item->likes->count() }}</span>
                </form>
                @endif
                <div class="item__comment">
                    <a href="#comment">💬</a>
                    <p class="comment__count">{{ $item->getComments()->count() }}</p>
                </div>
            </div>
        @if($item->sold())
            <a href="#" class="btn item__purchase disable">売り切れました</a>
        @elseif($item->mine())
            <a href="#" class="btn item__purchase disable">購入できません</a>
        @else
            <a href="{{ route('purchase.index', ['item_id' => $item->id]) }}" class="btn item__purchase">購入手続きへ</a>
        @endif
            <h3 class="item__section">商品説明</h3>
            <p class="item__description">{{ $item->description }}</p>
            <h3 class="item__section">商品の情報</h3>
            <table class="item__table">
                <tr>
                    <th>カテゴリー</th>
                    <td>
                        <ul class="item__category">
                            @forelse($item->categories as $cat)
                                <span class="badge">{{ $cat->category }}</span>
                            @empty
                                <span class="badge badge--muted">カテゴリ未設定</span>
                            @endforelse
                        </ul>
                    </td>
                </tr>
                <tr>
                    <th>商品の状態</th>
                    <td>
                        {{ $item->condition->condition }}
                    </td>
                </tr>
            </table>
        </div>
        <div class="comment__section" id="comment">
            <h3>コメント({{ $item->getComments()->count() }})</h3>
            <div class="comments">
                @foreach($item->getComments() as $comment)
                    <div class="comment">
                        <div class="comment__user">
                            <div class="user__img">
                                @if($comment->user->profile && $comment->user->profile->img_url)
                                    <img src="{{ Storage::url($comment->user->profile->img_url) }}" alt="ユーザー画像">
                                @else
                                    <img src="{{ asset('/img/icon.png') }}" alt="ユーザー画像">
                                @endif
                            </div>
                            <p class="comment__name">{{ $comment->user->name }}</p>
                        </div>
                        <p class="comment__content">{{ $comment->comment }}</p>
                    </div>
                @endforeach
            </div>
            <form action="{{ route('comments.create', ['item_id' => $item->id]) }}" method="post" class="comment__form">
                @csrf
                <p class="comment__form-title">商品へのコメント</p>
                <textarea name="comment" class="comment__form-textarea"></textarea>
                    @error('comment')
                        {{ $message }}
                    @enderror

                <button type="submit" class="comment__btn btn">コメントを送信する</button>
            </form>
        </div>
    </div>
</div>
@endsection