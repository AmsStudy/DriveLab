{{-- resources/views/components/profile-modal.blade.php --}}
@php
    $modalUser = auth()->user();
    $totalDokumen = $modalUser ? $modalUser->soals()->count() : 0;
    $totalKuis    = $modalUser ? $modalUser->quizzes()->count() : 0;
    $totalKategori = $modalUser ? $modalUser->categories()->count() : 0;
@endphp

@if($modalUser)

{{-- ===== PROFILE MODAL STYLES ===== --}}
<style>
/* ---- overlay & wrapper ---- */
#prf-modal { display:none; }
#prf-modal.prf-open { display:flex; }

/* ---- card ---- */
.prf-card {
    background:#0F1713;
    border:1px solid #1F3028;
    border-radius:28px;
    overflow:hidden;
    width:100%;
    max-width:560px;
    max-height:92vh;
    display:flex;
    flex-direction:column;
    box-shadow:0 32px 80px rgba(0,0,0,.7), 0 0 0 1px rgba(16,185,129,.06);
    position:relative;
    z-index:1;
}

/* ---- hero banner ---- */
.prf-hero {
    background:linear-gradient(140deg,#071510 0%,#0D1F18 50%,#071510 100%);
    padding:28px 28px 20px;
    position:relative;
    overflow:hidden;
    border-bottom:1px solid #1F3028;
    flex-shrink:0;
}
.prf-hero::before {
    content:'';
    position:absolute;
    top:-60px; right:-60px;
    width:220px; height:220px;
    background:radial-gradient(circle,rgba(16,185,129,.18) 0%,transparent 70%);
    pointer-events:none;
}
.prf-hero::after {
    content:'';
    position:absolute;
    bottom:-40px; left:-30px;
    width:160px; height:160px;
    background:radial-gradient(circle,rgba(13,148,136,.12) 0%,transparent 70%);
    pointer-events:none;
}
.prf-hero-top {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    position:relative;
    z-index:1;
}
.prf-avatar-wrap {
    position:relative;
    flex-shrink:0;
}
.prf-avatar {
    width:56px; height:56px;
    border-radius:18px;
    background:linear-gradient(135deg,#059669,#0D9488);
    display:flex; align-items:center; justify-content:center;
    font-size:20px; font-weight:900;
    color:#fff;
    box-shadow:0 8px 24px rgba(5,150,105,.35);
    border:2px solid rgba(16,185,129,.3);
}
.prf-pulse {
    position:absolute; bottom:-2px; right:-2px;
    width:14px; height:14px;
    border-radius:50%;
    background:#10B981;
    border:2.5px solid #0F1713;
    box-shadow:0 0 0 0 rgba(16,185,129,.5);
    animation:prf-ping 2s ease infinite;
}
@keyframes prf-ping {
    0%,100%  { box-shadow:0 0 0 0 rgba(16,185,129,.5); }
    50%       { box-shadow:0 0 0 5px rgba(16,185,129,0); }
}
.prf-close-btn {
    width:36px; height:36px;
    border-radius:12px;
    background:rgba(255,255,255,.05);
    border:1px solid rgba(255,255,255,.08);
    display:flex; align-items:center; justify-content:center;
    color:#6B7280;
    cursor:pointer;
    transition:all .2s;
    flex-shrink:0;
}
.prf-close-btn:hover { background:rgba(244,63,94,.15); border-color:rgba(244,63,94,.35); color:#FB7185; }

.prf-name { font-size:17px; font-weight:800; color:#fff; line-height:1.2; }
.prf-email { font-size:11px; color:#6B7280; font-family:monospace; margin-top:2px; }
.prf-role-badge {
    display:inline-flex; align-items:center; gap:5px;
    padding:3px 10px; border-radius:50px;
    font-size:10px; font-weight:700; letter-spacing:.03em;
    background:rgba(16,185,129,.12);
    border:1px solid rgba(16,185,129,.25);
    color:#34D399;
    margin-top:6px;
}

/* stats strip */
.prf-stats {
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:8px;
    margin-top:16px;
    position:relative; z-index:1;
}
.prf-stat-item {
    background:rgba(255,255,255,.04);
    border:1px solid rgba(255,255,255,.07);
    border-radius:14px;
    padding:10px 8px;
    text-align:center;
}
.prf-stat-num  { font-size:16px; font-weight:900; line-height:1; }
.prf-stat-lbl  { font-size:9px; color:#6B7280; margin-top:3px; text-transform:uppercase; letter-spacing:.06em; }

/* ---- tabs ---- */
.prf-tabs {
    display:flex;
    gap:4px;
    padding:12px 20px 0;
    border-bottom:1px solid #1F3028;
    flex-shrink:0;
    background:#0F1713;
}
.prf-tab {
    padding:9px 16px;
    font-size:11px; font-weight:700;
    border-radius:10px 10px 0 0;
    color:#6B7280;
    cursor:pointer;
    transition:all .2s;
    display:flex; align-items:center; gap:6px;
    border:none; background:transparent;
    white-space:nowrap;
    border-bottom:2px solid transparent;
    margin-bottom:-1px;
}
.prf-tab:hover { color:#D1FAE5; background:rgba(16,185,129,.06); }
.prf-tab.prf-tab--active  { color:#34D399; border-bottom-color:#10B981; background:rgba(16,185,129,.08); }
.prf-tab.prf-tab--danger  { }
.prf-tab.prf-tab--danger:hover { color:#FB7185; background:rgba(244,63,94,.06); }
.prf-tab.prf-tab--danger.prf-tab--active { color:#FB7185; border-bottom-color:#F43F5E; background:rgba(244,63,94,.08); }

/* ---- body ---- */
.prf-body { padding:24px; overflow-y:auto; flex:1; }
.prf-panel { display:none; }
.prf-panel.prf-panel--active { display:block; }

/* ---- field group ---- */
.prf-field + .prf-field { margin-top:16px; }
.prf-label {
    display:block; font-size:10px; font-weight:800;
    text-transform:uppercase; letter-spacing:.08em;
    color:#6B7280; margin-bottom:7px;
}
.prf-input-wrap { position:relative; }
.prf-input-icon {
    position:absolute; left:13px; top:50%; transform:translateY(-50%);
    color:#374151; pointer-events:none;
    display:flex; align-items:center;
}
.prf-input {
    width:100%;
    background:#060C09;
    border:1px solid #1F3028;
    border-radius:14px;
    padding:11px 14px 11px 42px;
    font-size:13px; color:#E5E7EB;
    outline:none;
    transition:border-color .2s, box-shadow .2s;
    font-family:inherit;
}
.prf-input::placeholder { color:#374151; }
.prf-input:focus {
    border-color:#10B981;
    box-shadow:0 0 0 3px rgba(16,185,129,.12);
}
.prf-input--danger:focus {
    border-color:#F43F5E;
    box-shadow:0 0 0 3px rgba(244,63,94,.12);
}
.prf-eye-btn {
    position:absolute; right:13px; top:50%; transform:translateY(-50%);
    color:#374151; cursor:pointer; background:none; border:none; padding:2px;
    display:flex; align-items:center;
    transition:color .2s;
}
.prf-eye-btn:hover { color:#9CA3AF; }

/* info card */
.prf-info-row {
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:10px;
    margin-top:16px;
}
.prf-info-card {
    background:#060C09;
    border:1px solid #1F3028;
    border-radius:14px;
    padding:12px 14px;
    display:flex; align-items:center; gap:10px;
}
.prf-info-icon {
    width:32px; height:32px; border-radius:10px;
    display:flex; align-items:center; justify-content:center;
    flex-shrink:0;
}
.prf-info-sub  { font-size:9px; color:#4B5563; text-transform:uppercase; letter-spacing:.07em; font-weight:700; }
.prf-info-val  { font-size:12px; font-weight:700; color:#D1FAE5; margin-top:1px; }
.prf-info-val--success { color:#34D399; }

/* strength bar */
.prf-strength-wrap { margin-top:8px; }
.prf-strength-bar-bg {
    height:4px; border-radius:4px;
    background:rgba(255,255,255,.07);
    overflow:hidden;
}
.prf-strength-bar {
    height:100%; border-radius:4px;
    transition:width .3s, background-color .3s;
}
.prf-strength-label {
    font-size:10px; font-weight:700; margin-top:4px;
}

/* danger box */
.prf-danger-box {
    background:rgba(244,63,94,.08);
    border:1px solid rgba(244,63,94,.2);
    border-radius:16px;
    padding:16px;
    display:flex; gap:12px; align-items:flex-start;
    margin-bottom:20px;
}
.prf-danger-icon {
    width:36px; height:36px; flex-shrink:0;
    border-radius:12px;
    background:rgba(244,63,94,.15);
    display:flex; align-items:center; justify-content:center;
    color:#FB7185;
}
.prf-danger-title { font-size:12px; font-weight:800; color:#FB7185; }
.prf-danger-text  { font-size:11px; color:#9CA3AF; margin-top:3px; line-height:1.6; }

/* safeguard */
.prf-check-wrap {
    display:flex; align-items:flex-start; gap:10px;
    padding:14px; border-radius:14px;
    border:1px solid rgba(244,63,94,.18);
    background:rgba(244,63,94,.05);
    cursor:pointer;
    margin-top:16px;
}
.prf-check-wrap input { margin-top:1px; accent-color:#F43F5E; }
.prf-check-label { font-size:11px; color:#9CA3AF; line-height:1.6; }

/* ---- footer / actions ---- */
.prf-actions {
    display:flex; align-items:center; justify-content:flex-end; gap:10px;
    margin-top:24px; padding-top:20px;
    border-top:1px solid #1F3028;
}
.prf-btn-cancel {
    padding:9px 18px; border-radius:12px;
    font-size:12px; font-weight:600; color:#6B7280;
    background:transparent; border:none; cursor:pointer;
    transition:color .2s;
}
.prf-btn-cancel:hover { color:#D1D5DB; }
.prf-btn-primary {
    padding:10px 22px; border-radius:14px;
    font-size:12px; font-weight:800; color:#fff;
    background:linear-gradient(135deg,#059669,#0D9488);
    border:none; cursor:pointer;
    display:flex; align-items:center; gap:7px;
    box-shadow:0 6px 20px rgba(5,150,105,.35);
    transition:all .2s;
}
.prf-btn-primary:hover {
    transform:translateY(-1px);
    box-shadow:0 10px 28px rgba(5,150,105,.45);
}
.prf-btn-primary:active { transform:scale(.97); }
.prf-btn-delete {
    padding:10px 22px; border-radius:14px;
    font-size:12px; font-weight:800; color:#fff;
    background:linear-gradient(135deg,#DC2626,#9F1239);
    border:none; cursor:pointer;
    display:flex; align-items:center; gap:7px;
    box-shadow:0 6px 20px rgba(220,38,38,.3);
    transition:all .2s;
    opacity:.45; pointer-events:none;
}
.prf-btn-delete.prf-delete-ready {
    opacity:1; pointer-events:all;
}
.prf-btn-delete.prf-delete-ready:hover {
    transform:translateY(-1px);
    box-shadow:0 10px 28px rgba(220,38,38,.4);
}

/* ---- flash banner ---- */
.prf-flash {
    margin:0 24px; margin-top:14px;
    padding:12px 16px; border-radius:14px;
    display:flex; align-items:center; gap:10px;
    font-size:12px; font-weight:600;
    flex-shrink:0;
}
.prf-flash--success {
    background:rgba(16,185,129,.1);
    border:1px solid rgba(16,185,129,.25);
    color:#34D399;
}
.prf-flash--info {
    background:rgba(13,148,136,.1);
    border:1px solid rgba(13,148,136,.25);
    color:#2DD4BF;
}

/* ---- Esc hint ---- */
.prf-hint {
    display:flex; align-items:center; justify-content:space-between;
    padding:11px 24px;
    border-top:1px solid #1A2820;
    font-size:11px; color:#374151;
    flex-shrink:0;
    background:#0A1210;
}
.prf-hint kbd {
    display:inline-block;
    padding:2px 7px; border-radius:6px;
    background:#111F18; border:1px solid #2D4039;
    color:#6B7280; font-family:monospace; font-size:10px;
}

/* ---- Light Mode ---- */
html:not(.dark) .prf-card           { background:#FFFFFF; border-color:#E2EAE5; box-shadow:0 24px 64px rgba(0,0,0,.13); }
html:not(.dark) .prf-hero           { background:linear-gradient(140deg,#F0FDF4,#F8FAF9,#F0FDF4); border-color:#E2EAE5; }
html:not(.dark) .prf-hero::before   { background:radial-gradient(circle,rgba(16,185,129,.12) 0%,transparent 70%); }
html:not(.dark) .prf-hero::after    { background:radial-gradient(circle,rgba(13,148,136,.08) 0%,transparent 70%); }
html:not(.dark) .prf-avatar         { box-shadow:0 8px 24px rgba(5,150,105,.25); }
html:not(.dark) .prf-pulse          { border-color:#FFFFFF; }
html:not(.dark) .prf-close-btn      { background:rgba(0,0,0,.04); border-color:#E2EAE5; color:#94A3B8; }
html:not(.dark) .prf-close-btn:hover { background:#FEE2E2; border-color:#FCA5A5; color:#EF4444; }
html:not(.dark) .prf-name           { color:#0F1F17; }
html:not(.dark) .prf-email          { color:#94A3B8; }
html:not(.dark) .prf-role-badge     { background:rgba(16,185,129,.1); border-color:rgba(16,185,129,.25); color:#059669; }
html:not(.dark) .prf-stat-item      { background:#FFFFFF; border-color:#E2EAE5; }
html:not(.dark) .prf-stat-lbl       { color:#94A3B8; }
html:not(.dark) .prf-tabs           { background:#FFFFFF; border-color:#E2EAE5; }
html:not(.dark) .prf-tab            { color:#94A3B8; }
html:not(.dark) .prf-tab:hover      { color:#059669; background:rgba(16,185,129,.06); }
html:not(.dark) .prf-tab.prf-tab--active { color:#059669; border-bottom-color:#059669; background:rgba(16,185,129,.08); }
html:not(.dark) .prf-tab.prf-tab--danger:hover { color:#DC2626; background:rgba(220,38,38,.05); }
html:not(.dark) .prf-tab.prf-tab--danger.prf-tab--active { color:#DC2626; border-bottom-color:#DC2626; background:rgba(220,38,38,.06); }
html:not(.dark) .prf-body           { background:#FFFFFF; }
html:not(.dark) .prf-label          { color:#64748B; }
html:not(.dark) .prf-input          { background:#FFFFFF; border-color:#CBD5E1; color:#0F1F17; }
html:not(.dark) .prf-input::placeholder { color:#CBD5E1; }
html:not(.dark) .prf-input:focus    { border-color:#10B981; box-shadow:0 0 0 3px rgba(16,185,129,.1); }
html:not(.dark) .prf-input-icon     { color:#CBD5E1; }
html:not(.dark) .prf-eye-btn        { color:#CBD5E1; }
html:not(.dark) .prf-eye-btn:hover  { color:#94A3B8; }
html:not(.dark) .prf-info-card      { background:#F8FAF9; border-color:#E2EAE5; }
html:not(.dark) .prf-info-sub       { color:#94A3B8; }
html:not(.dark) .prf-info-val       { color:#0F1F17; }
html:not(.dark) .prf-info-val--success { color:#059669; }
html:not(.dark) .prf-actions        { border-color:#E2EAE5; }
html:not(.dark) .prf-btn-cancel:hover { color:#374151; }
html:not(.dark) .prf-flash--success { background:rgba(16,185,129,.08); border-color:rgba(16,185,129,.2); color:#059669; }
html:not(.dark) .prf-flash--info    { background:rgba(13,148,136,.08); border-color:rgba(13,148,136,.2); color:#0D9488; }
html:not(.dark) .prf-hint           { background:#F8FAF9; border-color:#E2EAE5; color:#94A3B8; }
html:not(.dark) .prf-hint kbd       { background:#FFFFFF; border-color:#E2EAE5; color:#94A3B8; }
html:not(.dark) .prf-strength-bar-bg { background:rgba(0,0,0,.07); }
html:not(.dark) .prf-danger-box     { background:rgba(220,38,38,.06); border-color:rgba(220,38,38,.18); }
html:not(.dark) .prf-danger-text    { color:#64748B; }
html:not(.dark) .prf-check-wrap     { border-color:rgba(220,38,38,.2); background:rgba(220,38,38,.04); }
html:not(.dark) .prf-check-label    { color:#64748B; }
</style>

{{-- ===== MODAL DOM ===== --}}
<div id="prf-modal"
     class="fixed inset-0 prf-open:flex items-center justify-center p-4"
     style="display:none; z-index:110;"
     x-data="{
        tab: 'profile',
        showCurrent: false,
        showNew: false,
        showConfirm: false,
        showDel: false,
        newPwd: '',
        copied: false,
        delChecked: false,

        get strength() {
            const p = this.newPwd;
            if (!p) return { pct: 0, label: 'Belum diisi', color: '#374151' };
            let s = 0;
            if (p.length >= 8) s++;
            if (/[A-Z]/.test(p) && /[a-z]/.test(p)) s++;
            if (/\d/.test(p)) s++;
            if (/[^A-Za-z0-9]/.test(p)) s++;
            if (s <= 1) return { pct: 25,  label: 'Lemah',       color: '#EF4444' };
            if (s === 2) return { pct: 50,  label: 'Cukup',       color: '#F59E0B' };
            if (s === 3) return { pct: 75,  label: 'Kuat',        color: '#14B8A6' };
            return          { pct: 100, label: 'Sangat Kuat', color: '#10B981' };
        },

        copyEmail() {
            navigator.clipboard.writeText('{{ addslashes($modalUser->email) }}');
            this.copied = true;
            setTimeout(() => this.copied = false, 2000);
        },

        open(t = 'profile') {
            this.tab = t;
            const el = document.getElementById('prf-modal');
            el.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        },
        close() {
            document.getElementById('prf-modal').style.display = 'none';
            document.body.style.overflow = '';
        },
        init() {
            window.openProfileModal  = (t) => this.open(t);
            window.closeProfileModal = ()  => this.close();

            @if($errors->updatePassword->isNotEmpty() || session('status') === 'password-updated')
                this.$nextTick(() => this.open('security'));
            @elseif($errors->userDeletion->isNotEmpty())
                this.$nextTick(() => this.open('danger'));
            @elseif($errors->any() && ($errors->has('name') || $errors->has('email')) || session('status') === 'profile-updated')
                this.$nextTick(() => this.open('profile'));
            @endif
        }
     }"
     @keydown.escape.window="close()">

    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/75 backdrop-blur-sm" @click="close()"></div>

    {{-- ===== CARD ===== --}}
    <div class="prf-card">

        {{-- ── HERO HEADER ── --}}
        <div class="prf-hero">
            <div class="prf-hero-top">
                {{-- Avatar --}}
                <div class="prf-avatar-wrap">
                    <div class="prf-avatar">{{ strtoupper(substr($modalUser->name, 0, 2)) }}</div>
                    <span class="prf-pulse"></span>
                </div>

                {{-- Identity info --}}
                <div style="flex:1; min-width:0;">
                    <div class="prf-name truncate">{{ $modalUser->name }}</div>
                    <div class="prf-email truncate">{{ $modalUser->email }}</div>
                    <div class="prf-role-badge">
                        <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        {{ $modalUser->isSuperAdmin() ? 'Super Administrator' : 'Admin Asisten / Dosen' }}
                    </div>
                </div>

                {{-- Copy email + Close --}}
                <div style="display:flex; flex-direction:column; align-items:flex-end; gap:6px; flex-shrink:0;">
                    <button type="button" class="prf-close-btn" @click="close()" title="Tutup (Esc)">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                    <button type="button" @click="copyEmail()"
                            style="font-size:10px; color:#34D399; display:flex; align-items:center; gap:4px; background:none; border:none; cursor:pointer; padding:0;"
                            x-text="copied ? '✓ Tersalin!' : '⎘ Salin Email'"></button>
                </div>
            </div>

            {{-- Stats strip --}}
            <div class="prf-stats" style="margin-top:16px;">
                <div class="prf-stat-item">
                    <div class="prf-stat-num" style="color:#34D399;">{{ $totalDokumen }}</div>
                    <div class="prf-stat-lbl">Dokumen</div>
                </div>
                <div class="prf-stat-item">
                    <div class="prf-stat-num" style="color:#2DD4BF;">{{ $totalKuis }}</div>
                    <div class="prf-stat-lbl">Kuis CBT</div>
                </div>
                <div class="prf-stat-item">
                    <div class="prf-stat-num" style="color:#38BDF8;">{{ $totalKategori }}</div>
                    <div class="prf-stat-lbl">Kategori</div>
                </div>
            </div>
        </div>

        {{-- ── FLASH ALERTS ── --}}
        @if(session('status') === 'profile-updated' || session('success'))
            <div class="prf-flash prf-flash--success">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') ?: 'Biodata Anda berhasil diperbarui.' }}
            </div>
        @elseif(session('status') === 'password-updated')
            <div class="prf-flash prf-flash--info">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Kata sandi Anda berhasil diperbarui.
            </div>
        @endif

        {{-- ── TABS ── --}}
        <div class="prf-tabs">
            <button type="button" class="prf-tab" :class="tab==='profile' ? 'prf-tab--active' : ''" @click="tab='profile'">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Biodata Diri
            </button>
            <button type="button" class="prf-tab" :class="tab==='security' ? 'prf-tab--active' : ''" @click="tab='security'">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Kata Sandi
            </button>
            <button type="button" class="prf-tab prf-tab--danger" :class="tab==='danger' ? 'prf-tab--active' : ''" @click="tab='danger'">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Zona Bahaya
            </button>
        </div>

        {{-- ── BODY ── --}}
        <div class="prf-body">

            {{-- Panel 1: Biodata --}}
            <div class="prf-panel" :class="tab==='profile' ? 'prf-panel--active' : ''">
                <form method="post" action="{{ route('profile.update') }}">
                    @csrf
                    @method('patch')

                    <div class="prf-field">
                        <label class="prf-label" for="pm_name">Nama Lengkap <span style="color:#F43F5E;">*</span></label>
                        <div class="prf-input-wrap">
                            <span class="prf-input-icon"><svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></span>
                            <input id="pm_name" name="name" type="text" class="prf-input"
                                   value="{{ old('name', $modalUser->name) }}"
                                   placeholder="Nama lengkap Anda"
                                   required autofocus autocomplete="name">
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="prf-field">
                        <label class="prf-label" for="pm_email">Alamat Email <span style="color:#F43F5E;">*</span></label>
                        <div class="prf-input-wrap">
                            <span class="prf-input-icon"><svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></span>
                            <input id="pm_email" name="email" type="email" class="prf-input"
                                   value="{{ old('email', $modalUser->email) }}"
                                   placeholder="admin@domain.ac.id"
                                   required autocomplete="username">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    {{-- Info row --}}
                    <div class="prf-info-row">
                        <div class="prf-info-card">
                            <div class="prf-info-icon" style="background:rgba(16,185,129,.12); color:#34D399;">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div>
                                <div class="prf-info-sub">Unit</div>
                                <div class="prf-info-val">IC-Labs Repository</div>
                            </div>
                        </div>
                        <div class="prf-info-card">
                            <div class="prf-info-icon" style="background:rgba(16,185,129,.12); color:#34D399;">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <div class="prf-info-sub">Status</div>
                                <div class="prf-info-val prf-info-val--success">Aktif ✓</div>
                            </div>
                        </div>
                    </div>

                    <div class="prf-actions">
                        <button type="button" class="prf-btn-cancel" @click="close()">Batal</button>
                        <button type="submit" class="prf-btn-primary">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Simpan Biodata
                        </button>
                    </div>
                </form>
            </div>

            {{-- Panel 2: Kata Sandi --}}
            <div class="prf-panel" :class="tab==='security' ? 'prf-panel--active' : ''">
                <form method="post" action="{{ route('password.update') }}">
                    @csrf
                    @method('put')

                    {{-- Current password --}}
                    <div class="prf-field">
                        <label class="prf-label" for="pm_cur">Kata Sandi Saat Ini <span style="color:#F43F5E;">*</span></label>
                        <div class="prf-input-wrap">
                            <span class="prf-input-icon"><svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg></span>
                            <input id="pm_cur" name="current_password"
                                   :type="showCurrent ? 'text' : 'password'"
                                   class="prf-input" style="padding-right:42px;"
                                   placeholder="Kata sandi saat ini" autocomplete="current-password">
                            <button type="button" class="prf-eye-btn" @click="showCurrent=!showCurrent" tabindex="-1">
                                <svg x-show="!showCurrent" width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showCurrent" x-cloak width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#34D399;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                    </div>

                    {{-- New password with strength --}}
                    <div class="prf-field">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:7px;">
                            <label class="prf-label" for="pm_new" style="margin-bottom:0;">Kata Sandi Baru <span style="color:#F43F5E;">*</span></label>
                            <span class="prf-strength-label" :style="'color:'+strength.color" x-text="newPwd ? strength.label : ''"></span>
                        </div>
                        <div class="prf-input-wrap">
                            <span class="prf-input-icon"><svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg></span>
                            <input id="pm_new" name="password"
                                   :type="showNew ? 'text' : 'password'"
                                   x-model="newPwd"
                                   class="prf-input" style="padding-right:42px;"
                                   placeholder="Min. 8 karakter" autocomplete="new-password">
                            <button type="button" class="prf-eye-btn" @click="showNew=!showNew" tabindex="-1">
                                <svg x-show="!showNew" width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showNew" x-cloak width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#34D399;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                            </button>
                        </div>
                        <div class="prf-strength-wrap">
                            <div class="prf-strength-bar-bg">
                                <div class="prf-strength-bar" :style="'width:'+strength.pct+'%;background-color:'+strength.color"></div>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                    </div>

                    {{-- Confirm new password --}}
                    <div class="prf-field">
                        <label class="prf-label" for="pm_conf">Konfirmasi Kata Sandi Baru <span style="color:#F43F5E;">*</span></label>
                        <div class="prf-input-wrap">
                            <span class="prf-input-icon"><svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>
                            <input id="pm_conf" name="password_confirmation"
                                   :type="showConfirm ? 'text' : 'password'"
                                   class="prf-input" style="padding-right:42px;"
                                   placeholder="Ulangi kata sandi baru" autocomplete="new-password">
                            <button type="button" class="prf-eye-btn" @click="showConfirm=!showConfirm" tabindex="-1">
                                <svg x-show="!showConfirm" width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showConfirm" x-cloak width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#34D399;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="prf-actions">
                        <button type="button" class="prf-btn-cancel" @click="close()">Batal</button>
                        <button type="submit" class="prf-btn-primary">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Perbarui Kata Sandi
                        </button>
                    </div>
                </form>
            </div>

            {{-- Panel 3: Zona Bahaya --}}
            <div class="prf-panel" :class="tab==='danger' ? 'prf-panel--active' : ''">
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="prf-danger-box">
                        <div class="prf-danger-icon">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div>
                            <div class="prf-danger-title">Peringatan: Penghapusan Akun Permanen</div>
                            <div class="prf-danger-text">Seluruh data, dokumen, kuis, dan rekam jejak aktivitas akan dihapus dari server. Tindakan ini <strong style="color:#FB7185;">tidak dapat dibatalkan.</strong></div>
                        </div>
                    </div>

                    <div class="prf-field">
                        <label class="prf-label" for="pm_del">Konfirmasi Kata Sandi <span style="color:#F43F5E;">*</span></label>
                        <div class="prf-input-wrap">
                            <span class="prf-input-icon" style="color:#F43F5E;"><svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg></span>
                            <input id="pm_del" name="password"
                                   :type="showDel ? 'text' : 'password'"
                                   class="prf-input prf-input--danger" style="padding-right:42px; border-color:rgba(244,63,94,.3);"
                                   placeholder="Ketik kata sandi untuk verifikasi" required>
                            <button type="button" class="prf-eye-btn" @click="showDel=!showDel" tabindex="-1">
                                <svg x-show="!showDel" width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showDel" x-cloak width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#F43F5E;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                    </div>

                    <label class="prf-check-wrap" @click="delChecked=!delChecked" style="cursor:pointer; user-select:none;">
                        <input type="checkbox" x-model="delChecked" @click.stop style="width:16px;height:16px;accent-color:#F43F5E;flex-shrink:0;margin-top:2px;">
                        <span class="prf-check-label">Saya memahami semua risiko dan mengonfirmasi penghapusan akun ini secara permanen.</span>
                    </label>

                    <div class="prf-actions">
                        <button type="button" class="prf-btn-cancel" @click="close()">Batal</button>
                        <button type="submit"
                                class="prf-btn-delete" :class="delChecked ? 'prf-delete-ready' : ''"
                                :disabled="!delChecked"
                                onclick="return confirm('Konfirmasi terakhir: Anda yakin ingin menghapus akun ini secara permanen?')">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Hapus Akun Permanen
                        </button>
                    </div>
                </form>
            </div>

        </div>{{-- /prf-body --}}

        {{-- ── BOTTOM HINT ── --}}
        <div class="prf-hint">
            <span><kbd>Esc</kbd> untuk menutup</span>
            <span>IC-Labs · Profile Settings</span>
        </div>

    </div>{{-- /prf-card --}}
</div>{{-- /prf-modal --}}

@endif
