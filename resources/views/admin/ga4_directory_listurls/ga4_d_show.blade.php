<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            URL詳細
        </h2>
    </x-slot>

    <div class="py-4 px-6">
        <p><strong>ID:</strong> {{ $url->id }}</p>
        <p><strong>URL:</strong> {{ $url->url }}</p>
        <p><strong>アクティブ:</strong> {{ $url->is_active ? '有効' : '無効' }}</p>
        <p><strong>作成日:</strong> {{ $url->created_at }}</p>
        <p><strong>更新日:</strong> {{ $url->updated_at }}</p>

        <a href="{{ route('admin.urls.index') }}" class="text-blue-600 underline mt-4 inline-block">← 一覧に戻る</a>
    </div>
</x-app-layout>
