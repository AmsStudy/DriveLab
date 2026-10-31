<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $soal->title }} — IC-Labs Repository</title>
<link rel="icon" type="image/png" href="{{ asset('images/iclabs-logo.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>
*{margin:0;padding:0;box-sizing:border-box}

:root{
  --bg:#090D0B;
  --surface:#0F1713;
  --surface2:#142019;
  --surface3:#1A2820;
  --border:#1A2820;
  --border-emerald:rgba(16,185,129,0.35);
  --emerald:#10B981;
  --emerald-dim:rgba(16,185,129,0.12);
  --emerald-glow:rgba(16,185,129,0.25);
  --violet:#0D9488;
  --text1:#F1EEF6;
  --text2:#9B96B0;
  --text3:#5A5573;
  --green:#10B981;
  --green-dim:rgba(16,185,129,0.12);
  --blue:#06B6D4;
  --blue-dim:rgba(6,182,212,0.1);
  --amber:#F59E0B;
}

body{
  font-family:'DM Sans',sans-serif;
  background:var(--bg);
  color:var(--text1);
  min-height:100vh;
  overflow-x:hidden;
}

body::before{
  content:'';
  position:fixed;
  inset:0;
  background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
  pointer-events:none;
  z-index:0;
  opacity:0.5;
}

/* ─── HEADER ─── */
.topbar{
  position:sticky;top:0;z-index:100;
  background:rgba(9,13,11,0.85);
  backdrop-filter:blur(24px);
  border-bottom:1px solid var(--border);
  padding:0 24px;
  height:62px;
  display:flex;align-items:center;justify-content:space-between;
}

.topbar-left{display:flex;align-items:center;gap:14px}

.logo-icon{
  width:36px;height:36px;border-radius:10px;
  background:linear-gradient(135deg,var(--emerald),var(--violet));
  display:flex;align-items:center;justify-content:center;
  flex-shrink:0;
  box-shadow:0 0 20px rgba(16,185,129,0.3);
  position:relative;
}
.logo-icon::after{
  content:'';position:absolute;inset:-1px;border-radius:11px;
  background:linear-gradient(135deg,var(--emerald),var(--violet));
  z-index:-1;opacity:0.4;filter:blur(8px);
}

.topbar-title{
  font-family:'Syne',sans-serif;
  font-size:14px;font-weight:600;color:var(--text1);
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:320px;
}
.topbar-cat{
  font-size:11px;color:var(--emerald);font-weight:500;
  display:flex;align-items:center;gap:4px;margin-top:2px;
}

.badge-active{
  display:flex;align-items:center;gap:6px;
  font-size:11px;font-weight:600;color:var(--green);
  background:var(--green-dim);
  border:1px solid rgba(34,197,94,0.2);
  padding:5px 12px;border-radius:100px;
  letter-spacing:0.02em;
}
.badge-dot{
  width:6px;height:6px;border-radius:50%;
  background:var(--green);
  animation:pulse 2s infinite;
}
@keyframes pulse{
  0%,100%{opacity:1;transform:scale(1)}
  50%{opacity:0.5;transform:scale(0.8)}
}

/* ─── MAIN ─── */
main{max-width:960px;margin:0 auto;padding:28px 20px 60px;position:relative;z-index:1}

@keyframes fadeUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}

/* ─── INFO BAR ─── */
.infobar{
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:16px;
  padding:18px 22px;
  margin-bottom:24px;
  display:flex;align-items:center;justify-content:space-between;gap:16px;
  animation:fadeUp 0.4s ease both;
}
.infobar-desc{font-size:13.5px;color:var(--text2);line-height:1.5;flex:1}
.infobar-meta{
  display:flex;align-items:center;gap:16px;
  font-size:12px;color:var(--text3);flex-shrink:0;
}
.meta-item{display:flex;align-items:center;gap:5px}
.meta-item svg{width:14px;height:14px;opacity:0.7}

/* ─── SECTION HEADER ─── */
.section-header{
  display:flex;align-items:center;justify-content:space-between;
  margin-bottom:16px;
  animation:fadeUp 0.4s 0.1s ease both;
}
.section-title{
  font-family:'Syne',sans-serif;
  font-size:13px;font-weight:700;
  color:var(--text3);
  text-transform:uppercase;letter-spacing:0.08em;
  display:flex;align-items:center;gap:8px;
}
.section-title::before{
  content:'';width:3px;height:14px;border-radius:2px;
  background:linear-gradient(180deg,var(--emerald),var(--violet));
}

/* ─── FILE GRID ─── */
.file-grid{
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(280px,1fr));
  gap:14px;
  animation:fadeUp 0.4s 0.15s ease both;
}

.file-card{
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:18px;
  padding:20px;
  display:flex;flex-direction:column;gap:16px;
  transition:all 0.25s ease;
  position:relative;overflow:hidden;
}
.file-card::before{
  content:'';
  position:absolute;
  top:0;left:0;right:0;height:1px;
  background:linear-gradient(90deg,transparent,rgba(16,185,129,0.4),transparent);
  opacity:0;transition:opacity 0.3s;
}
.file-card:hover{
  border-color:var(--border-emerald);
  transform:translateY(-2px);
  box-shadow:0 8px 32px rgba(0,0,0,0.4),0 0 0 1px rgba(16,185,129,0.12);
}
.file-card:hover::before{opacity:1}

.file-card:nth-child(1){animation:fadeUp 0.4s 0.15s ease both}
.file-card:nth-child(2){animation:fadeUp 0.4s 0.20s ease both}
.file-card:nth-child(3){animation:fadeUp 0.4s 0.25s ease both}
.file-card:nth-child(4){animation:fadeUp 0.4s 0.30s ease both}
.file-card:nth-child(5){animation:fadeUp 0.4s 0.35s ease both}
.file-card:nth-child(6){animation:fadeUp 0.4s 0.40s ease both}

.file-card-top{display:flex;align-items:flex-start;gap:14px}

.ext-badge{
  width:46px;height:46px;border-radius:12px;
  display:flex;align-items:center;justify-content:center;
  flex-shrink:0;
  font-size:10px;font-weight:800;font-family:'Syne',sans-serif;
  letter-spacing:0.04em;text-transform:uppercase;
}
.ext-preview{background:var(--emerald-dim);color:var(--emerald);border:1px solid rgba(16,185,129,0.2)}
.ext-other{background:var(--surface3);color:var(--text3);border:1px solid var(--border)}

.file-info{flex:1;min-width:0}
.file-name{
  font-size:13.5px;font-weight:500;color:var(--text1);
  display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;
  overflow:hidden;line-height:1.45;
}
.file-size{font-size:11.5px;color:var(--text3);margin-top:5px}

.file-actions{display:flex;gap:8px}

.btn{
  display:flex;align-items:center;justify-content:center;gap:6px;
  padding:9px 16px;border-radius:10px;
  font-size:12.5px;font-weight:600;font-family:'DM Sans',sans-serif;
  border:none;cursor:pointer;transition:all 0.2s ease;
  text-decoration:none;
}
.btn-primary{
  flex:1;
  background:linear-gradient(135deg,var(--emerald),#0D9488);
  color:#fff;
  box-shadow:0 4px 16px rgba(16,185,129,0.2);
}
.btn-primary:hover{
  box-shadow:0 6px 22px rgba(16,185,129,0.35);
  transform:translateY(-1px);
  filter:brightness(1.05);
}
.btn-primary:active{transform:translateY(0);filter:brightness(0.95)}

.btn-dl-only{
  flex:1;
  background:var(--surface3);color:var(--text2);
  border:1px solid var(--border);
}
.btn-dl-only:hover{background:var(--surface2);color:var(--text1);border-color:rgba(255,255,255,0.12)}

.btn-disabled{
  flex:1;
  background:rgba(255,255,255,0.03);color:var(--text3);
  border:1px solid var(--border);
  cursor:not-allowed;font-size:12px;opacity:0.65;
}

.badge-preview-only{
  font-size:10px;font-weight:600;color:var(--amber);
  background:rgba(245,158,11,0.12);border:1px solid rgba(245,158,11,0.25);
  padding:2px 7px;border-radius:6px;
}

/* ─── CONTENT TEXT CARD ─── */
.content-text-card{
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:18px;
  padding:20px 24px;
  margin-bottom:24px;
  animation:fadeUp 0.4s 0.05s ease both;
  position:relative;
}
.content-text-header{
  display:flex;align-items:center;justify-content:space-between;gap:12px;
  margin-bottom:14px;padding-bottom:12px;
  border-bottom:1px solid var(--border);
}
.content-text-title{
  font-family:'Syne',sans-serif;font-size:13px;font-weight:700;
  color:var(--emerald);text-transform:uppercase;letter-spacing:0.06em;
  display:flex;align-items:center;gap:8px;
}
.content-text-title svg{width:16px;height:16px}
.btn-copy-text{
  background:var(--surface3);color:var(--text2);
  border:1px solid var(--border);border-radius:8px;
  padding:6px 12px;font-size:12px;font-weight:500;
  cursor:pointer;display:flex;align-items:center;gap:6px;
  transition:all 0.2s;
}
.btn-copy-text:hover{background:var(--surface2);color:var(--text1);border-color:rgba(255,255,255,0.15)}
.content-text-body{
  font-size:13.5px;line-height:1.7;color:var(--text1);
  white-space:pre-wrap;word-break:break-word;
  background:#05050A;padding:18px;border-radius:12px;
  border:1px solid rgba(255,255,255,0.05);
  font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,monospace;
  max-height:450px;overflow-y:auto;
}

/* ─── LINK GRID ─── */
.link-grid{
  display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));
  gap:14px;margin-bottom:24px;
  animation:fadeUp 0.4s 0.1s ease both;
}
.link-card{
  background:var(--surface);border:1px solid var(--border);
  border-radius:16px;padding:16px;
  display:flex;align-items:center;gap:14px;
  transition:all 0.25s ease;
}
.link-card:hover{
  border-color:rgba(114,9,183,0.4);
  transform:translateY(-2px);
  box-shadow:0 8px 30px rgba(0,0,0,0.4);
}
.link-icon-wrap{
  width:42px;height:42px;border-radius:12px;
  background:rgba(114,9,183,0.15);border:1px solid rgba(114,9,183,0.3);
  color:#C77DFF;display:flex;align-items:center;justify-content:center;
  flex-shrink:0;
}
.link-info{flex:1;min-width:0}
.link-title{font-size:13px;font-weight:600;color:var(--text1);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.link-url{font-size:11px;color:var(--text3);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

/* ─── SUBMISSION TYPE TABS ─── */
.sub-type-tabs{
  display:flex;gap:8px;margin-bottom:20px;
  background:var(--surface2);padding:6px;border-radius:12px;
  border:1px solid var(--border);
}
.tab-btn{
  flex:1;display:flex;align-items:center;justify-content:center;gap:8px;
  padding:10px 14px;background:transparent;border:none;border-radius:8px;
  color:var(--text2);font-family:'DM Sans',sans-serif;font-size:13px;font-weight:600;
  cursor:pointer;transition:all 0.2s ease;
}
.tab-btn:hover{color:var(--text1)}
.tab-btn.active{
  background:linear-gradient(135deg,rgba(16,185,129,0.25),rgba(13,148,136,0.25));
  color:#fff;border:1px solid rgba(16,185,129,0.4);
  box-shadow:0 2px 10px rgba(16,185,129,0.15);
}
.sub-pane{display:none}
.sub-pane.active{display:block}

.btn-icon{
  width:38px;height:38px;padding:0;flex-shrink:0;
  background:var(--surface3);color:var(--text2);
  border:1px solid var(--border);
}
.btn-icon:hover{background:var(--surface2);color:var(--text1);border-color:rgba(255,255,255,0.12)}
.btn-icon svg{width:15px;height:15px}

/* ─── EMPTY STATE ─── */
.empty-state{
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:18px;
  padding:56px 24px;
  text-align:center;
  animation:fadeUp 0.4s 0.15s ease both;
}
.empty-state svg{width:48px;height:48px;color:var(--text3);margin:0 auto 16px}
.empty-state p{font-size:14px;color:var(--text2)}
.empty-state .empty-desc{font-size:13px;color:var(--text3);margin-top:8px;max-width:420px;margin-left:auto;margin-right:auto}

/* ─── SUBMISSION ─── */
.submission-wrap{
  margin-top:36px;
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:20px;
  overflow:hidden;
  position:relative;
  animation:fadeUp 0.4s 0.25s ease both;
}
.sub-glow{
  position:absolute;
  bottom:-60px;left:50%;transform:translateX(-50%);
  width:500px;height:200px;
  background:radial-gradient(ellipse,rgba(16,185,129,0.08) 0%,transparent 70%);
  pointer-events:none;
}
.sub-header{
  padding:20px 24px;
  border-bottom:1px solid var(--border);
  display:flex;align-items:center;justify-content:space-between;gap:16px;
}
.sub-title{
  font-family:'Syne',sans-serif;
  font-size:14px;font-weight:700;color:var(--text1);
  display:flex;align-items:center;gap:10px;
}
.sub-title-icon{
  width:32px;height:32px;border-radius:8px;
  background:var(--emerald-dim);
  display:flex;align-items:center;justify-content:center;
}
.sub-title-icon svg{width:16px;height:16px;color:var(--emerald)}

.deadline-badge{
  display:flex;align-items:center;gap:5px;
  font-size:11.5px;color:var(--text2);
  background:var(--surface3);
  border:1px solid var(--border);
  padding:5px 12px;border-radius:8px;
  flex-shrink:0;
}
.deadline-badge.passed{
  background:rgba(239,68,68,0.08);
  border-color:rgba(239,68,68,0.2);
  color:#F87171;
}
.deadline-badge svg{width:13px;height:13px;opacity:0.6}
.deadline-badge strong{color:var(--text1);font-weight:600}

.sub-body{padding:24px;position:relative;z-index:1}

.instr-box{
  margin-bottom:20px;padding:14px 16px;
  border-radius:12px;
  background:var(--blue-dim);
  border:1px solid rgba(59,130,246,0.18);
  color:#93C5FD;
  font-size:13px;line-height:1.55;
  display:flex;gap:10px;
}
.instr-box svg{width:18px;height:18px;flex-shrink:0;margin-top:1px;opacity:0.8}
.instr-box strong{font-size:12px;text-transform:uppercase;letter-spacing:0.05em;display:block;margin-bottom:4px}

.deadline-passed-notice{
  padding:16px;border-radius:12px;text-align:center;font-size:13.5px;font-weight:600;
  background:rgba(239,68,68,0.08);
  border:1px solid rgba(239,68,68,0.2);
  color:#F87171;
}

.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px}
@media(max-width:580px){.form-grid{grid-template-columns:1fr}}

.field-group{display:flex;flex-direction:column;gap:7px}
.field-label{
  font-size:10.5px;font-weight:700;color:var(--text3);
  text-transform:uppercase;letter-spacing:0.08em;
  display:flex;align-items:center;gap:4px;
}
.field-label .req{color:var(--emerald)}
.field-label .note{color:var(--text3);font-weight:400;text-transform:none;letter-spacing:0;margin-left:auto;font-size:11px}
.field-wrap{position:relative}
.field-icon{
  position:absolute;left:13px;top:50%;transform:translateY(-50%);
  width:15px;height:15px;color:var(--text3);pointer-events:none;
  transition:color 0.2s;
}
.field-input{
  width:100%;padding:11px 14px 11px 38px;
  background:#080810;
  border:1px solid var(--border);
  border-radius:10px;
  color:var(--text1);font-size:13.5px;
  font-family:'DM Sans',sans-serif;
  outline:none;transition:all 0.2s;
}
.field-input::placeholder{color:var(--text3)}
.field-input:focus{
  border-color:var(--emerald);
  box-shadow:0 0 0 3px rgba(16,185,129,0.12);
  background:#070C09;
}

/* Dropzone */
.dropzone{
  border:1.5px dashed rgba(255,255,255,0.1);
  background:#070C09;
  border-radius:14px;
  padding:36px 24px;
  text-align:center;
  cursor:pointer;
  transition:all 0.25s ease;
  position:relative;
  margin-bottom:16px;
}
.dropzone:hover{
  border-color:rgba(16,185,129,0.35);
  background:rgba(16,185,129,0.03);
}
.dropzone.dragover{
  border-color:var(--emerald);
  background:var(--emerald-dim);
}
.dropzone.has-file{
  border-color:rgba(34,197,94,0.35);
  background:rgba(34,197,94,0.04);
}
.dz-icon-wrap{
  width:52px;height:52px;margin:0 auto 14px;
  background:var(--surface3);border:1px solid var(--border);
  border-radius:50%;display:flex;align-items:center;justify-content:center;
  transition:all 0.25s;
}
.dropzone:hover .dz-icon-wrap{border-color:rgba(16,185,129,0.3);background:var(--emerald-dim)}
.dz-icon-wrap svg{width:22px;height:22px;color:var(--text3);transition:color 0.25s}
.dropzone:hover .dz-icon-wrap svg{color:var(--emerald)}
.dz-text{font-size:13px;font-weight:600;color:var(--text2);margin-bottom:5px;transition:color 0.25s}
.dropzone:hover .dz-text{color:var(--emerald)}
.dz-sub{font-size:11.5px;color:var(--text3)}
.dz-success{display:none;flex-direction:column;align-items:center}
.dz-success-icon{
  width:52px;height:52px;margin:0 auto 12px;
  background:var(--green-dim);border:1px solid rgba(34,197,94,0.25);
  border-radius:50%;display:flex;align-items:center;justify-content:center;
  box-shadow:0 0 20px rgba(34,197,94,0.15);
}
.dz-success-icon svg{width:24px;height:24px;color:var(--green)}
.dz-filename{font-size:13px;font-weight:600;color:var(--green);word-break:break-all;padding:0 16px;text-align:center}
.dz-change{
  margin-top:10px;font-size:11.5px;color:var(--text3);
  padding:4px 12px;background:var(--surface3);border-radius:6px;
  cursor:pointer;transition:all 0.15s;display:inline-block;
}
.dz-change:hover{color:var(--text1);background:var(--surface2)}

/* Alert */
.alert{
  display:none;padding:13px 16px;border-radius:10px;
  font-size:13px;font-weight:600;text-align:center;
  margin-bottom:16px;
}
.alert-success{background:var(--green-dim);color:var(--green);border:1px solid rgba(34,197,94,0.2)}
.alert-error{background:rgba(239,68,68,0.1);color:#F87171;border:1px solid rgba(239,68,68,0.2)}

/* Submit btn */
.btn-submit{
  width:100%;padding:14px 24px;
  background:linear-gradient(135deg,var(--emerald),#0D9488);
  color:#fff;border:none;border-radius:12px;cursor:pointer;
  font-family:'Syne',sans-serif;font-size:14px;font-weight:700;
  display:flex;align-items:center;justify-content:center;gap:8px;
  transition:all 0.25s ease;
  box-shadow:0 4px 20px rgba(16,185,129,0.25);
  position:relative;overflow:hidden;
}
.btn-submit::after{
  content:'';position:absolute;inset:0;
  background:linear-gradient(135deg,rgba(255,255,255,0.08),transparent);
  pointer-events:none;
}
.btn-submit:hover{box-shadow:0 8px 30px rgba(16,185,129,0.4);transform:translateY(-2px)}
.btn-submit:active{transform:translateY(0);filter:brightness(0.95)}
.btn-submit:disabled{opacity:0.6;cursor:not-allowed;transform:none;box-shadow:none}
.btn-submit svg{width:16px;height:16px}

@keyframes spin{to{transform:rotate(360deg)}}
.spinner{
  width:16px;height:16px;border:2px solid rgba(255,255,255,0.3);
  border-top-color:#fff;border-radius:50%;
  animation:spin 0.7s linear infinite;
}

/* Autofill override */
input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus{
  -webkit-box-shadow:0 0 0 30px #080810 inset !important;
  -webkit-text-fill-color:var(--text1) !important;
}

/* ─── FOOTER ─── */
footer{
  text-align:center;padding:32px 16px;
  font-size:11.5px;color:var(--text3);
  position:relative;z-index:1;
}
</style>
</head>
<body>

{{-- ─── TOP BAR ─── --}}
<header class="topbar">
    <div class="topbar-left">
        <a href="/" class="logo-icon" style="padding:4px;text-decoration:none;" title="IC-Labs Repository">
            <img src="{{ asset('images/iclabs-logo.png') }}" alt="IC-Labs" style="width:100%;height:100%;object-fit:contain;" draggable="false">
        </a>
        <div>
            <div class="topbar-title">{{ $soal->title }}</div>
            <div class="topbar-cat">
                <svg width="10" height="10" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4"/></svg>
                @if($soal->category)
                    <span style="color:{{ $soal->category->color }}">{{ $soal->category->name }}</span>
                @endif
                @if($soal->access_type === 'scheduled' && $soal->active_until)
                    <span style="color:var(--amber)">· Berakhir {{ $soal->active_until->format('d M Y, H:i') }}</span>
                @endif
            </div>
        </div>
    </div>
    <div class="badge-active">
        <span class="badge-dot"></span>
        Akses Aktif
    </div>
</header>

{{-- ─── MAIN ─── --}}
<main>

    {{-- Info Bar --}}
    <div class="infobar">
        @if($soal->description)
            <p class="infobar-desc">{{ $soal->description }}</p>
        @endif
        <div class="infobar-meta">
            @if($soal->attachments->count() > 0)
                <div class="meta-item">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                    </svg>
                    {{ $soal->attachments->count() }} File Terlampir
                </div>
            @endif
            <div class="meta-item">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                {{ $soal->admin->name }}
            </div>
        </div>
    </div>

    {{-- Soal Text Content --}}
    @if($soal->content_text)
        <div class="content-text-card">
            <div class="content-text-header">
                <div class="content-text-title">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Instruksi / Soal Teks
                </div>
                <button type="button" class="btn-copy-text" onclick="navigator.clipboard.writeText(document.getElementById('soal-raw-text').innerText).then(()=>alert('Teks soal berhasil disalin!'))">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    Salin Teks
                </button>
            </div>
            <div class="content-text-body" id="soal-raw-text">{!! nl2br(e($soal->content_text)) !!}</div>
        </div>
    @endif

    @php
        $linkAttachments = $soal->attachments->where('type', 'link');
        $fileAttachments = $soal->attachments->where('type', 'file');
    @endphp

    {{-- Link Attachments --}}
    @if($linkAttachments->count() > 0)
        <div class="section-header">
            <div class="section-title">Tautan / Link Terkait</div>
        </div>
        <div class="link-grid">
            @foreach($linkAttachments as $linkAtt)
                <div class="link-card">
                    <div class="link-icon-wrap">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </div>
                    <div class="link-info">
                        <div class="link-title">{{ $linkAtt->file_original_name ?: 'Tautan Eksternal' }}</div>
                        <div class="link-url">{{ Str::limit($linkAtt->link_url, 45) }}</div>
                    </div>
                    <a href="{{ $linkAtt->link_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary" style="flex:none;padding:9px 18px">
                        Buka Link
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    {{-- File Section --}}
    @if($fileAttachments->count() > 0)

        <div class="section-header">
            <div class="section-title">File Lampiran (Maks. 15 File)</div>
        </div>

        <div class="file-grid">
            @foreach($fileAttachments as $attachment)
                @php
                    $ext           = strtolower($attachment->file_type ?? '');
                    $isPreviewable = in_array($ext, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp']);
                    $canDownload   = $attachment->canDownload();

                    $previewUrl = route('soal.preview_wrapper', [
                        'token'      => $soal->unique_token,
                        'attachment' => $attachment->id,
                    ]);
                    $downloadUrl = route('soal.download', [
                        'token'      => $soal->unique_token,
                        'attachment' => $attachment->id,
                    ]);

                    if (isset($isAdminPreview) && $isAdminPreview) {
                        $previewUrl  .= '?admin_preview=1';
                        $downloadUrl .= '?admin_preview=1';
                    }
                @endphp

                <div class="file-card">
                    <div class="file-card-top">
                        <div class="ext-badge {{ $isPreviewable ? 'ext-preview' : 'ext-other' }}">
                            {{ $ext ?: 'FILE' }}
                        </div>
                        <div class="file-info">
                            <p class="file-name" title="{{ $attachment->file_original_name }}">
                                {{ $attachment->file_original_name }}
                            </p>
                            <div style="display:flex;align-items:center;gap:8px;margin-top:5px;">
                                <span class="file-size">{{ $attachment->file_size_formatted }}</span>
                                @if(!$canDownload)
                                    <span class="badge-preview-only">Hanya Pratinjau</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="file-actions">
                        @if($isPreviewable)
                            <a href="{{ $previewUrl }}"
                               target="file_{{ $attachment->id }}"
                               class="btn btn-primary">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Buka Preview
                            </a>
                        @endif

                        @if($canDownload)
                            @if($isPreviewable)
                                {{-- Icon-only download button alongside preview --}}
                                <a href="{{ $downloadUrl }}" class="btn btn-icon" title="Unduh File">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                </a>
                            @else
                                {{-- Full-width download button for non-previewable --}}
                                <a href="{{ $downloadUrl }}" class="btn btn-dl-only">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Unduh File
                                </a>
                            @endif
                        @elseif(!$isPreviewable)
                            <div class="btn btn-disabled" title="Unduhan file dinonaktifkan oleh asisten">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                Hanya Pratinjau
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

    @elseif($linkAttachments->isEmpty() && empty($soal->content_text))

        {{-- Empty State --}}
        <div class="empty-state">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p>Dokumen ini belum memiliki file atau materi yang diunggah.</p>
            @if($soal->description)
                <p class="empty-desc">{{ $soal->description }}</p>
            @endif
        </div>

    @endif

    {{-- ─── SUBMISSION SECTION ─── --}}
    @if($soal->is_submission_enabled)
        @php
            $isDeadlinePassed = $isDeadlinePassed ?? ($soal->deadline_at && now()->greaterThan($soal->deadline_at));

            $accepts    = [];
            $extLabels  = [];
            $allowed    = $soal->allowed_extensions ?? [];

            if (empty($allowed) || in_array('all', $allowed)) {
                $extText    = 'Semua format file (Lolos Scan Keamanan)';
                $acceptAttr = '';
            } else {
                if (in_array('pdf', $allowed))     { $accepts[] = '.pdf';           $extLabels[] = 'PDF'; }
                if (in_array('word', $allowed))    { $accepts[] = '.doc,.docx';     $extLabels[] = 'Word'; }
                if (in_array('excel', $allowed))   { $accepts[] = '.xls,.xlsx';     $extLabels[] = 'Excel'; }
                if (in_array('ppt', $allowed))     { $accepts[] = '.ppt,.pptx';     $extLabels[] = 'PowerPoint'; }
                if (in_array('image', $allowed))   { $accepts[] = '.jpg,.jpeg,.png'; $extLabels[] = 'Gambar'; }
                if (in_array('archive', $allowed)) { $accepts[] = '.zip,.rar';       $extLabels[] = 'Arsip'; }
                if (in_array('pkt', $allowed))     { $accepts[] = '.pkt';            $extLabels[] = 'Packet Tracer'; }

                $extText    = 'Format: ' . implode(', ', $extLabels);
                $acceptAttr = implode(',', $accepts);
            }

            $allowedSubTypes = $soal->submission_types ?: ['file'];
            $defaultSubType  = in_array('file', $allowedSubTypes) ? 'file' : (in_array('link', $allowedSubTypes) ? 'link' : 'text');
        @endphp

        <div class="submission-wrap">
            <div class="sub-glow"></div>

            {{-- Header --}}
            <div class="sub-header">
                <div class="sub-title">
                    <div class="sub-title-icon">
                        <svg fill="none" stroke="#10B981" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                    </div>
                    Pengumpulan Tugas
                </div>

                @if($soal->deadline_at)
                    <div class="deadline-badge {{ $isDeadlinePassed ? 'passed' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $isDeadlinePassed ? 'Berakhir' : 'Batas Waktu' }}:
                        <strong>{{ $soal->deadline_at->format('d M Y, H:i') }}</strong>
                    </div>
                @endif
            </div>

            {{-- Body --}}
            <div class="sub-body">

                @if($soal->submission_instructions)
                    <div class="instr-box">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <strong>Catatan Asisten</strong>
                            {!! nl2br(e($soal->submission_instructions)) !!}
                        </div>
                    </div>
                @endif

                @if(isset($isAdminPreview) && $isAdminPreview)
                    <div class="deadline-passed-notice" style="border-color:rgba(59,130,246,0.25);color:#93C5FD;">
                        Mode preview admin — pengumpulan tugas tidak tersedia.
                    </div>
                @elseif($isDeadlinePassed)
                    <div class="deadline-passed-notice">
                        Maaf, batas waktu pengumpulan tugas telah berakhir.
                    </div>
                @else
                    <div class="instr-box" style="margin-bottom:16px;border-color:rgba(59,130,246,0.25);background:rgba(59,130,246,0.06);">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <div>
                            <strong>Pengumpulan ulang</strong>
                            Satu NIM hanya menyimpan satu data tugas. Upload lagi dengan NIM yang sama akan menggantikan pengumpulan sebelumnya.
                        </div>
                    </div>

                    <form id="submission-form" autocomplete="off">
                        @csrf

                        <div class="form-grid">
                            <div class="field-group">
                                <label class="field-label">
                                    NIM Mahasiswa <span class="req">*</span>
                                </label>
                                <div class="field-wrap">
                                    <input type="text" id="sub_nim" name="nim" required
                                           placeholder="Contoh: 13020230081"
                                           autocomplete="off"
                                           class="field-input">
                                    <svg class="field-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="field-group">
                                <label class="field-label">
                                    Nama Lengkap <span class="req">*</span>
                                </label>
                                <div class="field-wrap">
                                    <input type="text" id="sub_nama" name="nama" required
                                           placeholder="Contoh: Aan Maulana Sampe"
                                           autocomplete="off"
                                           class="field-input">
                                    <svg class="field-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="submission_type" id="sub_type" value="{{ $defaultSubType }}">

                        @if(count($allowedSubTypes) > 1)
                            <div class="sub-type-tabs">
                                @if(in_array('file', $allowedSubTypes))
                                    <button type="button" class="tab-btn {{ $defaultSubType === 'file' ? 'active' : '' }}" data-type="file" onclick="switchSubmissionType('file')">
                                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                        Upload File
                                    </button>
                                @endif
                                @if(in_array('link', $allowedSubTypes))
                                    <button type="button" class="tab-btn {{ $defaultSubType === 'link' ? 'active' : '' }}" data-type="link" onclick="switchSubmissionType('link')">
                                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                        Tautan / Link
                                    </button>
                                @endif
                                @if(in_array('text', $allowedSubTypes))
                                    <button type="button" class="tab-btn {{ $defaultSubType === 'text' ? 'active' : '' }}" data-type="text" onclick="switchSubmissionType('text')">
                                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Jawaban Teks
                                    </button>
                                @endif
                            </div>
                        @endif

                        {{-- Pane: File --}}
                        @if(in_array('file', $allowedSubTypes))
                            <div class="sub-pane {{ $defaultSubType === 'file' ? 'active' : '' }}" id="pane-file">
                                <label class="field-label" style="margin-bottom:7px">
                                    File Tugas <span class="req">*</span>
                                    <span class="note">Maks. 10 MB (Check Keamanan Otomatis)</span>
                                </label>

                                <div class="dropzone" id="dropzone"
                                     onclick="document.getElementById('sub_file').click()">
                                    <div id="dz-empty">
                                        <div class="dz-icon-wrap">
                                            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                            </svg>
                                        </div>
                                        <div class="dz-text">Klik atau seret file ke sini</div>
                                        <div class="dz-sub">{{ $extText }} · Maks. 10 MB</div>
                                    </div>
                                    <div class="dz-success" id="dz-success">
                                        <div class="dz-success-icon">
                                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div class="dz-filename" id="dz-filename"></div>
                                        <span class="dz-change"
                                              onclick="event.stopPropagation();document.getElementById('sub_file').click()">
                                            Ganti File
                                        </span>
                                    </div>
                                </div>

                                <input type="file"
                                       id="sub_file"
                                       name="file"
                                       style="display:none"
                                       {{ $acceptAttr ? 'accept=' . $acceptAttr : '' }}>
                            </div>
                        @endif

                        {{-- Pane: Link --}}
                        @if(in_array('link', $allowedSubTypes))
                            <div class="sub-pane {{ $defaultSubType === 'link' ? 'active' : '' }}" id="pane-link" style="margin-bottom:16px;">
                                <div class="field-group">
                                    <label class="field-label">
                                        URL / Tautan Tugas <span class="req">*</span>
                                        <span class="note">GitHub, Google Drive, Figma, dll.</span>
                                    </label>
                                    <div class="field-wrap">
                                        <input type="url" id="sub_link" name="link_url"
                                               placeholder="https://github.com/... atau https://drive.google.com/..."
                                               class="field-input">
                                        <svg class="field-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Pane: Text --}}
                        @if(in_array('text', $allowedSubTypes))
                            <div class="sub-pane {{ $defaultSubType === 'text' ? 'active' : '' }}" id="pane-text" style="margin-bottom:16px;">
                                <div class="field-group">
                                    <label class="field-label">
                                        Jawaban / Kode Teks <span class="req">*</span>
                                        <span class="note">Maks. 50.000 karakter</span>
                                    </label>
                                    <textarea id="sub_text" name="text_content" rows="6"
                                              placeholder="Tuliskan atau tempel jawaban tugas Anda di sini..."
                                              class="field-input" style="height:auto;padding-left:14px;resize:vertical;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:13px;line-height:1.6;"></textarea>
                                </div>
                            </div>
                        @endif

                        <div class="alert" id="sub-alert"></div>

                        <button type="submit" class="btn-submit" id="sub-btn">
                            <span id="btn-label">Kirim Tugas Sekarang</span>
                            <svg id="btn-arrow" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @endif

</main>

<footer>
    IC-Labs Repository &mdash; Akses tercatat pada {{ now()->format('d M Y, H:i') }} WITA
</footer>

<script>
// ─── Prevent save / print shortcuts ───
document.addEventListener('contextmenu', e => e.preventDefault());
document.addEventListener('keydown', e => {
    if ((e.ctrlKey || e.metaKey) && ['s','p','u'].includes(e.key.toLowerCase())) {
        e.preventDefault();
    }
});

// ─── Auto-reload when scheduled access expires ───
@if($soal->access_type === 'scheduled' && $soal->active_until)
    @php $remainingSeconds = max(0, now()->diffInSeconds($soal->active_until, false)); @endphp
    @if($remainingSeconds > 0)
        setTimeout(() => window.location.reload(), {{ $remainingSeconds }} * 1000);
    @endif
@endif

// ─── Polling: reload if admin changes access/download status ───
@if(!isset($isAdminPreview) || !$isAdminPreview)
setInterval(() => {
    fetch('{{ route('soal.status', $soal->unique_token) }}')
        .then(r => r.json())
        .then(data => {
            if (data.access_status !== 'active') {
                window.location.reload();
            }
            if (data.is_downloadable !== {{ $soal->is_downloadable ? 'true' : 'false' }}) {
                window.location.reload();
            }
        })
        .catch(() => {});
}, 15000);
@endif

// ─── Submission form logic ───
@if($soal->is_submission_enabled && (!isset($isDeadlinePassed) || !$isDeadlinePassed) && (!isset($isAdminPreview) || !$isAdminPreview))

const dropzone     = document.getElementById('dropzone');
const fileInput    = document.getElementById('sub_file');
const dzEmpty      = document.getElementById('dz-empty');
const dzSuccess    = document.getElementById('dz-success');
const dzFilename   = document.getElementById('dz-filename');
const subAlert     = document.getElementById('sub-alert');
const subBtn       = document.getElementById('sub-btn');
const btnLabel     = document.getElementById('btn-label');
const subTypeInput = document.getElementById('sub_type');

function switchSubmissionType(type) {
    if (subTypeInput) subTypeInput.value = type;
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.toggle('active', btn.getAttribute('data-type') === type);
    });
    document.querySelectorAll('.sub-pane').forEach(pane => {
        pane.classList.toggle('active', pane.id === 'pane-' + type);
    });
}

function showAlert(type, msg) {
    subAlert.className = 'alert alert-' + type;
    subAlert.textContent = msg;
    subAlert.style.display = 'block';
    setTimeout(() => { subAlert.style.display = 'none'; }, 6000);
}

function setFileSelected(file) {
    if (file.size > 10 * 1024 * 1024) {
        showAlert('error', 'Ukuran file melebihi batas maksimal 10 MB!');
        if (fileInput) fileInput.value = '';
        return;
    }
    if (dzEmpty) dzEmpty.style.display   = 'none';
    if (dzSuccess) dzSuccess.style.display = 'flex';
    if (dzFilename) dzFilename.textContent  = file.name;
    if (dropzone) {
        dropzone.classList.add('has-file');
        dropzone.classList.remove('dragover');
    }
}

function setLoading(loading) {
    subBtn.disabled = loading;
    if (loading) {
        btnLabel.textContent = 'Mengirim Tugas...';
        const arrow = document.getElementById('btn-arrow');
        if (arrow) arrow.outerHTML = '<div class="spinner" id="btn-arrow"></div>';
    } else {
        btnLabel.textContent = 'Kirim Tugas Sekarang';
        const spinner = document.getElementById('btn-arrow');
        if (spinner) spinner.outerHTML = '<svg id="btn-arrow" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>';
    }
}

if (fileInput) {
    fileInput.addEventListener('change', function() {
        if (this.files[0]) setFileSelected(this.files[0]);
    });
}

if (dropzone) {
    ['dragenter','dragover'].forEach(evt => {
        dropzone.addEventListener(evt, e => {
            e.preventDefault(); e.stopPropagation();
            dropzone.classList.add('dragover');
        });
    });
    ['dragleave','drop'].forEach(evt => {
        dropzone.addEventListener(evt, e => {
            e.preventDefault(); e.stopPropagation();
            dropzone.classList.remove('dragover');
        });
    });
    dropzone.addEventListener('drop', e => {
        const f = e.dataTransfer.files[0];
        if (f && fileInput) { fileInput.files = e.dataTransfer.files; setFileSelected(f); }
    });
}

document.getElementById('submission-form').addEventListener('submit', async function(e) {
    e.preventDefault();

    const currentType = subTypeInput ? subTypeInput.value : 'file';

    if (currentType === 'file') {
        if (!fileInput || !fileInput.files.length) {
            showAlert('error', 'Silakan pilih file tugas terlebih dahulu.');
            return;
        }
    } else if (currentType === 'link') {
        const linkEl = document.getElementById('sub_link');
        const linkVal = linkEl ? linkEl.value.trim() : '';
        if (!linkVal) {
            showAlert('error', 'Silakan masukkan URL / tautan tugas.');
            return;
        }
        if (!/^https?:\/\//i.test(linkVal)) {
            showAlert('error', 'URL harus diawali dengan http:// atau https://');
            return;
        }
    } else if (currentType === 'text') {
        const textEl = document.getElementById('sub_text');
        const textVal = textEl ? textEl.value.trim() : '';
        if (!textVal) {
            showAlert('error', 'Silakan ketikkan jawaban teks terlebih dahulu.');
            return;
        }
    }

    const formData = new FormData(this);

    setLoading(true);
    subAlert.style.display = 'none';

    try {
        const response = await fetch('{{ route('soal.submitTask', ['token' => $soal->unique_token]) }}', {
            method: 'POST',
            headers: { 
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData,
        });

        let result;
        const text = await response.text();
        try {
            result = JSON.parse(text);
        } catch {
            showAlert('error', 'Server error: ' + text.substring(0, 120));
            setLoading(false);
            return;
        }

        if (response.ok) {
            this.reset();
            switchSubmissionType(currentType);
            if (dzEmpty) dzEmpty.style.display   = 'block';
            if (dzSuccess) dzSuccess.style.display = 'none';
            if (dropzone) dropzone.classList.remove('has-file');
            showAlert('success', result.message || 'Tugas berhasil dikirim!');
            if (result.is_update) {
                btnLabel.textContent = 'Perbarui Tugas';
            }
        } else {
            let errorMsg = result.message || result.error || 'Terjadi kesalahan saat mengunggah.';
            if (result.errors) {
                const firstKey = Object.keys(result.errors)[0];
                errorMsg = result.errors[firstKey][0];
            }
            showAlert('error', errorMsg);
        }
    } catch (err) {
        showAlert('error', 'Kesalahan koneksi: ' + err.message);
    }

    setLoading(false);
});

@endif
</script>
</body>
</html>