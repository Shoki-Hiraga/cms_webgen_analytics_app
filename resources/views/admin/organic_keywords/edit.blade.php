<x-app-layout>
    <x-slot name="header">
        <x-slot name="title">編集 | Organic Keywords</x-slot>
        <h2 class="page-title">Organic Keyword 編集</h2>
    </x-slot>

    <div class="form-container">

        {{-- 編集フォーム --}}
        <form action="{{ route('admin.organic_keywords.update', $keyword->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="keyword">キーワード</label>
                <input type="text"
                       name="keyword"
                       id="keyword"
                       value="{{ old('keyword', $keyword->keyword) }}"
                       required
                       class="form-control">

                @error('keyword')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-primary">更新</button>
            <a href="{{ route('admin.organic_keywords.index') }}" class="btn-secondary">キャンセル</a>
        </form>

        {{-- 削除フォーム --}}
        <form action="{{ route('admin.organic_keywords.destroy', $keyword->id) }}"
              method="POST"
              style="margin-top: 20px;">
            @csrf
            @method('DELETE')

            <button type="submit" class="btn-danger"
                onclick="return confirm('本当に削除しますか？')">
                削除
            </button>
        </form>

    </div>
</x-app-layout>
