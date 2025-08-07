<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">URL詳細</h2>
    </x-slot>

    <div class="detail-table-container">
        <table class="detail-table">
            <tbody>
                <tr>
                    <th>ID</th>
                    <td>{{ $url->id }}</td>
                </tr>
                <tr>
                    <th>URL</th>
                    <td>{{ $url->url }}</td>
                </tr>
                <tr>
                    <th>アクティブ</th>
                    <td>{{ $url->is_active ? '有効' : '無効' }}</td>
                </tr>
                <tr>
                    <th>作成日</th>
                    <td>{{ $url->created_at }}</td>
                </tr>
                <tr>
                    <th>更新日</th>
                    <td>{{ $url->updated_at }}</td>
                </tr>
            </tbody>
        </table>
        <a href="{{ route('admin.gsc_fullurl_listurls.index') }}" class="back-link">← 一覧に戻る</a>
    </div>
</x-app-layout>
