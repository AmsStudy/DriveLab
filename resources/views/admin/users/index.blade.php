@extends('layouts.admin')
@section('title', 'Kelola Admin')
@section('page-title', 'Kelola Admin')
@section('page-description', 'Approve, aktifkan, atau hapus akun admin')

@section('content')

{{-- Filter & Action --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
    <form method="GET" class="flex flex-wrap items-center gap-2.5 sm:gap-3 flex-1">
        <div class="relative w-full sm:w-56 md:w-60 flex-shrink-0">
            <svg class="w-4 h-4 text-gray-500 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."
                   class="w-full pr-3.5 border text-gray-100 rounded-xl py-2 text-sm placeholder-gray-500 focus:outline-none focus:border-emerald-500 transition-colors"
                   style="background-color:#090D0B;border-color:#1A2820;padding-left:2.5rem !important;">
        </div>
        <select name="status"
                class="cyber-select text-sm pl-3.5 pr-10 py-2">
            <option value="">Semua Status</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>✓ Aktif</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>✕ Nonaktif</option>
        </select>
        <button type="submit"
                class="px-4 py-2 rounded-xl text-sm font-semibold transition-colors text-white flex-shrink-0"
                style="background:linear-gradient(135deg,#10B981,#0D9488);">
            Filter
        </button>
        @if(request()->hasAny(['search','status']))
            <a href="{{ route('admin.users.index') }}"
               class="px-3 py-2 rounded-xl text-sm font-medium transition-colors flex-shrink-0"
               style="background:#1A2820;color:#9CA3AF;"
               onmouseover="this.style.background='#25382D';this.style.color='#d1d5db'"
               onmouseout="this.style.background='#1A2820';this.style.color='#9CA3AF'">Reset</a>
        @endif
    </form>

    <button type="button" onclick="document.getElementById('create-modal').classList.remove('hidden')"
            class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white transition-all shadow-md shadow-emerald-500/20 whitespace-nowrap flex-shrink-0"
            style="background:linear-gradient(135deg,#10B981,#0D9488);"
            onmouseover="this.style.boxShadow='0 6px 18px rgba(16,185,129,.35)'"
            onmouseout="this.style.boxShadow='0 4px 12px rgba(16,185,129,.2)'">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Asisten
    </button>
</div>

<div class="bg-[#0F1713] border border-[#1A2820] rounded-2xl overflow-hidden shadow-xl">
    @if($admins->isEmpty())
        <div class="py-16 text-center text-gray-500 text-sm">Tidak ada admin yang ditemukan.</div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[640px]">
                <thead class="border-b border-gray-800 text-xs text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3 text-left">Admin</th>
                        <th class="px-4 py-3 text-left">Institusi</th>
                        <th class="px-4 py-3 text-center">Dokumen</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Daftar</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60">
                @foreach($admins as $admin)
                <tr class="hover:bg-gray-800/30 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center flex-shrink-0">
                                <span class="text-white text-xs font-bold">{{ strtoupper(substr($admin->name, 0, 2)) }}</span>
                            </div>
                            <div>
                                <a href="{{ route('admin.users.show', $admin) }}" class="font-medium text-gray-100 hover:text-emerald-400 transition-colors">{{ $admin->name }}</a>
                                <p class="text-xs text-gray-500">{{ $admin->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4 text-gray-400 text-sm">{{ $admin->institution ?? '—' }}</td>
                    <td class="px-4 py-4 text-center">
                        <span class="text-gray-300 font-medium">{{ $admin->soals_count }}</span>
                    </td>
                    <td class="px-4 py-4">
                        <span class="text-xs px-2.5 py-1 rounded-full
                            {{ $admin->status === 'active' ? 'bg-green-500/15 text-green-400' : '' }}
                            {{ $admin->status === 'inactive' ? 'bg-gray-500/15 text-gray-400' : '' }}">
                            {{ ['active'=>'Aktif','inactive'=>'Nonaktif'][$admin->status] }}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-gray-500 text-xs">{{ $admin->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-1.5">
                            {{-- Detail --}}
                            <a href="{{ route('admin.users.show', $admin) }}"
                               id="show-admin-{{ $admin->id }}"
                               title="Lihat Detail"
                               class="p-1.5 rounded-lg text-gray-500 hover:text-blue-400 hover:bg-blue-500/10 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            @if($admin->status === 'active')
                                <form method="POST" action="{{ route('admin.users.reject', $admin) }}" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" id="deactivate-admin-{{ $admin->id }}"
                                            class="px-2.5 py-1.5 rounded-lg text-orange-400 hover:bg-orange-500/20 text-xs font-semibold transition-colors"
                                            title="Nonaktifkan">
                                        Nonaktif
                                    </button>
                                </form>
                            @elseif($admin->status === 'inactive')
                                <form method="POST" action="{{ route('admin.users.approve', $admin) }}" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" id="activate-admin-{{ $admin->id }}"
                                            class="px-2.5 py-1.5 rounded-lg text-emerald-400 hover:bg-emerald-500/20 text-xs font-semibold transition-colors"
                                            title="Aktifkan">
                                        Aktifkan
                                    </button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('admin.users.destroy', $admin) }}" class="inline"
                                  onsubmit="confirmAction(event, 'Hapus admin ini? Semua dokumennya akan ikut terhapus.', 'Ya, Hapus', true)">
                                @csrf @method('DELETE')
                                <button type="submit" id="delete-admin-{{ $admin->id }}"
                                        class="p-1.5 rounded-lg text-gray-500 hover:text-red-400 hover:bg-red-500/10 transition-colors">
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
        @if($admins->hasPages())
            <div class="px-6 py-4 border-t border-gray-800">{{ $admins->links() }}</div>
        @endif
    @endif
</div>

{{-- Create Modal --}}
<div id="create-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="document.getElementById('create-modal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-[#0F1713] rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-[#1A2820]">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="px-6 py-5 border-b border-[#1A2820] flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-semibold text-white" id="modal-title">Tambah Asisten Baru</h3>
                    <button type="button" onclick="document.getElementById('create-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Nama Lengkap</label>
                        <input type="text" name="name" required class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Email / Username</label>
                        <input type="email" name="email" required class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Password Default</label>
                        <input type="text" name="password" required value="{{ config('app.admin_default_password') }}" class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                        <p class="text-xs text-gray-500 mt-1">Asisten bisa mengganti passwordnya sendiri nanti di menu Profil.</p>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-[#1A2820] flex justify-end gap-3 bg-[#0A100D]/50">
                    <button type="button" onclick="document.getElementById('create-modal').classList.add('hidden')" class="bg-[#1A2820] hover:bg-[#25382D] text-gray-300 px-4 py-2 rounded-xl text-sm font-medium transition-colors">Batal</button>
                    <button type="submit" class="bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white px-5 py-2 rounded-xl text-sm font-semibold transition-all shadow-lg shadow-emerald-500/20">Simpan Asisten</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

