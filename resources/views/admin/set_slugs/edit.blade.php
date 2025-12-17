<x-app-layout>
    <x-slot name="header">
        <x-slot name="title">編集 | Set Slug</x-slot>
        <h2 class="page-title">Set Slug 編集</h2>
    </x-slot>

    <div class="form-container">

        {{-- 編集フォーム --}}
        <form action="{{ route('admin.set_slugs.update', $set_slug->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Slug --}}
            <div class="form-group">
                <label for="slug">Slug</label>
                <input type="text"
                       name="slug"
                       id="slug"
                       value="{{ old('slug', $set_slug->slug) }}"
                       required
                       class="form-control">

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
                       value="{{ old('label', $set_slug->label) }}"
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
                    <option value="ga4" {{ old('type', $set_slug->type) === 'ga4' ? 'selected' : '' }}>
                        GA4
                    </option>
                    <option value="gsc" {{ old('type', $set_slug->type) === 'gsc' ? 'selected' : '' }}>
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
                       value="{{ old('handler', $set_slug->handler) }}"
                       class="form-control">

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
                            {{ old('parent_id', $set_slug->parent_id) == $parent->id ? 'selected' : '' }}>
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
                           {{ old('active', $set_slug->active) ? 'checked' : '' }}>
                    公開
                </label>
            </div>

            {{-- 並び順 --}}
            <div class="form-group">
                <label for="sort_order">並び順</label>
                <input type="number"
                       name="sort_order"
                       id="sort_order"
                       value="{{ old('sort_order', $set_slug->sort_order) }}"
                       class="form-control">

                @error('sort_order')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-primary">更新</button>
            <a href="{{ route('admin.set_slugs.index') }}" class="btn-secondary">
                キャンセル
            </a>
        </form>

        {{-- 削除フォーム --}}
        <form action="{{ route('admin.set_slugs.destroy', $set_slug->id) }}"
              method="POST"
              style="margin-top: 20px;">
            @csrf
            @method('DELETE')

            <button type="submit"
                    class="btn-danger"
                    onclick="return confirm('本当に削除しますか？')">
                削除
            </button>
        </form>

    </div>
</x-app-layout>
