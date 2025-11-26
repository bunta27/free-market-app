<header class="header">
    <div class="header__logo">
        <a href="/">
            <img src="{{ asset('/img/logo.svg') }}" alt="ロゴ">
        </a>
    </div>
    @unless (request()->routeIs('login') || request()->routeIs('register'))
        <div class="header__search">
            <form action="{{ route('items.search') }}" method="get">
                <input type="text" name="query" placeholder="なにをお探しですか？" value="{{ request('query') }}">
            </form>
        </div>

        <nav class="header__nav">

            <input type="checkbox" id="nav-toggle" class="header__nav-toggle">
            <label for="nav-toggle" class="header__nav-toggle-label">
                <span></span>
                <span></span>
                <span></span>
            </label>

            <ul class="header__nav-list">
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
                        <a href="{{ route('items.sell.view') }}">出品</a>
                    @else
                        <a href="{{ route('login') }}">出品</a>
                    @endauth
                </li>
            </ul>
        </nav>
    @endunless
</header>