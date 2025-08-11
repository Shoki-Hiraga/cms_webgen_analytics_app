<x-app-layout>
    <x-slot name="header">
        <x-slot name="title">編集 | @include('components.Ga4_Full_Urls') </x-slot>
        <h2 class="page-title">@include('components.Ga4_Full_Urls') 編集</h2>
    </x-slot>

    <div class="form-container">
        <form action="{{ route('admin.ga4_fullurl_listurls.update', $url->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="url">URL</label>
                <input type="text" name="url" id="url" value="{{ old('url', $url->url) }}" required class="form-control">
                @error('url')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_active" value="1" {{ $url->is_active ? 'checked' : '' }}>
                    有効にする
                </label>
            </div>

            <button type="submit" class="btn-primary">更新</button>
            <a href="{{ route('admin.ga4_fullurl_listurls.index') }}" class="btn-secondary">キャンセル</a>
        </form>

        <form action="{{ route('admin.ga4_fullurl_listurls.destroy', $url->id) }}" method="POST" style="margin-top: 20px;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-danger" onclick="return confirm('本当に削除しますか？')">削除</button>
        </form>
    </div>
</x-app-layout>
