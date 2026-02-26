<x-app-layout>
    <x-slot name="header">
        <x-slot name="title">新規 | Ads Keywords</x-slot>
        <h2 class="page-title">Ads Keywords を追加</h2>
    </x-slot>

    <div class="form-container">
        <form action="{{ route('admin.ads_keywords.store') }}" method="POST">
            @csrf

            {{-- Keyword --}}
            <div class="form-group">
                <label for="keyword">キーワード</label>
                <input type="text" name="keyword" id="keyword" 
                       value="{{ old('keyword') }}" required class="form-control">

                @error('keyword')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Product --}}
            <div class="form-group">
                <label for="product">プロダクト名</label>
                <input type="text" name="product" id="product" 
                       value="{{ old('product') }}" required class="form-control">

                @error('product')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Priority --}}
            <div class="form-group">
                <label for="priority">優先度</label>
                <input type="text" name="priority" id="priority" 
                       value="{{ old('priority') }}" required class="form-control">

                @error('priority')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-primary">追加</button>
            <a href="{{ route('admin.ads_keywords.index') }}" class="btn-secondary">キャンセル</a>
        </form>
    </div>
</x-app-layout>
