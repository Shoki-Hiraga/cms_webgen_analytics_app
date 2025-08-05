<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">GA4 Directory URLs 一覧</h2>
    </x-slot>

    <div class="table-container">
        <a href="{{ route('admin.ga4_directory_listurls.create') }}" class="btn-primary">＋ 新規追加</a>

        <table class="table-base">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>URL</th>
                    <th>アクティブ</th>
                    <th>作成日</th>
                    <th>詳細</th>
                    <th>編集</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($urls as $url)
                <tr>
                    <td>{{ $url->id }}</td>
                    <td>{{ $url->url }}</td>
                    <td>{{ $url->is_active ? '✅' : '❌' }}</td>
                    <td>{{ $url->created_at }}</td>
                    <td>
                        <a href="{{ route('admin.ga4_directory_listurls.show', $url->id) }}">詳細</a>
                    </td>
                    <td>
                        <a href="{{ route('admin.ga4_directory_listurls.edit', $url->id) }}">編集</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination">
            {{ $urls->links() }}
        </div>
    </div>
</x-app-layout>
