@extends('layouts.admin')
@section('title', 'Edit Kategori')
@section('page-title', 'Edit Kategori')

@section('content')
<div class="max-w-lg">
    <form method="POST" action="{{ route('admin.categories.update', $category) }}" id="edit-category-form">
        @csrf @method('PUT')
        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 space-y-5">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Nama Kategori <span class="text-red-400">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" required
                       class="w-full bg-gray-800 border {{ $errors->has('name') ? 'border-red-500' : 'border-gray-700' }} text-gray-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-pink-500">
                @error('name') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Deskripsi</label>
                <textarea id="description" name="description" rows="2"
                          class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-pink-500 resize-none">{{ old('description', $category->description) }}</textarea>
            </div>
            <div>
                <label for="color" class="block text-sm font-medium text-gray-300 mb-2">Warna Label</label>
                <div class="flex items-center gap-3">
                    <input type="color" id="color" name="color" value="{{ old('color', $category->color) }}"
                           class="w-12 h-10 rounded-lg bg-gray-800 border border-gray-700 cursor-pointer p-1">
                    <span class="text-sm text-gray-400">Pilih warna untuk badge kategori</span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3 mt-5">
            <button type="submit" id="update-category-btn"
                    class="bg-gradient-to-r from-pink-600 to-fuchsia-700 hover:from-pink-700 hover:to-fuchsia-800 text-white px-8 py-3 rounded-xl font-medium text-sm transition-all">
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.categories.index') }}" class="text-gray-400 hover:text-gray-200 px-4 py-3 rounded-xl text-sm">Batal</a>
        </div>
    </form>
</div>
@endsection

