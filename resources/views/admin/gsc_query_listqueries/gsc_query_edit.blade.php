<x-app-layout>
    <x-slot name="header">
        <x-slot name="title">編集 | @include('components.Gsc_Queries') </x-slot>
        <h2 class="page-title">@include('components.Gsc_Queries') 編集</h2>
    </x-slot>

    <div class="form-container">
        <form action="{{ route('admin.gsc_query_listqueries.update', $query->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="query">クエリ</label>
                <input type="text" name="query" id="query" value="{{ old('query', $query->query) }}" required class="form-control">
                @error('query')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_active" value="1" {{ $query->is_active ? 'checked' : '' }}>
                    有効にする
                </label>
            </div>

            <button type="submit" class="btn-primary">更新</button>
            <a href="{{ route('admin.gsc_query_listqueries.index') }}" class="btn-secondary">キャンセル</a>
        </form>

        <form action="{{ route('admin.gsc_query_listqueries.destroy', $query->id) }}" method="POST" style="margin-top: 20px;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-danger" onclick="return confirm('本当に削除しますか？')">削除</button>
        </form>
    </div>
</x-app-layout>
