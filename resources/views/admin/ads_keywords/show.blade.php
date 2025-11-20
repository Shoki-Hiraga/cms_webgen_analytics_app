<x-app-layout>
    <x-slot name="header">
        <x-slot name="title">詳細 | Organic Keywords</x-slot>
        <h2 class="page-title">Organic Keyword 詳細</h2>
    </x-slot>

    <div class="detail-table-container">
        <table class="detail-table">
            <tbody>
                <tr>
                    <th>ID</th>
                    <td>{{ $keyword->id }}</td>
                </tr>

                <tr>
                    <th>キーワード</th>
                    <td>{{ $keyword->keyword }}</td>
                </tr>

                <tr>
                    <th>作成日</th>
                    <td>{{ $keyword->created_at }}</td>
                </tr>

                <tr>
                    <th>更新日</th>
                    <td>{{ $keyword->updated_at }}</td>
                </tr>
            </tbody>
        </table>

        <a href="{{ route('admin.organic_keywords.index') }}" class="back-link">← 一覧に戻る</a>
    </div>

</x-app-layout>
