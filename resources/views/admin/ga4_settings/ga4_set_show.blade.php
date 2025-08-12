<x-app-layout>
    <x-slot name="header">
        <x-slot name="title">詳細 | @include('components.Gsc_Queries') </x-slot>
        <h2 class="page-title">@include('components.Gsc_Queries') 詳細</h2>
    </x-slot>

    <div class="detail-table-container">
        <table class="detail-table">
            <tbody>
                <tr>
                    <th>ID</th>
                    <td>{{ $query->id }}</td>
                </tr>
                <tr>
                    <th>クエリ</th>
                    <td>{{ $query->query }}</td>
                </tr>
                <tr>
                    <th>アクティブ</th>
                    <td>{{ $query->is_active ? '有効' : '無効' }}</td>
                </tr>
                <tr>
                    <th>作成日</th>
                    <td>{{ $query->created_at }}</td>
                </tr>
                <tr>
                    <th>更新日</th>
                    <td>{{ $query->updated_at }}</td>
                </tr>
            </tbody>
        </table>
        <a href="{{ route('admin.gsc_query_listqueries.index') }}" class="back-link">← 一覧に戻る</a>
    </div>
</x-app-layout>
