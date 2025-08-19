<x-app-layout>
    <x-slot name="header">
        <x-slot name="title">編集 | フィルター設定</x-slot>
        <h2 class="page-title">セッションメディアフィルターを編集</h2>
    </x-slot>

    <div class="form-container">
        {{-- 更新フォーム --}}
        <form action="{{ route('admin.ga4_filters.update', $setting->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- セッションメディアフィルター --}}
            <div class="form-group">
                <label for="session_medium_filter">セッションメディアフィルター</label>
                <input type="text"
                       name="session_medium_filter"
                       id="session_medium_filter"
                       value="{{ old('session_medium_filter', optional($filter)->session_medium_filter) }}"
                       required
                       class="form-control">
                @error('session_medium_filter')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-primary">更新</button>
            <a href="{{ route('admin.ga4_settings.index') }}" class="btn-secondary">キャンセル</a>
        </form>
    </div>
</x-app-layout>
