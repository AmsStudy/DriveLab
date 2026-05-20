@extends('layouts.admin')
@section('title', 'Kelola Admin')
@section('page-title', 'Kelola Admin')
@section('page-description', 'Approve, aktifkan, atau hapus akun admin')

@section('content')

{{-- Filter & Action --}}
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."
               class="flex-1 min-w-60 bg-gray-800 border border-gray-700 text-gray-100 rounded-xl px-4 py-2.5 text-sm placeholder-gray-500 focus:outline-none focus:border-pink-500">
        <select name="status" class="bg-gray-800 border border-gray-700 text-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none">
            <option value="">Semua Status</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
        </select>
        <button type="submit" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition-colors">Filter</button>
    </form>
    
    <button type="button" onclick="document.getElementById('create-modal').classList.remove('hidden')" class="bg-pink-600 hover:bg-pink-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Asisten
    </button>
</div>

<div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden">
    @if($admins->isEmpty())
        <div class="py-16 text-center text-gray-500 text-sm">Tidak ada admin yang ditemukan.</div>
    @else
        <table class="w-full text-sm">
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
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-pink-500 to-fuchsia-600 flex items-center justify-center flex-shrink-0">
                                <span class="text-white text-xs font-bold">{{ strtoupper(substr($admin->name, 0, 2)) }}</span>
                            </div>
                            <div>
                                <a href="{{ route('admin.users.show', $admin) }}" class="font-medium text-gray-100 hover:text-pink-400 transition-colors">{{ $admin->name }}</a>
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
                        <div class="flex items-center justify-end gap-1">
                            @if($admin->status === 'active')
                                <form method="POST" action="{{ route('admin.users.reject', $admin) }}" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" id="deactivate-admin-{{ $admin->id }}"
                                            class="px-3 py-1.5 rounded-lg bg-orange-500/20 text-orange-400 hover:bg-orange-500/30 text-xs font-medium transition-colors">
                                        Nonaktifkan
                                    </button>
                                </form>
                            @elseif($admin->status === 'inactive')
                                <form method="POST" action="{{ route('admin.users.approve', $admin) }}" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" id="activate-admin-{{ $admin->id }}"
                                            class="px-3 py-1.5 rounded-lg bg-green-500/20 text-green-400 hover:bg-green-500/30 text-xs font-medium transition-colors">
                                        Aktifkan
                                    </button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('admin.users.destroy', $admin) }}" class="inline"
                                  onsubmit="confirmAction(event, 'Hapus admin ini? Semua dokumennya akan ikut terhapus.', 'Ya, Hapus', true)">
                                @csrf @method('DELETE')
                                <button type="submit" id="delete-admin-{{ $admin->id }}"
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
        <div class="inline-block align-bottom bg-gray-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-800">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="px-6 py-5 border-b border-gray-800 flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-medium text-white" id="modal-title">Tambah Asisten Baru</h3>
                    <button type="button" onclick="document.getElementById('create-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Nama Lengkap</label>
                        <input type="text" name="name" required class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:border-pink-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Email / Username</label>
                        <input type="email" name="email" required class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:border-pink-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Password Default</label>
                        <input type="text" name="password" required value="{{ config('app.admin_default_password') }}" class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:border-pink-500">
                        <p class="text-xs text-gray-500 mt-1">Asisten bisa mengganti passwordnya sendiri nanti di menu Profil.</p>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-800 flex justify-end gap-3 bg-gray-900/50">
                    <button type="button" onclick="document.getElementById('create-modal').classList.add('hidden')" class="bg-gray-800 hover:bg-gray-700 text-gray-300 px-4 py-2 rounded-xl text-sm font-medium transition-colors">Batal</button>
                    <button type="submit" class="bg-pink-600 hover:bg-pink-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors">Simpan Asisten</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

