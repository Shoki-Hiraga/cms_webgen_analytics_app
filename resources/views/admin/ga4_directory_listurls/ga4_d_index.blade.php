<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">GA4 Directory URLs 一覧</h2>
    </x-slot>

    <div class="table-container">
        <table class="table-base">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>URL</th>
                    <th>アクティブ</th>
                    <th>作成日</th>
                    <th>詳細</th>
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
                        <a href="{{ route('admin.urls.show', $url->id) }}">詳細</a>
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
