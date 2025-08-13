<x-app-layout>
    <x-slot name="header">
        <x-slot name="title">{{ $script }} | Python 実行ログ</x-slot>
        <h2 class="page-title">{{ $script }} 実行ログ</h2>
    </x-slot>

    <div class="log-container">
        <div class="log-actions" style="margin-bottom:1rem;">
            <a href="{{ route('admin.python.index') }}" class="btn-secondary">← 戻る</a>

            {{-- 更新ボタン --}}
            <a href="{{ route('admin.python.log', ['script' => $script]) }}" class="btn-primary">🔄 更新</a>

            {{-- Linuxのみ停止ボタン表示 --}}
            @if ($isRunning)
                <form action="{{ route('admin.python.stop', ['script' => $script]) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-danger" onclick="return confirm('本当に停止しますか？')">停止</button>
                </form>
            @endif
        </div>

        <div class="log-output" style="background:#111;color:#0f0;padding:1rem;white-space:pre-wrap;max-height:70vh;overflow:auto;">
            {!! $log ? e($log) : 'まだログはありません。' !!}
        </div>
    </div>
</x-app-layout>
