<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("ダッシュボード") }}
                </div>
            </div>
        </div>
    </div>

    <x-slot name="header">
        <h2 class="page-title">ログイン中</h2>
    </x-slot>

    <div class="link-grid">
        <a href="{{ route('top') }}" class="link-card">URL管理ページTOP</a>

    </div>
    
</x-app-layout>

