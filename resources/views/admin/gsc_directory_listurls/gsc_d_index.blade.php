<x-app-layout>
    <x-slot name="header">
        <x-slot name="title">一覧 | @include('components.Gsc_Directory_Urls') </x-slot>
        <h2 class="page-title">@include('components.Gsc_Directory_Urls') 一覧</h2>
    </x-slot>

    <div class="table-container">
        <a href="{{ route('admin.gsc_directory_listurls.create') }}" class="btn-primary">＋ 新規追加</a>
            <div class="actions">
                <a href="{{ route('admin.gsc_directory_listurls.export') }}" class="btn-secondary">CSVエクスポート</a>

                <form action="{{ route('admin.gsc_directory_listurls.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <button type="submit" class="btn-primary">CSVインポート</button>
                    <input type="file" name="csv_file" accept=".csv" required>
                </form>
            </div>

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
                        <a href="{{ route('admin.gsc_directory_listurls.show', $url->id) }}">詳細</a>
                    </td>
                    <td>
                        <a href="{{ route('admin.gsc_directory_listurls.edit', $url->id) }}">編集</a>
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
