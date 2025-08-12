<x-app-layout>
    <x-slot name="header">
        <x-slot name="title">一覧 | GSC設定</x-slot>
        <h2 class="page-title">GSC設定 一覧</h2>
    </x-slot>

    <div class="table-container">
        @if(!$setting)
            {{-- データがない場合のみ新規作成ボタンを表示 --}}
            <a href="{{ route('admin.gsc_settings.create') }}" class="btn-primary">＋ 新規追加</a>
        @endif

        <table class="table-base">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>サイトURL</th>
                    <th>作成日</th>
                    <th>更新日</th>
                    <th>編集</th>
                </tr>
            </thead>
            <tbody>
                @if($setting)
                    <tr>
                        <td>{{ $setting->id }}</td>
                        <td>{{ $setting->site_url }}</td>
                        <td>{{ $setting->created_at }}</td>
                        <td>{{ $setting->updated_at }}</td>
                        <td>
                            <a href="{{ route('admin.gsc_settings.edit', $setting->id) }}" class="btn-secondary">編集</a>
                        </td>
                    </tr>
                @else
                    <tr>
                        <td colspan="5" style="text-align: center;">設定が登録されていません</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</x-app-layout>
