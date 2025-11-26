@extends('layouts.app')

@section('title','購入手続き')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/purchase.css') }}">
@endsection

@section('content')

@include('components.header')
<div class="container">
    <div class="buy">
        <div class="buy__left">
            <div class="item">
                <div class="item__img">
                    <img src="{{ Storage::url($item->img_url) }}" alt="商品画像">
                </div>
                <div class="item__info">
                    <h3 class="item__name">{{ $item->name }}</h3>
                    <p class="item__price">
                        <span class="yen">¥</span>
                        <span class="price">{{ number_format($item->price) }}</span>
                    </p>
                </div>
            </div>

            <hr class="purchase-line">

            <div class="purchases">
                <div class="purchase__flex">
                    <h3 class="purchase__title">支払い方法</h3>
                </div>

                <div class="custom-select" data-target="payment">
                    <select name="payment_select" id="payment-select" class="custom-select__real">
                        <option value="" selected disabled>選択してください</option>
                        <option value="konbini">コンビニ支払い</option>
                        <option value="card">カード支払い</option>
                    </select>

                    <button type="button" class="custom-select__trigger">
                        <span class="custom-select__label">選択してください</span>
                        <span class="select-arrow">
                            @include('components.svg.arrow')
                        </span>
                    </button>

                    <ul class="custom-select__options">
                        <li class="custom-select__option" data-value="konbini">コンビニ支払い</li>
                        <li class="custom-select__option" data-value="card">カード支払い</li>
                    </ul>
                </div>
            </div>

            <div class="purchases">
                <div class="purchase__flex">
                    <h3 class="purchase__title">配送先</h3>
                    <a href="{{ route('purchase.address', ['item_id' => $item->id]) }}" class="purchase__change">変更する</a>
                </div>
                <div class="purchase__value">
                    <p>〒 {{ $user->profile->postcode }}</p>
                    <p>{{ $user->profile->address }}</p>
                    @if(isset($user->profile->building))
                        <p>{{ $user->profile->building }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="buy__right">
            <div class="buy__info">
                <table>
                    <tr>
                        <th>商品代金</th>
                        <td class="price-cell">
                            <span class="yen">¥</span>
                            <span class="amount">{{ number_format($item->price) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>支払い方法</th>
                        <td id="method">コンビニ支払い</td>
                    </tr>
                </table>
            </div>
            <form action="{{ route('purchase.execute', ['item_id' => $item->id]) }}" method="post">
                @csrf
                <input type="hidden" name="payment_method" id="payment-hidden" value="">

                @if($item->sold())
                    <button type="submit" class="btn disable" disabled>売り切れました</button>
                @elseif($item->mine())
                    <button type="submit" class="btn disable" disabled>購入できません</button>
                @else
                    <button type="submit" class="btn">購入する</button>
                @endif
            </form>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const customSelect = document.querySelector('.custom-select');
            const trigger      = customSelect.querySelector('.custom-select__trigger');
            const label        = customSelect.querySelector('.custom-select__label');
            const optionsBox   = customSelect.querySelector('.custom-select__options');
            const options      = optionsBox.querySelectorAll('.custom-select__option');
            const realSelect   = customSelect.querySelector('.custom-select__real');
            const methodLabel  = document.getElementById('method');
            const hiddenPayment  = document.getElementById('payment-hidden');

            trigger.addEventListener('click', function () {
                customSelect.classList.toggle('is-open');
            });

            options.forEach(function (opt) {
                opt.addEventListener('click', function () {
                    const value = opt.dataset.value;
                    const text  = opt.textContent.trim();

                    label.textContent = text;

                    options.forEach(o => o.classList.remove('is-selected'));
                    opt.classList.add('is-selected');
                    realSelect.value = value;
                    realSelect.dispatchEvent(new Event('change'));

                    if (methodLabel) {
                        methodLabel.textContent = text;
                    }

                    if (hiddenPayment) {
                        hiddenPayment.value = value;
                    }

                    customSelect.classList.remove('is-open');
                });
            });

            document.addEventListener('click', function (e) {
                if (!customSelect.contains(e.target)) {
                    customSelect.classList.remove('is-open');
                }
            });
        });
    </script>
</div>
@endsection