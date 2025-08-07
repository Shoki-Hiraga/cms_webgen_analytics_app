<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">新しい GSC クエリを追加</h2>
    </x-slot>

    <div class="form-container">
        <form action="{{ route('admin.gsc_query_listqueries.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="query">クエリ</label>
                <input type="text" name="query" id="query" value="{{ old('query') }}" required class="form-control">
                @error('query')
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
            <a href="{{ route('admin.gsc_query_listqueries.index') }}" class="btn-secondary">キャンセル</a>
        </form>
    </div>
</x-app-layout>
