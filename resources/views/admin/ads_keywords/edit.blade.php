<x-app-layout>
    <x-slot name="header">
        <x-slot name="title">編集 | Ads Keywords</x-slot>
        <h2 class="page-title">Ads Keyword 編集</h2>
    </x-slot>

    <div class="form-container">

        {{-- 編集フォーム --}}
        <form action="{{ route('admin.ads_keywords.update', $keyword->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Keyword --}}
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

            {{-- Product --}}
            <div class="form-group">
                <label for="product">商品名</label>
                <input type="text"
                       name="product"
                       id="product"
                       value="{{ old('product', $keyword->product) }}"
                       required
                       class="form-control">

                @error('product')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Priority --}}
            <div class="form-group">
                <label for="priority">優先度</label>
                <input type="text"
                       name="priority"
                       id="priority"
                       value="{{ old('priority', $keyword->priority) }}"
                       required
                       class="form-control">

                @error('priority')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-primary">更新</button>
            <a href="{{ route('admin.ads_keywords.index') }}" class="btn-secondary">キャンセル</a>
        </form>

        {{-- 削除フォーム --}}
        <form action="{{ route('admin.ads_keywords.destroy', $keyword->id) }}"
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
