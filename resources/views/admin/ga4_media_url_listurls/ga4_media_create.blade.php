<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">新しい GA4 Media URL を追加</h2>
    </x-slot>

    <div class="form-container">
        <form action="{{ route('admin.ga4_media_url_listurls.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="url">URL</label>
                <input type="text" name="url" id="url" value="{{ old('url') }}" required class="form-control">
                @error('url')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_active" value="1" checked>
                    有効にする
                </label>
            </div>

            <button type="submit" class="btn-primary">追加</button>
            <a href="{{ route('admin.ga4_media_url_listurls.index') }}" class="btn-secondary">キャンセル</a>
        </form>
    </div>
</x-app-layout>
