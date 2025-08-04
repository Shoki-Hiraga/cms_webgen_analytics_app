@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-xl font-bold mb-4">URL詳細</h1>

    <p><strong>ID:</strong> {{ $url->id }}</p>
    <p><strong>URL:</strong> {{ $url->url }}</p>
    <p><strong>アクティブ:</strong> {{ $url->is_active ? '有効' : '無効' }}</p>
    <p><strong>作成日:</strong> {{ $url->created_at }}</p>
    <p><strong>更新日:</strong> {{ $url->updated_at }}</p>

    <a href="{{ route('admin.urls.index') }}" class="text-blue-600 underline mt-4 inline-block">← 一覧に戻る</a>
</div>
@endsection
