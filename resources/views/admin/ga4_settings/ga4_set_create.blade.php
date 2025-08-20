<x-app-layout>
    <x-slot name="header">
        <x-slot name="title">新規 | GA4設定</x-slot>
        <h2 class="page-title">GA4設定を追加</h2>
    </x-slot>

    <div class="form-container">
        <form action="{{ route('admin.ga4_settings.store') }}" method="POST">
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

            {{-- プロパティID --}}
            <div class="form-group">
                <label for="property_id">プロパティID</label>
                <input type="text" 
                       name="property_id" 
                       id="property_id" 
                       value="{{ old('property_id') }}" 
                       required 
                       class="form-control">
                @error('property_id')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-primary">追加</button>
            <a href="{{ route('admin.ga4_settings.index') }}" class="btn-secondary">キャンセル</a>
        </form>
    </div>
</x-app-layout>
