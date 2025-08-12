<x-app-layout>
    <x-slot name="header">
        <x-slot name="title">新規 | GSC設定</x-slot>
        <h2 class="page-title">GSC設定を追加</h2>
    </x-slot>

    <div class="form-container">
        <form action="{{ route('admin.gsc_settings.store') }}" method="POST">
            @csrf

            {{-- サイトURL --}}
            <div class="form-group">
                <label for="site_url">サイトURL</label>
                <input type="text"
                       name="site_url"
                       id="site_url"
                       value="{{ old('site_url') }}"
                       required
                       class="form-control"
                       placeholder="https://example.com/">
                @error('site_url')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            {{-- サービスアカウントJSON --}}
            <div class="form-group">
                <label for="service_account_json">サービスアカウントJSON</label>
                <textarea name="service_account_json"
                          id="service_account_json"
                          rows="12"
                          required
                          class="form-control"
                          placeholder='{"type":"service_account", ...}'>{{ old('service_account_json') }}</textarea>
                @error('service_account_json')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-primary">追加</button>
            <a href="{{ route('admin.gsc_settings.index') }}" class="btn-secondary">キャンセル</a>
        </form>
    </div>
</x-app-layout>
