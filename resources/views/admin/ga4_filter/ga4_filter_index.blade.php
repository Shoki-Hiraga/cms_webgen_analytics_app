<x-app-layout>
    <x-slot name="header">
        <x-slot name="title">一覧 | フィルター設定</x-slot>
        <h2 class="page-title">セッションメディアフィルター 一覧</h2>
    </x-slot>

    <div class="table-container">
        @if($filters->isEmpty())
            {{-- データがない場合のみ新規作成ボタンを表示 --}}
            <a href="{{ route('admin.ga4_filters.create') }}" class="btn-primary">＋ 新規追加</a>
        @endif

        <table class="table-base">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>GA4設定ID</th>
                    <th>セッションメディアフィルター</th>
                    <th>作成日</th>
                    <th>更新日</th>
                    <th>編集</th>
                </tr>
            </thead>
            <tbody>
                @forelse($filters as $filter)
                    <tr>
                        <td>{{ $filter->id }}</td>
                        <td>{{ $filter->ga4_setting_id }}</td>
                        <td>{{ $filter->session_medium_filter }}</td>
                        <td>{{ $filter->created_at }}</td>
                        <td>{{ $filter->updated_at }}</td>
                        <td>
                            <a href="{{ route('admin.ga4_filters.edit', $filter->ga4_setting_id) }}" class="btn-secondary">編集</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center;">フィルター設定が登録されていません</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
