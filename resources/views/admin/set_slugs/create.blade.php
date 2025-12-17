<x-app-layout>
    <x-slot name="header">
        <x-slot name="title">新規 | Set Slug</x-slot>
        <h2 class="page-title">Set Slug を追加</h2>
    </x-slot>

    <div class="form-container">
        <form action="{{ route('admin.set_slugs.store') }}" method="POST">
            @csrf

            {{-- Slug --}}
            <div class="form-group">
                <label for="slug">Slug</label>
                <input type="text"
                       name="slug"
                       id="slug"
                       value="{{ old('slug') }}"
                       required
                       class="form-control"
                       placeholder="/ga4_qsha_oh">

                @error('slug')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            {{-- 表示名 --}}
            <div class="form-group">
                <label for="label">表示名</label>
                <input type="text"
                       name="label"
                       id="label"
                       value="{{ old('label') }}"
                       required
                       class="form-control">

                @error('label')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Type --}}
            <div class="form-group">
                <label for="type">Type</label>
                <select name="type" id="type" class="form-control" required>
                    <option value="">選択してください</option>
                    <option value="ga4" {{ old('type') === 'ga4' ? 'selected' : '' }}>
                        GA4
                    </option>
                    <option value="gsc" {{ old('type') === 'gsc' ? 'selected' : '' }}>
                        GSC
                    </option>
                </select>

                @error('type')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Handler --}}
            <div class="form-group">
                <label for="handler">Handler</label>
                <input type="text"
                       name="handler"
                       id="handler"
                       value="{{ old('handler') }}"
                       class="form-control"
                       placeholder="index / show / yoy / mom">

                @error('handler')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            {{-- 親 --}}
            <div class="form-group">
                <label for="parent_id">親 Slug</label>
                <select name="parent_id" id="parent_id" class="form-control">
                    <option value="">なし</option>
                    @foreach ($parents as $parent)
                        <option value="{{ $parent->id }}"
                            {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                            {{ $parent->label }}
                        </option>
                    @endforeach
                </select>

                @error('parent_id')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            {{-- 公開 --}}
            <div class="form-group">
                <label>
                    <input type="checkbox"
                           name="active"
                           value="1"
                           {{ old('active', true) ? 'checked' : '' }}>
                    公開
                </label>
            </div>

            {{-- 並び順 --}}
            <div class="form-group">
                <label for="sort_order">並び順</label>
                <input type="number"
                       name="sort_order"
                       id="sort_order"
                       value="{{ old('sort_order', 0) }}"
                       class="form-control">

                @error('sort_order')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-primary">追加</button>
            <a href="{{ route('admin.set_slugs.index') }}"
               class="btn-secondary">
               キャンセル
            </a>
        </form>
    </div>
</x-app-layout>
