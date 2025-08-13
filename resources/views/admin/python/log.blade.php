<x-app-layout>
    <x-slot name="header">
        <x-slot name="title">{{ $script }} 実行ログ | 管理画面</x-slot>
        <h2 class="page-title">{{ $script }} 実行ログ</h2>
    </x-slot>

    <div class="table-container">
        <div style="background-color:#222; color:#0f0; padding:10px; min-height:300px; white-space:pre-wrap; font-family:monospace;">
            {{ $log ?: '[ログがまだありません]' }}
        </div>

        <div class="actions" style="margin-top:10px;">
            @if ($isRunning)
                <form action="{{ route('admin.python.stop', $script) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-danger">停止する</button>
                </form>
            @endif
            <a href="{{ route('admin.python.log', $script) }}" class="btn-secondary">更新</a>
            <a href="{{ route('admin.python.index') }}" class="btn-primary">一覧に戻る</a>
        </div>
    </div>

    {{-- 自動更新 --}}
    <meta http-equiv="refresh" content="5">
</x-app-layout>
