<x-app-layout>
    <x-slot name="header">
        <x-slot name="title">一覧 | データ取得設定</x-slot>
        <h2 class="page-title">データ取得設定 一覧</h2>
    </x-slot>

    <div class="table-container">
        <a href="{{ route('admin.dataseting.create') }}" class="btn-primary">＋ 新規追加</a>

        <table class="table-base">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ターゲット</th>
                    <th>開始年</th>
                    <th>開始月</th>
                    <th>作成日</th>
                    <th>更新日</th>
                    <th>編集</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($settings as $setting)
                    <tr>
                        <td>{{ $setting->id }}</td>
                        <td>{{ $setting->target }}</td>
                        <td>{{ $setting->start_year }}</td>
                        <td>{{ $setting->start_month }}</td>
                        <td>{{ $setting->created_at }}</td>
                        <td>{{ $setting->updated_at }}</td>
                        <td>
                            <a href="{{ route('admin.dataseting.edit', $setting->id) }}">編集</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">設定が登録されていません。</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if (method_exists($settings, 'links'))
            <div class="pagination">
                {{ $settings->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
