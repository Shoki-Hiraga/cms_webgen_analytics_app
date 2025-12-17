<x-app-layout>
    <x-slot name="header">
        <x-slot name="title">詳細 | Set Slug</x-slot>
        <h2 class="page-title">Set Slug 詳細</h2>
    </x-slot>

    <div class="detail-table-container">
        <table class="detail-table">
            <tbody>
                <tr>
                    <th>ID</th>
                    <td>{{ $set_slug->id }}</td>
                </tr>

                <tr>
                    <th>Slug</th>
                    <td>{{ $set_slug->slug }}</td>
                </tr>

                <tr>
                    <th>表示名</th>
                    <td>{{ $set_slug->label }}</td>
                </tr>

                <tr>
                    <th>Type</th>
                    <td>{{ strtoupper($set_slug->type) }}</td>
                </tr>

                <tr>
                    <th>Handler</th>
                    <td>{{ $set_slug->handler ?? '-' }}</td>
                </tr>

                <tr>
                    <th>親 Slug</th>
                    <td>{{ optional($set_slug->parent)->label ?? '-' }}</td>
                </tr>

                <tr>
                    <th>公開状態</th>
                    <td>{{ $set_slug->active ? '公開' : '非公開' }}</td>
                </tr>

                <tr>
                    <th>並び順</th>
                    <td>{{ $set_slug->sort_order }}</td>
                </tr>

                <tr>
                    <th>作成日</th>
                    <td>{{ $set_slug->created_at }}</td>
                </tr>

                <tr>
                    <th>更新日</th>
                    <td>{{ $set_slug->updated_at }}</td>
                </tr>
            </tbody>
        </table>

        <a href="{{ route('admin.set_slugs.index') }}" class="back-link">
            ← 一覧に戻る
        </a>
    </div>

</x-app-layout>
