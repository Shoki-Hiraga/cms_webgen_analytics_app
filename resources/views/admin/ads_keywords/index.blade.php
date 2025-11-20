<x-app-layout>
    <x-slot name="header">
        <x-slot name="title">一覧 | Ads Keywords</x-slot>
        <h2 class="page-title">Ads Keywords 一覧</h2>
    </x-slot>

    <div class="table-container">
        <div class="actions">
            <a href="{{ route('admin.ads_keywords.export') }}" class="btn-secondary">CSVエクスポート</a>

            <form action="{{ route('admin.ads_keywords.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <button type="submit" class="btn-primary">CSVインポート</button>
                <input type="file" name="csv_file" accept=".csv" required>
            </form>
        </div>

        {{-- 新規追加ボタン --}}
        <a href="{{ route('admin.ads_keywords.create') }}" class="btn-primary">＋ 新規追加</a>

        <table class="table-base">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>キーワード</th>
                    <th>作成日</th>
                    <th>編集</th>
                    <th>削除</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($keywords as $kw)
                <tr>
                    <td>{{ $kw->id }}</td>
                    <td>{{ $kw->keyword }}</td>
                    <td>{{ $kw->created_at }}</td>

                    <td>
                        <a href="{{ route('admin.Ads_keywords.edit', $kw->id) }}">編集</a>
                    </td>

                    <td>
                        <form action="{{ route('admin.Ads_keywords.destroy', $kw->id) }}" method="POST"
                            onsubmit="return confirm('削除してもよろしいですか？');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger">削除</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>

        </table>

        <div class="pagination">
            {{ $keywords->links() }}
        </div>

    </div>
</x-app-layout>
