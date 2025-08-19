<x-app-layout>
    <x-slot name="header">
        <x-slot name="title">新規 | フィルター設定</x-slot>
        <h2 class="page-title">セッションメディアフィルターを追加</h2>
    </x-slot>

    <div class="form-container">
        <form action="{{ route('admin.ga4_filters.store') }}" method="POST">
            @csrf

            {{-- セッションメディアフィルター --}}
            <div class="form-group">
                <label for="session_medium_filter">セッションメディアフィルター</label>
                <input type="text"
                       name="session_medium_filter"
                       id="session_medium_filter"
                       value="{{ old('session_medium_filter', 'organic') }}"
                       required
                       class="form-control">
                @error('session_medium_filter')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            {{-- 対象のGA4設定ID --}}
            <input type="hidden" name="ga4_setting_id" value="{{ old('ga4_setting_id', $ga4_setting_id ?? '') }}">

            <button type="submit" class="btn-primary">追加</button>
            <a href="{{ route('admin.ga4_filters.index') }}" class="btn-secondary">キャンセル</a>
        </form>
    </div>
</x-app-layout>
