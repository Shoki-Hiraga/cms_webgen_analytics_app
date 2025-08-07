<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">GSC クエリ一覧</h2>
    </x-slot>

    <div class="table-container">
        <a href="{{ route('admin.gsc_query_listqueries.create') }}" class="btn-primary">＋ 新規追加</a>

        <table class="table-base">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>クエリ</th>
                    <th>アクティブ</th>
                    <th>作成日</th>
                    <th>詳細</th>
                    <th>編集</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($queries as $query)
                <tr>
                    <td>{{ $query->id }}</td>
                    <td>{{ $query->query }}</td>
                    <td>{{ $query->is_active ? '✅' : '❌' }}</td>
                    <td>{{ $query->created_at }}</td>
                    <td><a href="{{ route('admin.gsc_query_listqueries.show', $query->id) }}">詳細</a></td>
                    <td><a href="{{ route('admin.gsc_query_listqueries.edit', $query->id) }}">編集</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination">
            {{ $queries->links() }}
        </div>
    </div>
</x-app-layout>
