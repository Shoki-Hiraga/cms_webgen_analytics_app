<x-app-layout>
    <x-slot name="header">
        <x-slot name="title">新規 | データ取得設定</x-slot>
        <h2 class="page-title">データ取得設定を追加</h2>
    </x-slot>

    <div class="form-container">
        <form action="{{ route('admin.dataseting.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="target">ターゲット</label>
                <select name="target" id="target" class="form-control" required>
                    <option value="">選択してください</option>
                    <option value="GA4" {{ old('target') === 'GA4' ? 'selected' : '' }}>GA4</option>
                    <option value="GSC" {{ old('target') === 'GSC' ? 'selected' : '' }}>GSC</option>
                </select>
                @error('target')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="start_year">開始年</label>
                <input type="number" name="start_year" id="start_year" value="{{ old('start_year') }}" min="2000" max="2100" required class="form-control">
                @error('start_year')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="start_month">開始月</label>
                <input type="number" name="start_month" id="start_month" value="{{ old('start_month') }}" min="1" max="12" required class="form-control">
                @error('start_month')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-primary">追加</button>
            <a href="{{ route('admin.dataseting.index') }}" class="btn-secondary">キャンセル</a>
        </form>
    </div>
</x-app-layout>
