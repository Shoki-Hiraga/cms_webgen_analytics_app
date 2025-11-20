<x-app-layout>
    <x-slot name="header">
        <x-slot name="title">新規 | Organic Keywords</x-slot>
        <h2 class="page-title">Organic Keywords を追加</h2>
    </x-slot>

    <div class="form-container">
        <form action="{{ route('admin.organic_keywords.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="keyword">キーワード</label>
                <input type="text" name="keyword" id="keyword" value="{{ old('keyword') }}" required class="form-control">

                @error('keyword')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-primary">追加</button>
            <a href="{{ route('admin.organic_keywords.index') }}" class="btn-secondary">キャンセル</a>
        </form>
    </div>
</x-app-layout>
