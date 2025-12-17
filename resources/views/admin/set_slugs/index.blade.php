<x-app-layout>
    <x-slot name="header">
        <x-slot name="title">一覧 | Set Slug</x-slot>
        <h2 class="page-title">Set Slug 一覧</h2>
    </x-slot>

    <div class="table-container">

        {{-- 新規追加 --}}
        <a href="{{ route('admin.set_slugs.create') }}" class="btn-primary">
            ＋ 新規追加
        </a>

        <table class="table-base">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Slug</th>
                    <th>表示名</th>
                    <th>Type</th>
                    <th>Handler</th>
                    <th>親</th>
                    <th>公開</th>
                    <th>並び順</th>
                    <th>編集</th>
                    <th>削除</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($slugs as $slug)
                <tr>
                    <td>{{ $slug->id }}</td>
                    <td>{{ $slug->slug }}</td>
                    <td>{{ $slug->label }}</td>
                    <td>{{ $slug->type }}</td>
                    <td>{{ $slug->handler }}</td>
                    <td>{{ optional($slug->parent)->label }}</td>
                    <td>{{ $slug->active ? '公開' : '非公開' }}</td>
                    <td>{{ $slug->sort_order }}</td>

                    <td>
                        <a href="{{ route('admin.set_slugs.edit', $slug->id) }}">
                            編集
                        </a>
                    </td>

                    <td>
                        <form action="{{ route('admin.set_slugs.destroy', $slug->id) }}"
                              method="POST"
                              onsubmit="return confirm('削除してもよろしいですか？');">
                            @csrf
                            @method('DELETE')
                            <button class="btn-danger">削除</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination">
            {{ $slugs->links() }}
        </div>
    </div>
</x-app-layout>
