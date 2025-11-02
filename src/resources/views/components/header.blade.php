<header class="header">
    <div class="header__logo">
        <a href="/">
            <img src="{{ asset('/img/logo.svg') }}" alt="ロゴ">
        </a>
    </div>
    <nav class="header__nav">
        <ul>
            @auth
                <li>
                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <button type="submit" class="header__logout">ログアウト</button>
                    </form>
                </li>
                <li>
                    <a href="{{ route('mypage') }}">マイページ</a>
                </li>
            @else
                <li>
                    <a href="{{ route('login') }}">ログイン</a>
                </li>
                <li>
                    <a href="{{ route('register') }}">会員登録</a>
                </li>
            @endauth

            <li class="header__btn">
                @auth
                    <a href="{{ route('item.sell.view') }}">出品する</a>
                @else
                    <a href="{{ route('login') }}">出品する</a>
                @endauth
            </li>
        </ul>
    </nav>
</header>