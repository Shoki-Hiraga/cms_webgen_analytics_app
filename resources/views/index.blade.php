<x-app-layout>
    <x-slot name="header">
        <x-slot name="title">TOP | cms_webgen_analytics_app</x-slot>
        <h2 class="page-title">URL 管理メニュー</h2>
    </x-slot>

    @auth
        <div class="link-grid">
            <a href="{{ route('admin.ga4_directory_listurls.index') }}" class="link-card">GA4 ディレクトリ URL</a>
            <a href="{{ route('admin.gsc_directory_listurls.index') }}" class="link-card">GSC ディレクトリ URL</a>
            <a href="{{ route('admin.ga4_fullurl_listurls.index') }}" class="link-card">GA4 フル URL</a>
            <a href="{{ route('admin.gsc_fullurl_listurls.index') }}" class="link-card">GSC フル URL</a>
            <a href="{{ route('admin.ga4_media_url_listurls.index') }}" class="link-card">GA4 メディア URL</a>
            <a href="{{ route('admin.gsc_media_url_listurls.index') }}" class="link-card">GSC メディア URL</a>
            <a href="{{ route('admin.gsc_query_listqueries.index') }}" class="link-card">GSC クエリ</a>
            <a href="{{ route('admin.ga4_settings.index') }}" class="link-card">GA4 設定</a>
            <a href="{{ route('admin.gsc_settings.index') }}" class="link-card">GSC 設定</a>

        </div>
    @else
        <div class="guest-index-message">
            <p class="guest-index-text">このページはログインユーザー専用です。</p>
            <div class="guest-index-links">
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="link-card">ログイン</a>
                @endif
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="link-card">新規登録</a>
                @endif
            </div>
        </div>
    @endauth
</x-app-layout>
