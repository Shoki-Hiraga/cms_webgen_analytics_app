<x-app-layout>
    <x-slot name="header">
        <x-slot name="title">編集 | GA4設定</x-slot>
        <h2 class="page-title">GA4設定を編集</h2>
    </x-slot>

    <div class="form-container">
        {{-- 更新フォーム --}}
        <form action="{{ route('admin.ga4_settings.update', $ga4_setting->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- サービスアカウントJSON --}}
            <div class="form-group">
                <label for="service_account_json">サービスアカウントJSON</label>
                <textarea name="service_account_json" 
                          id="service_account_json" 
                          rows="12" 
                          required 
                          class="form-control"
                          placeholder='{"type":"service_account", ...}'>{{ old('service_account_json', $ga4_setting->service_account_json) }}</textarea>
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
                       value="{{ old('property_id', $ga4_setting->property_id) }}" 
                       required 
                       class="form-control">
                @error('property_id')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-primary">更新</button>
            <a href="{{ route('admin.ga4_settings.index') }}" class="btn-secondary">キャンセル</a>
        </form>

        {{-- 削除フォーム --}}
        <form action="{{ route('admin.ga4_settings.destroy', $ga4_setting->id) }}" method="POST" style="margin-top: 20px;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-danger" onclick="return confirm('本当に削除しますか？')">削除</button>
        </form>
    </div>
</x-app-layout>
