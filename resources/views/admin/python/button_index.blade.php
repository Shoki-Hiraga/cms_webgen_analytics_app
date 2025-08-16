<x-app-layout>
    <x-slot name="header">
        <x-slot name="title">Python実行一覧 | 管理画面</x-slot>
        <h2 class="page-title">Python 実行一覧</h2>
    </x-slot>

    <div class="table-container">
        <table class="table-base">
            <thead>
                <tr>
                    <th>スクリプト名</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($scripts as $script)
                <tr>
                    <td>{{ $script }}</td>
                    <td>
                        @if ($statuses[$script])
                            <a href="{{ route('admin.python.log', $script) }}" class="btn-secondary">
                                ログを開く
                            </a>
                        @else
                            <a href="{{ route('admin.python.run', $script) }}" class="btn-primary">
                                API を 実行
                            </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
