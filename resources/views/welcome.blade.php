<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LabDrive — Platform Distribusi Soal & Modul</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg:    #0D0D14;
            --card:  #13131F;
            --border:#1E1E2E;
            --pink:  #F72585;
            --pink2: #C1005D;
            --muted: #6B7280;
            --text:  #E5E7EB;
        }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; display: flex; flex-direction: column; overflow-x: hidden; }

        /* Glow blobs */
        .blob { position: fixed; border-radius: 50%; filter: blur(120px); pointer-events: none; z-index: 0; }
        .blob-1 { width: 500px; height: 500px; background: rgba(247,37,133,.08); top: -10%; left: -10%; }
        .blob-2 { width: 600px; height: 600px; background: rgba(247,37,133,.05); bottom: -20%; right: -15%; }

        /* Nav */
        nav { position: sticky; top: 0; z-index: 50; border-bottom: 1px solid var(--border); background: rgba(13,13,20,.85); backdrop-filter: blur(16px); }
        .nav-inner { max-width: 1200px; margin: 0 auto; padding: 0 24px; display: flex; align-items: center; justify-content: space-between; height: 64px; }
        .logo { display: flex; align-items: center; gap: 10px; text-decoration: none; }
        .logo-icon { width: 36px; height: 36px; background: linear-gradient(135deg, var(--pink), #7209B7); border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .logo-text { font-weight: 800; font-size: 20px; color: #fff; letter-spacing: -.5px; }
        .nav-links { display: flex; align-items: center; gap: 12px; }
        .btn-ghost { color: var(--muted); font-size: 14px; font-weight: 500; text-decoration: none; padding: 8px 16px; border-radius: 8px; transition: color .2s; }
        .btn-ghost:hover { color: #fff; }
        .btn-pink { background: var(--pink); color: #fff; font-size: 14px; font-weight: 600; text-decoration: none; padding: 9px 20px; border-radius: 10px; transition: background .2s, box-shadow .2s; }
        .btn-pink:hover { background: #e01070; box-shadow: 0 0 20px rgba(247,37,133,.4); }

        /* Hero */
        .hero { position: relative; z-index: 10; padding: 80px 24px 64px; max-width: 1200px; margin: 0 auto; width: 100%; }
        .badge { display: inline-flex; align-items: center; gap: 8px; background: rgba(247,37,133,.1); border: 1px solid rgba(247,37,133,.25); color: #f472b6; font-size: 12px; font-weight: 600; padding: 6px 14px; border-radius: 50px; margin-bottom: 28px; }
        .badge-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--pink); animation: pulse 2s infinite; }
        @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(.95)} }
        h1 { font-size: clamp(2.6rem, 5vw, 4.5rem); font-weight: 800; color: #fff; line-height: 1.1; letter-spacing: -1.5px; margin-bottom: 20px; }
        h1 span { background: linear-gradient(135deg, var(--pink), #A855F7); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .subtitle { font-size: 17px; color: var(--muted); max-width: 520px; line-height: 1.75; margin-bottom: 40px; }

        /* Search bar */
        .search-wrap { max-width: 520px; margin-bottom: 20px; }
        .search-label { font-size: 11px; font-weight: 600; color: var(--pink); letter-spacing: .1em; text-transform: uppercase; margin-bottom: 10px; }
        .search-box { display: flex; background: var(--card); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; transition: border-color .2s, box-shadow .2s; }
        .search-box:focus-within { border-color: var(--pink); box-shadow: 0 0 0 3px rgba(247,37,133,.15); }
        .search-icon { display: flex; align-items: center; padding: 0 16px; color: var(--muted); flex-shrink: 0; }
        .search-input { flex: 1; background: transparent; border: none; outline: none; color: #fff; font-size: 14px; font-family: 'Inter', sans-serif; padding: 14px 0; }
        .search-input::placeholder { color: #4B5563; }
        .search-btn { background: var(--pink); color: #fff; font-size: 13px; font-weight: 700; border: none; cursor: pointer; padding: 14px 24px; font-family: 'Inter', sans-serif; transition: background .2s; flex-shrink: 0; }
        .search-btn:hover { background: var(--pink2); }
        .err-text { font-size: 12px; color: #FB7185; margin-top: 8px; padding-left: 4px; }
        .succ-text { font-size: 12px; color: #34D399; margin-top: 8px; padding-left: 4px; }
        .divider { display: flex; align-items: center; gap: 12px; max-width: 520px; margin: 16px 0; }
        .divider-line { flex: 1; height: 1px; background: var(--border); }
        .divider-text { font-size: 11px; color: #374151; font-weight: 500; text-transform: uppercase; letter-spacing: .08em; }
        .link-secondary { font-size: 13px; color: var(--muted); text-decoration: none; display: inline-flex; align-items: center; gap: 4px; transition: color .2s; }
        .link-secondary:hover { color: #fff; }

        /* Features */
        .section { position: relative; z-index: 10; padding: 72px 24px; }
        .section-inner { max-width: 1200px; margin: 0 auto; }
        .section-label { font-size: 11px; font-weight: 700; color: var(--pink); letter-spacing: .15em; text-transform: uppercase; margin-bottom: 12px; }
        .section-title { font-size: clamp(1.8rem, 3vw, 2.5rem); font-weight: 800; color: #fff; letter-spacing: -.5px; margin-bottom: 48px; }
        .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; }
        .card { background: var(--card); border: 1px solid var(--border); border-radius: 20px; padding: 28px; transition: border-color .25s, transform .25s; }
        .card:hover { border-color: rgba(247,37,133,.3); transform: translateY(-3px); }
        .card-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; }
        .card-icon.pink { background: rgba(247,37,133,.12); }
        .card-icon.purple { background: rgba(168,85,247,.12); }
        .card-icon.teal { background: rgba(20,184,166,.12); }
        .card-title { font-size: 16px; font-weight: 700; color: #fff; margin-bottom: 10px; }
        .card-desc { font-size: 13px; color: var(--muted); line-height: 1.7; }

        /* Steps */
        .steps { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0; }
        .step { padding: 32px; border-right: 1px solid var(--border); }
        .step:last-child { border-right: none; }
        .step-num { font-size: 48px; font-weight: 800; background: linear-gradient(135deg, var(--pink), #A855F7); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; line-height: 1; margin-bottom: 16px; }
        .step-title { font-size: 15px; font-weight: 700; color: #fff; margin-bottom: 8px; }
        .step-desc { font-size: 13px; color: var(--muted); line-height: 1.65; }

        /* CTA */
        .cta { background: linear-gradient(135deg, rgba(247,37,133,.08), rgba(168,85,247,.08)); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
        .cta-inner { max-width: 1200px; margin: 0 auto; padding: 80px 24px; text-align: center; }
        .cta h2 { font-size: clamp(1.8rem, 3vw, 2.8rem); font-weight: 800; color: #fff; margin-bottom: 14px; letter-spacing: -.5px; }
        .cta p { font-size: 15px; color: var(--muted); max-width: 400px; margin: 0 auto 32px; line-height: 1.7; }

        /* Footer */
        footer { margin-top: auto; border-top: 1px solid var(--border); padding: 24px; z-index: 10; }
        .footer-inner { max-width: 1200px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
        .footer-links { display: flex; gap: 20px; }
        .footer-link { font-size: 13px; color: var(--muted); text-decoration: none; transition: color .2s; }
        .footer-link:hover { color: #fff; }
    </style>
</head>
<body>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <!-- NAV -->
    <nav>
        <div class="nav-inner">
            <a href="/" class="logo">
                <div class="logo-icon">
                    <svg width="18" height="18" fill="none" stroke="#fff" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <span class="logo-text">LabDrive</span>
            </a>
            <div class="nav-links">
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="btn-pink">Dashboard →</a>
                @else
                    <a href="{{ route('login') }}" class="btn-pink">Masuk Portal</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero">
        <div class="badge">
            <span class="badge-dot"></span>
            Platform Distribusi Soal & Modul Laboratorium
        </div>
        <h1>Akses Dokumen Lab<br>Lebih <span>Aman & Cepat</span></h1>
        <p class="subtitle">Sistem terenkripsi untuk mendistribusikan soal ujian dan modul praktikum. Dilindungi kode unik, password, dan jadwal akses otomatis.</p>

        <div class="search-wrap" x-data="{ tab: 'akses' }">
            <div style="display: flex; gap: 16px; margin-bottom: 16px;">
                <button @click="tab = 'akses'" :style="tab === 'akses' ? 'color: var(--pink); border-bottom: 2px solid var(--pink);' : 'color: var(--muted); border-bottom: 2px solid transparent;'" style="background: none; border: none; font-size: 13px; font-weight: 700; padding-bottom: 8px; cursor: pointer; text-transform: uppercase; letter-spacing: 0.1em; transition: all 0.2s;">
                    Akses Dokumen
                </button>
                <button @click="tab = 'lacak'" :style="tab === 'lacak' ? 'color: #A855F7; border-bottom: 2px solid #A855F7;' : 'color: var(--muted); border-bottom: 2px solid transparent;'" style="background: none; border: none; font-size: 13px; font-weight: 700; padding-bottom: 8px; cursor: pointer; text-transform: uppercase; letter-spacing: 0.1em; transition: all 0.2s;">
                    Lacak Tugas
                </button>
            </div>

            <!-- Tab: Akses Dokumen -->
            <form action="{{ route('soal.accessByCode') }}" method="POST" x-show="tab === 'akses'" x-transition>
                @csrf
                <div class="search-box">
                    <div class="search-icon">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    </div>
                    <input type="text" name="kode_unik" class="search-input" placeholder="Contoh: AMSSOAL-AB123" required autocomplete="off">
                    <button type="submit" class="search-btn">AKSES →</button>
                </div>
                @if(session('error'))
                    <p class="err-text">✗ {{ session('error') }}</p>
                @endif
                @if(session('success'))
                    <p class="succ-text">✓ {{ session('success') }}</p>
                @endif
            </form>

            <!-- Tab: Lacak Tugas -->
            <div x-show="tab === 'lacak'" x-transition style="display: none;" x-data="lacakTugas()">
                <form @submit.prevent="submitLacak">
                    <div class="search-box" style="border-color: rgba(168,85,247,0.3);">
                        <div class="search-icon" style="color: #A855F7;">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" x-model="nim" class="search-input" placeholder="Masukkan NIM Anda" required autocomplete="off">
                        <button type="submit" class="search-btn" style="background: #A855F7;" :disabled="loading">
                            <span x-show="!loading">CARI →</span>
                            <span x-show="loading">Mencari...</span>
                        </button>
                    </div>
                </form>

                <!-- Hasil Lacak -->
                <div x-show="result" class="mt-4 p-4 rounded-xl" style="background: var(--card); border: 1px solid var(--border);" x-transition>
                    <p class="text-sm font-semibold text-white mb-3" x-text="resultTitle"></p>
                    <div class="flex flex-col gap-3">
                        <template x-for="item in tasks" :key="item.id">
                            <div class="p-3 rounded-lg" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05);">
                                <div class="flex justify-between items-start mb-1">
                                    <p class="text-sm font-medium text-pink-400" x-text="item.soal_title"></p>
                                    <span class="text-[10px] bg-green-500/20 text-green-400 px-2 py-0.5 rounded-full border border-green-500/30">Diterima</span>
                                </div>
                                <p class="text-xs text-gray-300 truncate mb-1" x-text="item.file_name"></p>
                                <p class="text-[11px] text-gray-500" x-text="'Dikumpulkan: ' + item.created_at"></p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <div class="divider">
            <div class="divider-line"></div>
            <span class="divider-text">LabDrive Portal Khusus</span>
            <div class="divider-line"></div>
        </div>
        <a href="{{ route('login') }}" class="link-secondary">
            Masuk sebagai Admin/Asisten
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </a>
    </section>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('lacakTugas', () => ({
                nim: '',
                loading: false,
                result: false,
                resultTitle: '',
                tasks: [],
                async submitLacak() {
                    if (!this.nim) return;
                    this.loading = true;
                    this.result = false;
                    
                    try {
                        const res = await fetch(`{{ route('soal.trackTask') }}?nim=${encodeURIComponent(this.nim)}`);
                        const data = await res.json();
                        
                        this.loading = false;
                        this.result = true;
                        
                        if (data.data.length > 0) {
                            this.resultTitle = `Ditemukan ${data.data.length} tugas untuk NIM ${this.nim} (${data.data[0].nama}):`;
                            this.tasks = data.data;
                        } else {
                            this.resultTitle = `Belum ada tugas yang dikumpulkan dengan NIM ${this.nim}.`;
                            this.tasks = [];
                        }
                    } catch (e) {
                        this.loading = false;
                        alert('Terjadi kesalahan koneksi.');
                    }
                }
            }));
        });
    </script>

    <!-- FEATURES -->
    <section class="section" style="border-top: 1px solid var(--border);">
        <div class="section-inner">
            <p class="section-label">Fitur Unggulan</p>
            <h2 class="section-title">Kenapa pilih LabDrive?</h2>
            <div class="cards">
                <div class="card">
                    <div class="card-icon pink">
                        <svg width="22" height="22" fill="none" stroke="#F72585" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <p class="card-title">Enkripsi Akses</p>
                    <p class="card-desc">Setiap dokumen dilindungi kode unik dan password khusus. File tidak bisa diakses langsung tanpa otentikasi.</p>
                </div>
                <div class="card">
                    <div class="card-icon purple">
                        <svg width="22" height="22" fill="none" stroke="#A855F7" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    </div>
                    <p class="card-title">Kontrol Hak Unduh</p>
                    <p class="card-desc">Admin bisa mengizinkan atau memblokir fitur unduh kapan saja. Perubahan langsung terasa tanpa refresh manual.</p>
                </div>
                <div class="card">
                    <div class="card-icon teal">
                        <svg width="22" height="22" fill="none" stroke="#14B8A6" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="card-title">Jadwal & Auto-Refresh</p>
                    <p class="card-desc">Atur waktu aktif dan kedaluwarsa. Jika waktu habis, halaman mahasiswa otomatis ter-refresh dan akses ditutup.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- HOW IT WORKS -->
    <section class="section" style="border-top:1px solid var(--border); padding-top:64px; padding-bottom:64px;">
        <div class="section-inner">
            <p class="section-label">Cara Kerja</p>
            <h2 class="section-title" style="margin-bottom:0;">Empat langkah mudah</h2>
            <div style="border:1px solid var(--border); border-radius:20px; overflow:hidden; margin-top:40px;">
                <div class="steps">
                    <div class="step">
                        <div class="step-num">01</div>
                        <p class="step-title">Admin Upload</p>
                        <p class="step-desc">Upload soal/modul, tentukan password & jadwal akses.</p>
                    </div>
                    <div class="step">
                        <div class="step-num">02</div>
                        <p class="step-title">Kode Dibagikan</p>
                        <p class="step-desc">Bagikan kode unik & password ke mahasiswa.</p>
                    </div>
                    <div class="step">
                        <div class="step-num">03</div>
                        <p class="step-title">Mahasiswa Akses</p>
                        <p class="step-desc">Input kode di halaman ini, masukkan password.</p>
                    </div>
                    <div class="step" style="border-right:none;">
                        <div class="step-num">04</div>
                        <p class="step-title">Dokumen Terbuka</p>
                        <p class="step-desc">Dokumen tampil sesuai izin yang ditetapkan admin.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta">
        <div class="cta-inner">
            <p class="section-label" style="margin-bottom:16px;">Sistem Tertutup</p>
            <h2>Portal Khusus<br>Internal Laboratorium.</h2>
            <p>Akses ke dashboard pengelola dokumen dibatasi hanya untuk Asisten dan Laboran yang terdaftar.</p>
            <a href="{{ route('login') }}" class="btn-pink" style="font-size:15px; padding:12px 32px; border-radius:12px;">Masuk Sekarang →</a>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <div class="footer-inner">
            <a href="/" class="logo">
                <div class="logo-icon" style="width:28px;height:28px;border-radius:8px;">
                    <svg width="14" height="14" fill="none" stroke="#fff" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <span style="font-weight:700;font-size:15px;color:#fff;">LabDrive</span>
            </a>
            <p style="font-size:12px;color:#374151;">© {{ date('Y') }} Platform Distribusi Dokumen Laboratorium</p>
            <div class="footer-links">
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="footer-link">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="footer-link">Masuk Portal</a>
                @endauth
            </div>
        </div>
    </footer>
</body>
</html>
