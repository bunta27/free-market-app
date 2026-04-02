@extends('layouts.app')

@section('title','取引チャット画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/trades/show.css') }}">
@endsection

@section('content')

@include('components.header-simple')
<div class="trade-show">
    <div class="trade-show__container">

        <aside class="trade-show__sidebar">
            <h2 class="trade-show__sidebar-title">その他の取引</h2>

            <div class="trade-show__sidebar-list">
                @foreach ($otherTrades as $otherTrade)
                <a href="{{ route('trades.show', $otherTrade) }}" class="trade-show__sidebar-item">
                    {{ $otherTrade->item->name }}
                </a>
                @endforeach
            </div>
        </aside>

        <div class="trade-show__main">
            <div class="trade-show__header">
                <div class="trade-show__partner">
                    <div class="trade-show__partner-icon">
                        @if (!empty($partnerUser->profile->img_url ?? null))
                        <img src="{{ asset('storage/' . $partnerUser->profile->img_url) }}" alt="{{ $partnerUser->name }}">
                        @endif
                    </div>
                    <h1 class="trade-show__partner-name">「{{ $partnerUser->name }}」さんとの取引画面</h1>
                </div>

                @if ($canComplete)
                <form action="{{ route('trades.complete', $trade) }}" method="post">
                    @csrf
                    <button type="submit" class="trade-show__complete-btn">取引を完了する</button>
                </form>
                @endif
            </div>

            <div class="trade-show__item-card">
                <div class="trade-show__item-image">
                    <img src="{{ asset($trade->item->img_url) }}" alt="{{ $trade->item->name }}">
                </div>
                <div class="trade-show__item-info">
                    <h2 class="trade-show__item-name">{{ $trade->item->name }}</h2>
                    <p class="trade-show__item-price">¥{{ number_format($trade->item->price) }}</p>
                </div>
            </div>

            @if (session('success'))
            <div class="trade-show__alert trade-show__alert--success">
                {{ session('success') }}
            </div>
            @endif

            @if (session('error'))
            <div class="trade-show__alert trade-show__alert--error">
                {{ session('error') }}
            </div>
            @endif

            <div class="trade-show__messages">
                @foreach ($trade->messages as $message)
                @php
                $isMine = $message->user_id === auth()->id();
                @endphp

                <div class="trade-show__message {{ $isMine ? 'trade-show__message--mine' : 'trade-show__message--other' }}">
                    <div class="trade-show__message-head">
                        @if (!$isMine)
                        <div class="trade-show__message-user-icon"></div>
                        <span class="trade-show__message-user-name">{{ $message->user->name }}</span>
                        @else
                        <span class="trade-show__message-user-name">{{ $message->user->name }}</span>
                        <div class="trade-show__message-user-icon"></div>
                        @endif
                    </div>

                    <div class="trade-show__message-body">
                        <p class="trade-show__message-text">{{ $message->message }}</p>

                        @if ($message->image_path)
                        <div class="trade-show__message-image">
                            <img src="{{ asset('storage/' . $message->image_path) }}" alt="送信画像">
                        </div>
                        @endif
                    </div>

                    @if ($isMine)
                    <div class="trade-show__message-actions">
                        <a href="{{ route('trade.messages.edit', $message) }}">編集</a>
                        <form action="{{ route('trade.messages.destroy', $message) }}" method="post" onsubmit="return confirm('削除しますか？')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">削除</button>
                        </form>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

            <div class="trade-show__form-wrap">
                <form
                    action="{{ route('trade.messages.store', $trade) }}"
                    method="post"
                    enctype="multipart/form-data"
                    class="trade-show__form"
                    id="trade-message-form">
                    @csrf

                    <div class="trade-show__form-main">
                        <input
                            type="text"
                            id="trade-message-input"
                            name="message"
                            class="trade-show__input"
                            placeholder="取引メッセージを記入してください"
                            value="{{ old('message') }}">

                        <label class="trade-show__image-btn">
                            画像を追加
                            <input type="file" name="image" hidden>
                        </label>

                        <button type="submit" class="trade-show__submit-btn">
                            <img src="{{ asset('img/inputbutton.jpg') }}" alt="送信">
                        </button>
                    </div>

                    @error('message')
                    <p class="trade-show__error">{{ $message }}</p>
                    @enderror

                    @error('image')
                    <p class="trade-show__error">{{ $message }}</p>
                    @enderror
                </form>
            </div>
        </div>
    </div>

    @if ($canReview)
    <div class="trade-review-modal">
        <div class="trade-review-modal__overlay"></div>
        <div class="trade-review-modal__content">
            <div class="trade-review-modal__header">
                <h2>取引が完了しました。</h2>
                <p>今回の取引相手はどうでしたか？</p>
            </div>

            <form action="{{ route('trade.reviews.store', $trade) }}" method="post" class="trade-review-modal__form">
                @csrf

                <div class="trade-review-modal__stars">
                    @for ($i = 5; $i >= 1; $i--)
                    <input
                        type="radio"
                        id="rating-{{ $i }}"
                        name="rating"
                        value="{{ $i }}"
                        {{ old('rating') == $i ? 'checked' : '' }}>
                    <label for="rating-{{ $i }}">★</label>
                    @endfor
                </div>

                <div class="trade-review-modal__comment">
                    <textarea name="comment" rows="4" placeholder="コメントを入力してください（任意）">{{ old('comment') }}</textarea>
                </div>

                @error('rating')
                <p class="trade-show__error">{{ $message }}</p>
                @enderror

                @error('comment')
                <p class="trade-show__error">{{ $message }}</p>
                @enderror

                <div class="trade-review-modal__footer">
                    <button type="submit" class="trade-review-modal__submit">送信する</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tradeId = "{{ $trade->id }}";
        const input = document.getElementById('trade-message-input');
        const form = document.getElementById('trade-message-form');
        const sidebarLinks = document.querySelectorAll('.trade-show__sidebar-item');
        const shouldClearDraft = "{{ request('clear_draft') }}" === "1";

        if (!input || !form) return;

        const storageKey = 'trade_message_draft_' + tradeId;

        const saveDraft = function() {
            localStorage.setItem(storageKey, input.value || '');
        };

        if (shouldClearDraft) {
            localStorage.removeItem(storageKey);
            input.value = '';
            return;
        }

        const saved = localStorage.getItem(storageKey);
        if (!input.value && saved !== null) {
            input.value = saved;
        }

        input.addEventListener('input', saveDraft);
        input.addEventListener('keyup', saveDraft);
        input.addEventListener('change', saveDraft);
        input.addEventListener('blur', saveDraft);

        sidebarLinks.forEach(function(link) {
            link.addEventListener('mousedown', saveDraft);
            link.addEventListener('touchstart', saveDraft, {
                passive: true
            });
        });

        window.addEventListener('beforeunload', saveDraft);

        form.addEventListener('submit', function() {
            saveDraft();
        });
    });
</script>
@endsection