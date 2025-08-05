<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            GA4 Directory URLs 一覧
        </h2>
    </x-slot>

    <div class="py-4 px-6">
        <table class="table-auto w-full border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-4 py-2">ID</th>
                    <th class="border px-4 py-2">URL</th>
                    <th class="border px-4 py-2">アクティブ</th>
                    <th class="border px-4 py-2">作成日</th>
                    <th class="border px-4 py-2">詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($urls as $url)
                <tr>
                    <td class="border px-4 py-2">{{ $url->id }}</td>
                    <td class="border px-4 py-2">{{ $url->url }}</td>
                    <td class="border px-4 py-2">{{ $url->is_active ? '✅' : '❌' }}</td>
                    <td class="border px-4 py-2">{{ $url->created_at }}</td>
                    <td class="border px-4 py-2">
                        <a href="{{ route('admin.urls.show', $url->id) }}" class="text-blue-600 underline">詳細</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $urls->links() }}
        </div>
    </div>
</x-app-layout>
