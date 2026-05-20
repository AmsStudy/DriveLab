@extends('layouts.admin')
@section('title', 'Log Akses')
@section('page-title', 'Log Akses Dokumen')
@section('page-description', 'Riwayat lengkap semua percobaan Akses Dokumen')

@section('content')

{{-- Filter --}}
<form method="GET" class="flex flex-wrap gap-3 mb-5">
    <select name="status" class="bg-gray-800 border border-gray-700 text-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-pink-500">
        <option value="">Semua Status</option>
        <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Berhasil</option>
        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Gagal</option>
    </select>
    <input type="date" name="date" value="{{ request('date') }}"
           class="bg-gray-800 border border-gray-700 text-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-pink-500">
    <button type="submit" class="bg-pink-600 hover:bg-pink-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition-colors">Filter</button>
    @if(request()->anyFilled(['status', 'date']))
        <a href="{{ route('admin.logs.index') }}" class="bg-gray-800 hover:bg-gray-700 text-gray-300 px-4 py-2.5 rounded-xl text-sm transition-colors">Reset</a>
    @endif
</form>

<div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden">
    @if($logs->isEmpty())
        <div class="py-16 text-center text-gray-500">
            <p>Belum ada log akses.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-800 text-xs text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3 text-left">Soal</th>
                        <th class="px-4 py-3 text-left">Waktu</th>
                        <th class="px-4 py-3 text-left">IP Address</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60">
                    @foreach($logs as $log)
                    <tr class="hover:bg-gray-800/30 transition-colors">
                        <td class="px-6 py-3">
                            <p class="text-gray-200 text-sm truncate max-w-xs">{{ $log->soal->title ?? 'Dokumen Dihapus' }}</p>
                            @if($log->soal)
                                <a href="{{ route('admin.soal.show', $log->soal) }}" class="text-xs text-pink-400 hover:underline">Lihat dokumen</a>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs whitespace-nowrap">
                            {{ $log->accessed_at->format('d M Y H:i:s') }}
                            <p class="text-gray-600">{{ $log->accessed_at->diffForHumans() }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-300 font-mono text-xs">{{ $log->ip_address }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full
                                {{ $log->status === 'success' ? 'bg-green-500/15 text-green-400' : 'bg-red-500/15 text-red-400' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $log->status === 'success' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                {{ $log->status === 'success' ? 'Berhasil' : 'Gagal' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">
                            {{ ['wrong_password'=>'Password salah','expired'=>'Sudah expired','inactive'=>'Soal nonaktif'][$log->reason] ?? '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-gray-800">{{ $logs->withQueryString()->links() }}</div>
        @endif
    @endif
</div>
@endsection

