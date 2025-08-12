<x-app-layout>
    <x-slot name="header">
        <x-slot name="title">編集 | GSC設定</x-slot>
        <h2 class="page-title">GSC設定を編集</h2>
    </x-slot>

    <div class="form-container">
        {{-- 更新フォーム --}}
        <form action="{{ route('admin.gsc_settings.update', $gsc_setting->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- サイトURL --}}
            <div class="form-group">
                <label for="site_url">サイトURL</label>
                <input type="text"
                       name="site_url"
                       id="site_url"
                       value="{{ old('site_url', $gsc_setting->site_url) }}"
                       required
                       class="form-control">
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
                          class="form-control">{{ old('service_account_json', $gsc_setting->service_account_json) }}</textarea>
                @error('service_account_json')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-primary">更新</button>
            <a href="{{ route('admin.gsc_settings.index') }}" class="btn-secondary">キャンセル</a>
        </form>

        {{-- 削除フォーム --}}
        <form action="{{ route('admin.gsc_settings.destroy', $gsc_setting->id) }}" method="POST" style="margin-top: 20px;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-danger" onclick="return confirm('本当に削除しますか？')">削除</button>
        </form>
    </div>
</x-app-layout>
