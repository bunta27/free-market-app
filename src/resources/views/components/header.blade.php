<header class="header">
    <div class="header__logo">
        <a href="/">
            <img src="{{ asset('/img/logo.svg') }}" alt="ロゴ">
        </a>
    </div>
    <nav class="header__nav">
        <ul>
            @if(Auth::check())
            <li>
                <form action="/logout" method="post">
                    @csrf
                    <button class="header__logout">ログアウト</button>
                </form>
            </li>
            <li>
                <a href="{{ route('mypage') }}">マイページ</a>
            </li>
            @else
            <li>
                {{-- <a href="{{ route('login') }}">ログイン</a> --}}
            </li>
            <li>
                {{-- <a href="{{ route('register') }}">会員登録</a> --}}
            </li>
            @endif
            <li class="header__btn">
                {{-- <a href="{{ route('sell') }}">出品</a> --}}
            </li>
        </ul>
    </nav>
</header>