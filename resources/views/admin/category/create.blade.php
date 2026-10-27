@extends('layouts.admin')
@section('title', 'Tambah Kategori')
@section('page-title', 'Tambah Kategori')

@section('content')
<div class="max-w-lg">
    <form method="POST" action="{{ route('admin.categories.store') }}" id="create-category-form">
        @csrf
        <div class="bg-[#0F1713] border border-[#1A2820] rounded-2xl p-6 space-y-5">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Nama Kategori <span class="text-red-400">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                       placeholder="Contoh: Pemrograman Web"
                       class="w-full bg-[#090E0B] border {{ $errors->has('name') ? 'border-red-500' : 'border-[#1A2820]' }} text-gray-100 rounded-xl px-4 py-3 text-sm placeholder-gray-500 focus:outline-none focus:border-emerald-500">
                @error('name') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Deskripsi</label>
                <textarea id="description" name="description" rows="2" placeholder="Keterangan singkat tentang kategori ini..."
                          class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-4 py-3 text-sm placeholder-gray-500 focus:outline-none focus:border-emerald-500 resize-none">{{ old('description') }}</textarea>
            </div>
            <div>
                <label for="color" class="block text-sm font-medium text-gray-300 mb-2">Warna Label</label>
                <div class="flex items-center gap-3">
                    <input type="color" id="color" name="color" value="{{ old('color', '#10b981') }}"
                           class="w-12 h-10 rounded-lg bg-[#090E0B] border border-[#1A2820] cursor-pointer p-1">
                    <span class="text-sm text-gray-400">Pilih warna untuk badge kategori</span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3 mt-5">
            <button type="submit" id="submit-category-btn"
                    class="bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white px-8 py-3 rounded-xl font-semibold text-sm transition-all shadow-lg shadow-emerald-500/20">
                Simpan Kategori
            </button>
            <a href="{{ route('admin.categories.index') }}" class="text-gray-400 hover:text-gray-200 px-4 py-3 rounded-xl text-sm">Batal</a>
        </div>
    </form>
</div>
@endsection

