@extends('layouts.admin')
@section('title', 'Kategori')
@section('page-title', 'Kelola Kategori')
@section('page-description', 'Atur pengelompokan soal berdasarkan kategori')

@section('content')
<div class="flex justify-end mb-5">
    <a href="{{ route('admin.categories.create') }}" id="add-category-btn"
       class="bg-gradient-to-r from-pink-600 to-fuchsia-700 hover:from-pink-700 hover:to-fuchsia-800 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition-all flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Kategori
    </a>
</div>

<div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden">
    @if($categories->isEmpty())
        <div class="py-16 text-center text-gray-500">
            <p>Belum ada kategori. Buat kategori pertama Anda!</p>
        </div>
    @else
        <table class="w-full text-sm">
            <thead class="border-b border-gray-800 text-xs text-gray-500 uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3 text-left">Kategori</th>
                    <th class="px-4 py-3 text-left">Deskripsi</th>
                    @if(auth()->user()->isSuperAdmin()) <th class="px-4 py-3 text-left">Dibuat Oleh</th> @endif
                    <th class="px-4 py-3 text-center">Jumlah Soal</th>
                    <th class="px-6 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800/60">
                @foreach($categories as $category)
                <tr class="hover:bg-gray-800/30 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: {{ $category->color }}"></span>
                            <span class="font-medium text-gray-100">{{ $category->name }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-4 text-gray-400 text-xs max-w-xs truncate">{{ $category->description ?? '—' }}</td>
                    @if(auth()->user()->isSuperAdmin())
                        <td class="px-4 py-4 text-gray-400 text-sm">{{ $category->creator->name }}</td>
                    @endif
                    <td class="px-4 py-4 text-center">
                        <span class="bg-gray-800 text-gray-300 text-xs px-2.5 py-1 rounded-full">{{ $category->soals_count }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('admin.categories.edit', $category) }}"
                               id="edit-category-{{ $category->id }}"
                               class="p-2 rounded-lg text-gray-500 hover:text-yellow-400 hover:bg-yellow-500/10 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline"
                                  onsubmit="confirmAction(event, 'Hapus kategori ini? dokumen yang terkait tidak akan ikut terhapus.', 'Ya, Hapus', true)">
                                @csrf @method('DELETE')
                                <button type="submit" id="delete-category-{{ $category->id }}"
                                        class="p-2 rounded-lg text-gray-500 hover:text-red-400 hover:bg-red-500/10 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($categories->hasPages())
            <div class="px-6 py-4 border-t border-gray-800">{{ $categories->links() }}</div>
        @endif
    @endif
</div>
@endsection

