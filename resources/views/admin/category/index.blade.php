@extends('layouts.admin')
@section('title', 'Kategori')
@section('page-title', 'Kelola Kategori')
@section('page-description', 'Atur pengelompokan soal berdasarkan kategori')

@section('content')
<div class="flex justify-end mb-5">
    <button type="button" onclick="openCreateCategoryModal()" id="add-category-btn"
       class="bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition-all flex items-center gap-2 shadow-lg shadow-emerald-500/20 cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Kategori
    </button>
</div>

<div class="bg-white dark:bg-[#0F1713] border border-gray-200 dark:border-[#1A2820] rounded-2xl overflow-hidden shadow-xs">
    @if($categories->isEmpty())
        <div class="py-16 text-center text-gray-500">
            <p>Belum ada kategori. Buat kategori pertama Anda!</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[540px]">
                <thead class="border-b border-gray-200 dark:border-gray-800 text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-transparent">
                    <tr>
                        <th class="px-6 py-3.5 text-left font-semibold">Kategori</th>
                        <th class="px-4 py-3.5 text-left font-semibold">Deskripsi</th>
                        @if(auth()->user()->isSuperAdmin()) <th class="px-4 py-3.5 text-left font-semibold">Dibuat Oleh</th> @endif
                        <th class="px-4 py-3.5 text-center font-semibold">Jumlah Soal</th>
                        <th class="px-6 py-3.5 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800/60">
                    @foreach($categories as $category)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <span class="w-3.5 h-3.5 rounded-full flex-shrink-0 shadow-xs" style="background-color: {{ $category->color }}"></span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $category->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-gray-600 dark:text-gray-400 text-xs max-w-xs truncate">{{ $category->description ?? '—' }}</td>
                        @if(auth()->user()->isSuperAdmin())
                            <td class="px-4 py-4 text-gray-600 dark:text-gray-400 text-sm">{{ $category->creator->name }}</td>
                        @endif
                        <td class="px-4 py-4 text-center">
                            <span class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-transparent text-xs px-2.5 py-1 rounded-full font-medium">{{ $category->soals_count }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-1">
                                <button type="button"
                                        onclick="openEditCategoryModal({{ json_encode($category) }})"
                                        id="edit-category-{{ $category->id }}"
                                        title="Edit Kategori"
                                        class="p-2 rounded-lg text-gray-500 hover:text-amber-500 hover:bg-amber-500/10 transition-colors cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline"
                                      onsubmit="confirmAction(event, 'Hapus kategori ini? dokumen yang terkait tidak akan ikut terhapus.', 'Ya, Hapus', true)">
                                    @csrf @method('DELETE')
                                    <button type="submit" id="delete-category-{{ $category->id }}"
                                            title="Hapus Kategori"
                                            class="p-2 rounded-lg text-gray-500 hover:text-red-500 hover:bg-red-500/10 transition-colors cursor-pointer">
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
        </div>
        @if($categories->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800">{{ $categories->links() }}</div>
        @endif
    @endif
</div>

{{-- ─── POP UP MODAL: TAMBAH KATEGORI ─── --}}
<div id="createCategoryModal" class="fixed inset-0 z-[100] flex items-center justify-center p-3 sm:p-6 hidden" style="z-index: 100;">
    {{-- Backdrop with blurred background --}}
    <div class="modal-backdrop fixed inset-0 bg-black/60 dark:bg-black/80 backdrop-blur-md transition-opacity" onclick="closeCreateCategoryModal()"></div>

    {{-- Modal Card --}}
    <div class="modal-card relative bg-white dark:bg-[#0F1713] border border-gray-200 dark:border-[#1A2820] rounded-2xl w-full max-w-lg shadow-2xl z-10 overflow-hidden">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-[#1A2820] bg-gray-50/90 dark:bg-[#090E0B]">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white leading-tight">Tambah Kategori Baru</h3>
            </div>
            <button type="button" onclick="closeCreateCategoryModal()" class="text-gray-400 hover:text-gray-700 dark:hover:text-white p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors cursor-pointer" title="Tutup Modal (Esc)">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Form Body --}}
        <form method="POST" action="{{ route('admin.categories.store') }}" id="create-category-form" class="p-6 space-y-4">
            @csrf
            <div>
                <label for="create_category_name" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5 uppercase tracking-wider">Nama Kategori <span class="text-red-500">*</span></label>
                <input type="text" id="create_category_name" name="name" value="{{ old('name') }}" required
                       placeholder="Contoh: Pemrograman Web"
                       class="w-full bg-white dark:bg-[#090E0B] border border-gray-300 dark:border-[#1A2820] text-gray-900 dark:text-gray-100 rounded-xl px-4 py-2.5 text-xs placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20">
                @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="create_category_description" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5 uppercase tracking-wider">Deskripsi (Opsional)</label>
                <textarea id="create_category_description" name="description" rows="3" placeholder="Keterangan singkat tentang kategori ini..."
                          class="w-full bg-white dark:bg-[#090E0B] border border-gray-300 dark:border-[#1A2820] text-gray-900 dark:text-gray-100 rounded-xl px-4 py-2.5 text-xs placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 resize-none">{{ old('description') }}</textarea>
            </div>
            <div>
                <label for="create_category_color" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5 uppercase tracking-wider">Warna Label</label>
                <div class="flex items-center gap-3">
                    <input type="color" id="create_category_color" name="color" value="{{ old('color', '#10b981') }}"
                           class="w-12 h-9 rounded-lg bg-white dark:bg-[#090E0B] border border-gray-300 dark:border-[#1A2820] cursor-pointer p-1">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Pilih warna untuk identifikasi visual kategori</span>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-200 dark:border-[#1A2820]">
                <button type="button" onclick="closeCreateCategoryModal()"
                        class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white bg-gray-100 hover:bg-gray-200 dark:bg-transparent dark:hover:bg-gray-800 transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" id="submit-category-btn"
                        class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 shadow-md shadow-emerald-600/20 transition-all cursor-pointer">
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ─── POP UP MODAL: EDIT KATEGORI ─── --}}
<div id="editCategoryModal" class="fixed inset-0 z-[100] flex items-center justify-center p-3 sm:p-6 hidden" style="z-index: 100;">
    {{-- Backdrop with blurred background --}}
    <div class="modal-backdrop fixed inset-0 bg-black/60 dark:bg-black/80 backdrop-blur-md transition-opacity" onclick="closeEditCategoryModal()"></div>

    {{-- Modal Card --}}
    <div class="modal-card relative bg-white dark:bg-[#0F1713] border border-gray-200 dark:border-[#1A2820] rounded-2xl w-full max-w-lg shadow-2xl z-10 overflow-hidden">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-[#1A2820] bg-gray-50/90 dark:bg-[#090E0B]">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center bg-amber-500/10 border border-amber-500/20 text-amber-600 dark:text-amber-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white leading-tight">Edit Kategori</h3>
            </div>
            <button type="button" onclick="closeEditCategoryModal()" class="text-gray-400 hover:text-gray-700 dark:hover:text-white p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors cursor-pointer" title="Tutup Modal (Esc)">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Form Body --}}
        <form method="POST" action="" id="edit-category-form" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="edit_category_name" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5 uppercase tracking-wider">Nama Kategori <span class="text-red-500">*</span></label>
                <input type="text" id="edit_category_name" name="name" required
                       placeholder="Contoh: Pemrograman Web"
                       class="w-full bg-white dark:bg-[#090E0B] border border-gray-300 dark:border-[#1A2820] text-gray-900 dark:text-gray-100 rounded-xl px-4 py-2.5 text-xs placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20">
            </div>
            <div>
                <label for="edit_category_description" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5 uppercase tracking-wider">Deskripsi (Opsional)</label>
                <textarea id="edit_category_description" name="description" rows="3" placeholder="Keterangan singkat tentang kategori ini..."
                          class="w-full bg-white dark:bg-[#090E0B] border border-gray-300 dark:border-[#1A2820] text-gray-900 dark:text-gray-100 rounded-xl px-4 py-2.5 text-xs placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 resize-none"></textarea>
            </div>
            <div>
                <label for="edit_category_color" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5 uppercase tracking-wider">Warna Label</label>
                <div class="flex items-center gap-3">
                    <input type="color" id="edit_category_color" name="color"
                           class="w-12 h-9 rounded-lg bg-white dark:bg-[#090E0B] border border-gray-300 dark:border-[#1A2820] cursor-pointer p-1">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Pilih warna untuk identifikasi visual kategori</span>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-200 dark:border-[#1A2820]">
                <button type="button" onclick="closeEditCategoryModal()"
                        class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white bg-gray-100 hover:bg-gray-200 dark:bg-transparent dark:hover:bg-gray-800 transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" id="update-category-btn"
                        class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 shadow-md shadow-emerald-600/20 transition-all cursor-pointer">
                    Perbarui Kategori
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openCreateCategoryModal() {
    const modal = document.getElementById('createCategoryModal');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        setTimeout(() => document.getElementById('create_category_name')?.focus(), 50);
    }
}

function closeCreateCategoryModal() {
    const modal = document.getElementById('createCategoryModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

function openEditCategoryModal(category) {
    const modal = document.getElementById('editCategoryModal');
    const form = document.getElementById('edit-category-form');
    if (!modal || !form || !category) return;

    form.action = "{{ url('admin/categories') }}/" + category.id;
    document.getElementById('edit_category_name').value = category.name || '';
    document.getElementById('edit_category_description').value = category.description || '';
    document.getElementById('edit_category_color').value = category.color || '#10b981';

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    setTimeout(() => document.getElementById('edit_category_name')?.focus(), 50);
}

function closeEditCategoryModal() {
    const modal = document.getElementById('editCategoryModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCreateCategoryModal();
        closeEditCategoryModal();
    }
});

@if(request()->has('open_create') || ($errors->any() && !old('_method')))
    document.addEventListener('DOMContentLoaded', () => {
        openCreateCategoryModal();
    });
@endif

@if(request()->has('open_edit'))
    document.addEventListener('DOMContentLoaded', () => {
        const catId = {{ (int)request('open_edit') }};
        const allCategories = @json($categories->items());
        const target = allCategories.find(c => c.id === catId);
        if (target) openEditCategoryModal(target);
    });
@endif
</script>
@endsection

