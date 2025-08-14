<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">{{ $script }} のログ</h2>
    </x-slot>

    <div class="log-controls" style="margin-bottom: 10px;">
        @if ($isRunning ?? false)
            <form action="{{ route('admin.python.stop', $script) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn-danger">停止</button>
            </form>
        @else
            <p>現在実行中ではありません。</p>
        @endif
        <a href="{{ route('admin.python.log', $script) }}" class="btn-secondary">更新</a>
        <a href="{{ route('admin.python.index') }}" class="btn-primary">戻る</a>
    </div>

    <div id="log-container"
         style="background-color: #000; color: #0f0; font-family: monospace;
                padding: 15px; border-radius: 5px; white-space: pre-wrap;
                overflow-y: auto; height: 500px;">
        {!! nl2br(e($log)) !!}
    </div>

    <script>
        // ページロード時に最下部へ自動スクロール
        const logContainer = document.getElementById('log-container');
        logContainer.scrollTop = logContainer.scrollHeight;
    </script>
</x-app-layout>
