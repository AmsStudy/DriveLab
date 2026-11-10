<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $soal->title }} — IC-Labs Repository</title>
<x-theme-head-script />
<link rel="icon" type="image/png" href="{{ asset('images/iclabs-logo.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700;800;900&family=Chakra+Petch:wght@500;600;700&family=Syne:wght@500;600;700;800&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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

html:not(.dark) {
  --bg: #F4F7F5;
  --surface: #FFFFFF;
  --surface2: #F8FAF9;
  --surface3: #F0F4F2;
  --border: #E2EAE5;
  --border-emerald: rgba(16,185,129,0.4);
  --emerald: #059669;
  --emerald-dim: rgba(16,185,129,0.12);
  --emerald-glow: rgba(16,185,129,0.15);
  --violet: #0D9488;
  --text1: #0F1F17;
  --text2: #374151;
  --text3: #64748B;
  --green: #059669;
  --green-dim: rgba(16,185,129,0.12);
  --blue: #0284C7;
  --blue-dim: rgba(2,132,199,0.1);
  --amber: #D97706;
}

html:not(.dark) body::before {
  opacity: 0.02;
}

html:not(.dark) .topbar {
  background: rgba(255, 255, 255, 0.95) !important;
  border-bottom: 1px solid #E2EAE5 !important;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

html:not(.dark) .brand-name {
  color: #0F1F17 !important;
}

html:not(.dark) .topbar-divider {
  background: #E2EAE5 !important;
}

html:not(.dark) .topbar-doc-title {
  color: #0F1F17 !important;
}

html:not(.dark) .field-input,
html:not(.dark) .custom-cyber-trigger {
  background: #FFFFFF !important;
  border-color: #E2EAE5 !important;
  color: #0F1F17 !important;
}

html:not(.dark) .field-wrap select.field-input {
  background-color: #FFFFFF !important;
  border-color: #E2EAE5 !important;
  color: #0F1F17 !important;
}
html:not(.dark) .field-wrap select.field-input option {
  background-color: #FFFFFF !important;
  color: #0F1F17 !important;
}

html:not(.dark) .content-text-body {
  background: #F8FAF9 !important;
  border-color: #E2EAE5 !important;
  color: #0F1F17 !important;
}

html:not(.dark) .sub-glow {
  opacity: 0.3;
}

html:not(.dark) .dropzone {
  background: #FFFFFF !important;
  border-color: #D1DCD6 !important;
}
html:not(.dark) .dropzone:hover {
  background: rgba(16, 185, 129, 0.04) !important;
  border-color: rgba(16, 185, 129, 0.45) !important;
}
html:not(.dark) .dz-change {
  background: #E2EAE5 !important;
  color: #374151 !important;
}
html:not(.dark) .btn-dz-add {
  background: #ECFDF5 !important;
  color: #047857 !important;
  border: 1px solid #A7F3D0 !important;
  box-shadow: 0 1px 3px rgba(16, 185, 129, 0.12) !important;
}
html:not(.dark) .btn-dz-add:hover {
  background: #059669 !important;
  color: #FFFFFF !important;
  border-color: #047857 !important;
  box-shadow: 0 4px 10px rgba(5, 150, 105, 0.25) !important;
}
html:not(.dark) .btn-dz-add:hover svg {
  color: #FFFFFF !important;
  stroke: #FFFFFF !important;
}
html:not(.dark) .btn-dz-reset {
  background: #F9FAFB !important;
  color: #4B5563 !important;
  border: 1px solid #E5E7EB !important;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
}
html:not(.dark) .btn-dz-reset:hover {
  background: #FEF2F2 !important;
  color: #DC2626 !important;
  border-color: #FCA5A5 !important;
  box-shadow: 0 2px 6px rgba(220, 38, 38, 0.1) !important;
}
html:not(.dark) .btn-dz-reset:hover svg {
  color: #DC2626 !important;
  stroke: #DC2626 !important;
}
html:not(.dark) .custom-cyber-menu {
  background: rgba(255, 255, 255, 0.98) !important;
  border-color: #E2EAE5 !important;
  box-shadow: 0 20px 35px -10px rgba(0, 0, 0, 0.12), 0 0 15px rgba(16, 185, 129, 0.08) !important;
}
html:not(.dark) .custom-cyber-menu-header {
  color: #64748B !important;
  border-bottom-color: #E2EAE5 !important;
}
html:not(.dark) .custom-cyber-menu-item {
  color: #374151 !important;
}
html:not(.dark) .custom-cyber-menu-item:hover {
  background: rgba(16, 185, 129, 0.08) !important;
  color: #059669 !important;
  border-color: rgba(16, 185, 129, 0.2) !important;
}
html:not(.dark) .custom-cyber-menu-item.is-selected {
  background: rgba(16, 185, 129, 0.12) !important;
  color: #059669 !important;
  border-color: rgba(16, 185, 129, 0.3) !important;
}

html:not(.dark) .instr-box {
  background: #EFF6FF !important;
  border-color: #BFDBFE !important;
  color: #1E3A8A !important;
}
html:not(.dark) .instr-box strong {
  color: #1D4ED8 !important;
}
html:not(.dark) .instr-box svg {
  color: #2563EB !important;
  opacity: 1 !important;
}
html:not(.dark) .deadline-passed-notice.preview-notice {
  background: #EFF6FF !important;
  border-color: #BFDBFE !important;
  color: #1E40AF !important;
}
html:not(.dark) .custom-cyber-trigger .trigger-text {
  color: #000000 !important;
}
html:not(.dark) .custom-cyber-trigger .trigger-chevron {
  color: #059669 !important;
}

html:not(.dark) .cyber-swal-popup {
  background: rgba(255, 255, 255, 0.98) !important;
  border-color: #E2EAE5 !important;
  box-shadow: 0 25px 60px -10px rgba(0, 0, 0, 0.15), 0 0 35px rgba(16, 185, 129, 0.08) !important;
}
html:not(.dark) .cyber-swal-title {
  color: #0F1F17 !important;
}
html:not(.dark) .cyber-swal-text {
  color: #374151 !important;
}
html:not(.dark) input:-webkit-autofill,
html:not(.dark) input:-webkit-autofill:hover,
html:not(.dark) input:-webkit-autofill:focus {
  -webkit-box-shadow: 0 0 0 30px #FFFFFF inset !important;
  -webkit-text-fill-color: #0F1F17 !important;
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
  background:rgba(9,13,11,0.92);
  backdrop-filter:blur(24px);
  -webkit-backdrop-filter:blur(24px);
  border-bottom:1px solid #1A2820;
  padding:0 24px;
  height:66px;
  display:flex;align-items:center;justify-content:space-between;
}

.topbar-left{
  display:flex;
  align-items:center;
  gap:16px;
  min-width:0;
}

.topbar-brand-link{
  display:flex;
  align-items:center;
  gap:12px;
  text-decoration:none;
  flex-shrink:0;
  transition:all 0.25s ease;
}
.topbar-brand-link:hover{
  opacity:0.95;
  transform:translateY(-1px);
}

.brand-logo-box{
  width:40px;
  height:40px;
  border-radius:12px;
  background:rgba(16,185,129,0.08);
  border:1px solid rgba(52,211,153,0.28);
  box-shadow:0 0 16px rgba(16,185,129,0.15);
  display:flex;
  align-items:center;
  justify-content:center;
  padding:5px;
  flex-shrink:0;
  transition:all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.topbar-brand-link:hover .brand-logo-box{
  border-color:rgba(52,211,153,0.55);
  background:rgba(16,185,129,0.14);
  box-shadow:0 0 24px rgba(16,185,129,0.3);
  transform:scale(1.02);
}

.brand-logo-img{
  width:100%;
  height:100%;
  object-fit:contain;
}

.brand-text-block{
  display:flex;
  flex-direction:column;
  line-height:1;
}

.brand-name{
  font-family:'Orbitron',sans-serif;
  font-size:15px;
  font-weight:800;
  color:#FFFFFF;
  letter-spacing:1.2px;
}

.brand-tag{
  font-family:'Chakra Petch',sans-serif;
  font-size:9.5px;
  font-weight:700;
  color:#34D399;
  letter-spacing:2px;
  margin-top:3px;
  text-transform:uppercase;
}

.topbar-divider{
  width:1px;
  height:28px;
  background:#1A2820;
  flex-shrink:0;
}

.topbar-doc-info{
  display:flex;
  flex-direction:column;
  justify-content:center;
  min-width:0;
}

.topbar-title-row{
  display:flex;
  align-items:center;
  gap:8px;
  min-width:0;
}

.topbar-doc-title{
  font-family:'Syne',sans-serif;
  font-size:15px;
  font-weight:700;
  color:#F3F4F6;
  white-space:nowrap;
  overflow:hidden;
  text-overflow:ellipsis;
  max-width:320px;
  letter-spacing:-0.01em;
  margin:0;
}

.topbar-meta-row{
  display:flex;
  align-items:center;
  gap:8px;
  margin-top:4px;
  flex-wrap:wrap;
}

.category-pill{
  display:inline-flex;
  align-items:center;
  gap:5px;
  padding:2px 8px;
  border-radius:6px;
  font-size:10.5px;
  font-weight:600;
  letter-spacing:0.02em;
  line-height:1.3;
}

.category-dot{
  width:6px;
  height:6px;
  border-radius:50%;
  flex-shrink:0;
}

.schedule-pill{
  display:inline-flex;
  align-items:center;
  gap:4px;
  padding:2px 8px;
  border-radius:6px;
  font-size:10.5px;
  font-weight:500;
  color:#FBBF24;
  background:rgba(245,158,11,0.1);
  border:1px solid rgba(245,158,11,0.25);
  line-height:1.3;
}

@media (max-width: 768px){
  .topbar-doc-title{
    max-width:200px;
    font-size:13.5px;
  }
  .brand-tag{
    display:none;
  }
}

@media (max-width: 540px){
  .topbar{
    padding:0 16px;
  }
  .topbar-left{
    gap:10px;
  }
  .brand-text-block{
    display:none;
  }
  .topbar-divider{
    display:none;
  }
  .topbar-doc-title{
    max-width:150px;
  }
}

.badge-active{
  display:flex;align-items:center;gap:6px;
  font-size:11px;font-weight:600;color:var(--green);
  background:var(--green-dim);
  border:1px solid rgba(34,197,94,0.2);
  padding:5px 12px;border-radius:100px;
  letter-spacing:0.02em;
  flex-shrink:0;
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

/* ─── Assistant Note (Catatan Asisten) ─── */
.assistant-note-card {
  margin-bottom: 14px;
  padding: 12px 16px;
  border-radius: 12px;
  background: rgba(6, 182, 212, 0.08);
  border: 1px solid rgba(6, 182, 212, 0.25);
  border-left: 3.5px solid #06B6D4;
  display: flex;
  flex-direction: column;
  gap: 4px;
}
html:not(.dark) .assistant-note-card {
  background: #F0F9FF !important;
  border-color: #BAE6FD !important;
  border-left-color: #0284C7 !important;
}

.note-badge {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-family: 'Syne', sans-serif;
  font-size: 10.5px;
  font-weight: 700;
  letter-spacing: 0.06em;
  color: #38BDF8;
  text-transform: uppercase;
}
html:not(.dark) .note-badge {
  color: #0284C7 !important;
}

.note-text {
  font-size: 13px;
  line-height: 1.5;
  color: var(--text1);
}
html:not(.dark) .note-text {
  color: #0F172A !important;
}

/* ─── Compact Helper Tip (Kebijakan Ringkas) ─── */
.sub-policy-tip {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 9px 14px;
  border-radius: 10px;
  background: rgba(255, 255, 255, 0.03);
  border: 1px dashed rgba(255, 255, 255, 0.12);
  color: var(--text3);
  font-size: 12px;
  margin-bottom: 16px;
  line-height: 1.4;
}
html:not(.dark) .sub-policy-tip {
  background: #F8FAF9 !important;
  border-color: #E2EAE5 !important;
  color: #64748B !important;
}
.sub-policy-tip svg {
  color: var(--emerald);
  flex-shrink: 0;
}

/* ─── Prior Submission Receipt Card ─── */
.prior-submission-card {
  margin-bottom: 18px;
  border-radius: 14px;
  background: rgba(16, 185, 129, 0.08);
  border: 1px solid rgba(16, 185, 129, 0.35);
  overflow: hidden;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
  animation: fadeInDown 0.25s ease both;
}
html:not(.dark) .prior-submission-card {
  background: #F0FDF4 !important;
  border-color: #86EFAC !important;
  box-shadow: 0 2px 10px rgba(16, 185, 129, 0.08) !important;
}

@keyframes fadeInDown {
  from { opacity: 0; transform: translateY(-6px); }
  to { opacity: 1; transform: translateY(0); }
}

.prior-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 14px;
  background: rgba(16, 185, 129, 0.12);
  border-bottom: 1px solid rgba(16, 185, 129, 0.2);
}
html:not(.dark) .prior-card-header {
  background: #DCFCE7 !important;
  border-bottom-color: #BBF7D0 !important;
}

.prior-status-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-family: 'Syne', sans-serif;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.04em;
  color: #34D399;
}
html:not(.dark) .prior-status-pill {
  color: #15803D !important;
}

.prior-pulse-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #10B981;
  box-shadow: 0 0 8px #10B981;
  animation: pulse 1.8s infinite;
}
html:not(.dark) .prior-pulse-dot {
  background: #16A34A;
  box-shadow: 0 0 6px #16A34A;
}

.prior-mode-tag {
  font-size: 10.5px;
  font-weight: 700;
  letter-spacing: 0.05em;
  padding: 3px 8px;
  border-radius: 6px;
  background: rgba(245, 158, 11, 0.15);
  color: #F59E0B;
  border: 1px solid rgba(245, 158, 11, 0.3);
}
html:not(.dark) .prior-mode-tag {
  background: #FEF3C7 !important;
  color: #B45309 !important;
  border-color: #FDE68A !important;
}

.prior-card-body {
  padding: 12px 14px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.prior-meta-row {
  display: flex;
  align-items: center;
  gap: 20px;
  flex-wrap: wrap;
}

.prior-meta-item,
.prior-file-row {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12.5px;
}

.p-label {
  color: var(--text3);
  font-size: 11.5px;
}
html:not(.dark) .p-label {
  color: #64748B !important;
}

.p-val {
  color: var(--text1);
}
html:not(.dark) .p-val {
  color: #0F172A !important;
}

.p-val.file-tag {
  font-family: monospace;
  font-weight: 600;
  background: rgba(0, 0, 0, 0.25);
  padding: 2px 8px;
  border-radius: 6px;
  border: 1px solid rgba(255, 255, 255, 0.08);
  font-size: 12px;
  color: #34D399;
}
html:not(.dark) .p-val.file-tag {
  background: #FFFFFF !important;
  border-color: #CBD5E1 !important;
  color: #047857 !important;
}

.prior-warning-bar {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 12px;
  background: rgba(239, 68, 68, 0.10);
  border-top: 1px solid rgba(239, 68, 68, 0.2);
  color: #F87171;
  font-size: 11.5px;
}
html:not(.dark) .prior-warning-bar {
  background: #FEF2F2 !important;
  border-top-color: #FECACA !important;
  color: #DC2626 !important;
}
.prior-warning-bar svg {
  flex-shrink: 0;
}

.deadline-passed-notice{
  padding:16px;border-radius:12px;text-align:center;font-size:13.5px;font-weight:600;
  background:rgba(239,68,68,0.08);
  border:1px solid rgba(239,68,68,0.2);
  color:#F87171;
}

.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px}
.form-grid.has-classes{grid-template-columns:repeat(auto-fit,minmax(210px,1fr))}
@media(max-width:580px){.form-grid,.form-grid.has-classes{grid-template-columns:1fr}}

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
.field-wrap select.field-input{
  appearance:none !important;
  -webkit-appearance:none !important;
  -moz-appearance:none !important;
  background-image:none !important;
  padding-left:38px !important;
  padding-right:40px !important;
  cursor:pointer !important;
  background-color:#090D0B !important;
  border:1px solid #1A2820 !important;
  border-radius:12px !important;
  color:var(--text1) !important;
  font-family:'DM Sans',sans-serif !important;
  transition:all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
}
.field-wrap select.field-input:hover{
  background-color:#0D1512 !important;
  border-color:rgba(16,185,129,0.5) !important;
  box-shadow:0 0 14px rgba(16,185,129,0.18) !important;
}
.field-wrap select.field-input:focus{
  background-color:#070C09 !important;
  border-color:var(--emerald) !important;
  outline:none !important;
  box-shadow:0 0 0 2px rgba(16,185,129,0.25), 0 0 18px rgba(16,185,129,0.2) !important;
}
.field-wrap select.field-input option{
  background-color:#0F1713 !important;
  color:#F3F4F6 !important;
  padding:12px 14px !important;
  font-family:'DM Sans',sans-serif !important;
}
.field-wrap select.field-input option:disabled{
  color:#6B7280 !important;
}
.field-wrap .select-chevron{
  position:absolute;
  right:14px;
  top:50%;
  transform:translateY(-50%);
  width:16px;
  height:16px;
  color:#10B981;
  pointer-events:none;
  transition:transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), color 0.2s ease;
}
.field-wrap:focus-within .select-chevron{
  color:#34D399;
  transform:translateY(-50%) rotate(180deg);
}

/* ─── Custom Cyber Dropdown (Mahasiswa & Form Choices) ─── */
.custom-cyber-dropdown {
  position: relative;
  width: 100%;
}
.custom-cyber-trigger {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 14px 12px 38px;
  background-color: #090D0B;
  border: 1px solid #1A2820;
  border-radius: 12px;
  color: var(--text1);
  font-family: 'DM Sans', sans-serif;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  text-align: left;
}
.custom-cyber-trigger:hover,
.custom-cyber-trigger.is-open {
  background-color: #0D1512;
  border-color: rgba(16, 185, 129, 0.5);
  box-shadow: 0 0 14px rgba(16, 185, 129, 0.18);
}
.custom-cyber-trigger.is-open {
  border-color: var(--emerald);
  box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.25), 0 0 18px rgba(16, 185, 129, 0.2);
}
.custom-cyber-trigger .trigger-text {
  flex: 1;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.custom-cyber-trigger .trigger-chevron {
  width: 16px;
  height: 16px;
  color: #10B981;
  transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  flex-shrink: 0;
  margin-left: 8px;
}
.custom-cyber-trigger.is-open .trigger-chevron {
  transform: rotate(180deg);
  color: #34D399;
}
.custom-cyber-menu {
  position: absolute;
  top: calc(100% + 8px);
  left: 0;
  right: 0;
  background: rgba(15, 23, 19, 0.98);
  border: 1px solid rgba(16, 185, 129, 0.35);
  box-shadow: 0 20px 45px -10px rgba(0, 0, 0, 0.9), 0 0 25px rgba(16, 185, 129, 0.18);
  border-radius: 16px;
  padding: 8px;
  z-index: 100;
  max-height: 250px;
  overflow-y: auto;
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
}
.custom-cyber-menu::-webkit-scrollbar {
  width: 4px;
}
.custom-cyber-menu::-webkit-scrollbar-thumb {
  background: rgba(16, 185, 129, 0.35);
  border-radius: 4px;
}
.custom-cyber-menu::-webkit-scrollbar-thumb:hover {
  background: #10B981;
}
.custom-cyber-menu-header {
  padding: 6px 10px 6px 10px;
  font-size: 10px;
  font-weight: 700;
  color: #6B7280;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  border-bottom: 1px solid rgba(26, 40, 32, 0.8);
  margin-bottom: 6px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.custom-cyber-menu-item {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 12px;
  margin-bottom: 3px;
  border-radius: 10px;
  font-size: 13.5px;
  font-weight: 500;
  color: #E5E7EB;
  background: transparent;
  border: 1px solid transparent;
  cursor: pointer;
  text-align: left;
  transition: all 0.15s ease;
}
.custom-cyber-menu-item:last-child {
  margin-bottom: 0;
}
.custom-cyber-menu-item:hover {
  background: rgba(16, 185, 129, 0.14);
  color: #34D399;
  border-color: rgba(16, 185, 129, 0.25);
  transform: translateX(2px);
}
.custom-cyber-menu-item.is-selected {
  background: rgba(16, 185, 129, 0.22);
  color: #10B981;
  font-weight: 600;
  border-color: rgba(16, 185, 129, 0.4);
}
.custom-cyber-menu-item .item-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #4B5563;
  margin-right: 8px;
  transition: all 0.15s ease;
}
.custom-cyber-menu-item:hover .item-dot {
  background: #34D399;
  box-shadow: 0 0 6px #10B981;
}
.custom-cyber-menu-item.is-selected .item-dot {
  background: #10B981;
  box-shadow: 0 0 8px #10B981;
}
.custom-cyber-menu-item .check-svg {
  width: 16px;
  height: 16px;
  color: #10B981;
  margin-left: 8px;
  flex-shrink: 0;
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

/* Dropzone Action Buttons */
.btn-dz-add {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-size: 11.5px;
  font-weight: 700;
  padding: 5px 12px;
  border-radius: 8px;
  cursor: pointer;
  background: rgba(16, 185, 129, 0.12);
  color: #34D399;
  border: 1px solid rgba(16, 185, 129, 0.35);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
.btn-dz-add svg {
  width: 13px;
  height: 13px;
  stroke-width: 2.5;
  transition: transform 0.2s ease;
}
.btn-dz-add:hover {
  background: rgba(16, 185, 129, 0.22);
  color: #6EE7B7;
  border-color: rgba(52, 211, 153, 0.6);
  box-shadow: 0 0 14px rgba(16, 185, 129, 0.25);
  transform: translateY(-1px);
}
.btn-dz-add:hover svg {
  transform: scale(1.15);
}
.btn-dz-add:active {
  transform: translateY(0px) scale(0.97);
}

.btn-dz-reset {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-size: 11.5px;
  font-weight: 600;
  padding: 5px 11px;
  border-radius: 8px;
  cursor: pointer;
  background: rgba(255, 255, 255, 0.04);
  color: #9CA3AF;
  border: 1px solid rgba(255, 255, 255, 0.12);
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
.btn-dz-reset svg {
  width: 12px;
  height: 12px;
  stroke-width: 2.2;
}
.btn-dz-reset:hover {
  background: rgba(239, 68, 68, 0.12);
  color: #F87171;
  border-color: rgba(239, 68, 68, 0.35);
  transform: translateY(-1px);
}
.btn-dz-reset:active {
  transform: translateY(0px) scale(0.97);
}

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

/* ─── SweetAlert2 Cyber Theme ─── */
.cyber-swal-popup {
  background: rgba(15, 23, 19, 0.96) !important;
  backdrop-filter: blur(20px) !important;
  -webkit-backdrop-filter: blur(20px) !important;
  border-radius: 24px !important;
  padding: 26px 28px !important;
  box-shadow: 0 25px 60px -10px rgba(0, 0, 0, 0.85), 0 0 35px rgba(16, 185, 129, 0.15) !important;
}
.cyber-swal-popup.cyber-swal-success {
  border: 1px solid rgba(16, 185, 129, 0.45) !important;
  box-shadow: 0 25px 60px -10px rgba(0, 0, 0, 0.85), 0 0 35px rgba(16, 185, 129, 0.22) !important;
}
.cyber-swal-popup.cyber-swal-error {
  border: 1px solid rgba(239, 68, 68, 0.45) !important;
  box-shadow: 0 25px 60px -10px rgba(0, 0, 0, 0.85), 0 0 35px rgba(239, 68, 68, 0.22) !important;
}
.cyber-swal-title {
  font-family: 'Syne', sans-serif !important;
  font-weight: 700 !important;
  font-size: 1.25rem !important;
  color: #FFFFFF !important;
  margin-bottom: 6px !important;
}
.cyber-swal-text {
  font-family: 'DM Sans', sans-serif !important;
  font-size: 0.9rem !important;
  line-height: 1.55 !important;
  color: #D1D5DB !important;
}
.cyber-swal-btn {
  border-radius: 12px !important;
  font-family: 'Syne', sans-serif !important;
  font-weight: 700 !important;
  font-size: 0.875rem !important;
  padding: 10px 24px !important;
  border: none !important;
  cursor: pointer !important;
  transition: all 0.2s ease !important;
}
.cyber-swal-btn-success {
  background: linear-gradient(135deg, #10B981, #0D9488) !important;
  color: #FFFFFF !important;
  box-shadow: 0 4px 18px rgba(16, 185, 129, 0.35) !important;
}
.cyber-swal-btn-error {
  background: linear-gradient(135deg, #EF4444, #DC2626) !important;
  color: #FFFFFF !important;
  box-shadow: 0 4px 18px rgba(239, 68, 68, 0.35) !important;
}
.swal2-icon.swal2-success {
  border-color: #10B981 !important; color: #10B981 !important;
  box-shadow: 0 0 25px rgba(16, 185, 129, 0.3) !important;
}
.swal2-icon.swal2-success [class^='swal2-success-line'] { background-color: #10B981 !important; }
.swal2-icon.swal2-success .swal2-success-ring { border-color: rgba(16, 185, 129, 0.3) !important; }
.swal2-icon.swal2-success .swal2-success-fix,
.swal2-icon.swal2-success .swal2-success-circular-line-left,
.swal2-icon.swal2-success .swal2-success-circular-line-right { background-color: transparent !important; }
.swal2-icon.swal2-error {
  border-color: #EF4444 !important; color: #EF4444 !important;
  box-shadow: 0 0 25px rgba(239, 68, 68, 0.3) !important;
}
.swal2-icon.swal2-error [class^='swal2-x-mark-line'] { background-color: #EF4444 !important; }

/* ─── ULTRA "WAH" AMBIENT CYBER BACKGROUND ─── */
.soal-ambient-canvas {
  position: fixed;
  inset: 0;
  z-index: 0;
  pointer-events: none;
  overflow: hidden;
  will-change: transform;
}

/* Fluid Morphing Aurora Waves */
.soal-aurora-blob {
  position: absolute;
  border-radius: 50%;
  pointer-events: none;
  filter: blur(85px);
  -webkit-filter: blur(85px);
  will-change: transform, border-radius;
  opacity: 0.85;
}

.soal-aurora-blob.blob-1 {
  width: 680px;
  height: 680px;
  top: -120px;
  left: -140px;
  background: radial-gradient(circle, rgba(16, 185, 129, 0.22) 0%, rgba(6, 182, 212, 0.12) 50%, transparent 75%);
  animation: auroraMorph1 24s ease-in-out infinite alternate;
}
html:not(.dark) .soal-aurora-blob.blob-1 {
  background: radial-gradient(circle, rgba(16, 185, 129, 0.20) 0%, rgba(6, 182, 212, 0.10) 50%, transparent 75%);
}

.soal-aurora-blob.blob-2 {
  width: 640px;
  height: 640px;
  top: -80px;
  right: -120px;
  background: radial-gradient(circle, rgba(6, 182, 212, 0.20) 0%, rgba(13, 148, 136, 0.12) 50%, transparent 75%);
  animation: auroraMorph2 28s ease-in-out infinite alternate;
}
html:not(.dark) .soal-aurora-blob.blob-2 {
  background: radial-gradient(circle, rgba(6, 182, 212, 0.18) 0%, rgba(2, 132, 199, 0.08) 50%, transparent 75%);
}

.soal-aurora-blob.blob-3 {
  width: 580px;
  height: 580px;
  top: 40%;
  left: -120px;
  background: radial-gradient(circle, rgba(52, 211, 153, 0.16) 0%, rgba(99, 102, 241, 0.08) 55%, transparent 75%);
  animation: auroraMorph3 22s ease-in-out infinite alternate;
}
html:not(.dark) .soal-aurora-blob.blob-3 {
  background: radial-gradient(circle, rgba(16, 185, 129, 0.15) 0%, rgba(99, 102, 241, 0.06) 55%, transparent 75%);
}

.soal-aurora-blob.blob-4 {
  width: 720px;
  height: 720px;
  bottom: -160px;
  right: -140px;
  background: radial-gradient(circle, rgba(16, 185, 129, 0.18) 0%, rgba(6, 182, 212, 0.12) 50%, transparent 75%);
  animation: auroraMorph4 32s ease-in-out infinite alternate;
}
html:not(.dark) .soal-aurora-blob.blob-4 {
  background: radial-gradient(circle, rgba(16, 185, 129, 0.15) 0%, rgba(6, 182, 212, 0.09) 50%, transparent 75%);
}

@keyframes auroraMorph1 {
  0% {
    border-radius: 42% 58% 70% 30% / 45% 45% 55% 55%;
    transform: translate3d(0, 0, 0) rotate(0deg) scale(1);
  }
  50% {
    border-radius: 58% 42% 40% 60% / 60% 50% 50% 40%;
    transform: translate3d(80px, 60px, 0) rotate(160deg) scale(1.12);
  }
  100% {
    border-radius: 48% 52% 58% 42% / 40% 60% 40% 60%;
    transform: translate3d(-30px, 90px, 0) rotate(320deg) scale(0.96);
  }
}

@keyframes auroraMorph2 {
  0% {
    border-radius: 50% 50% 40% 60% / 55% 45% 55% 45%;
    transform: translate3d(0, 0, 0) rotate(0deg) scale(1);
  }
  50% {
    border-radius: 40% 60% 55% 45% / 45% 55% 45% 55%;
    transform: translate3d(-90px, 70px, 0) rotate(-140deg) scale(1.08);
  }
  100% {
    border-radius: 60% 40% 45% 55% / 55% 40% 60% 45%;
    transform: translate3d(50px, 100px, 0) rotate(-280deg) scale(0.94);
  }
}

@keyframes auroraMorph3 {
  0% {
    border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
    transform: translate3d(0, 0, 0) scale(0.95);
  }
  50% {
    border-radius: 45% 55% 65% 35% / 40% 65% 35% 60%;
    transform: translate3d(70px, -50px, 0) scale(1.14);
  }
  100% {
    border-radius: 55% 45% 40% 60% / 50% 45% 55% 50%;
    transform: translate3d(-30px, 40px, 0) scale(1);
  }
}

@keyframes auroraMorph4 {
  0% {
    border-radius: 45% 55% 60% 40% / 50% 50% 50% 50%;
    transform: translate3d(0, 0, 0) scale(1);
  }
  50% {
    border-radius: 65% 35% 45% 55% / 40% 60% 40% 60%;
    transform: translate3d(-80px, -60px, 0) scale(1.1);
  }
  100% {
    border-radius: 35% 65% 55% 45% / 60% 40% 60% 40%;
    transform: translate3d(40px, -40px, 0) scale(0.95);
  }
}

/* Cinematic Cyber Light Shafts / Rays */
.soal-light-shaft {
  position: absolute;
  top: -20vh;
  width: 440px;
  height: 130vh;
  pointer-events: none;
  filter: blur(55px);
  -webkit-filter: blur(55px);
  opacity: 0.55;
  will-change: opacity, transform;
}
.soal-light-shaft.shaft-left {
  left: -100px;
  background: linear-gradient(135deg, rgba(16, 185, 129, 0.18) 0%, rgba(6, 182, 212, 0.08) 50%, transparent 80%);
  transform: rotate(-24deg);
  animation: shaftPulse1 12s ease-in-out infinite alternate;
}
.soal-light-shaft.shaft-right {
  right: -100px;
  background: linear-gradient(225deg, rgba(6, 182, 212, 0.18) 0%, rgba(16, 185, 129, 0.08) 50%, transparent 80%);
  transform: rotate(24deg);
  animation: shaftPulse2 14s ease-in-out infinite alternate;
}
html:not(.dark) .soal-light-shaft.shaft-left {
  background: linear-gradient(135deg, rgba(16, 185, 129, 0.14) 0%, rgba(6, 182, 212, 0.06) 50%, transparent 80%);
}
html:not(.dark) .soal-light-shaft.shaft-right {
  background: linear-gradient(225deg, rgba(6, 182, 212, 0.14) 0%, rgba(16, 185, 129, 0.06) 50%, transparent 80%);
}

@keyframes shaftPulse1 {
  0% { opacity: 0.35; transform: rotate(-24deg) translateY(0); }
  100% { opacity: 0.65; transform: rotate(-22deg) translateY(20px); }
}
@keyframes shaftPulse2 {
  0% { opacity: 0.35; transform: rotate(24deg) translateY(0); }
  100% { opacity: 0.65; transform: rotate(22deg) translateY(20px); }
}

/* Cyber Matrix Grid */
.soal-ambient-grid {
  position: absolute;
  inset: 0;
  background-image:
    radial-gradient(circle, rgba(16, 185, 129, 0.14) 1.2px, transparent 1.2px),
    linear-gradient(to right, rgba(16, 185, 129, 0.035) 1px, transparent 1px),
    linear-gradient(to bottom, rgba(16, 185, 129, 0.035) 1px, transparent 1px);
  background-size: 36px 36px, 36px 36px, 36px 36px;
  mask-image: radial-gradient(ellipse 90% 85% at 50% 35%, black 40%, transparent 95%);
  -webkit-mask-image: radial-gradient(ellipse 90% 85% at 50% 35%, black 40%, transparent 95%);
}
html:not(.dark) .soal-ambient-grid {
  background-image:
    radial-gradient(circle, rgba(16, 185, 129, 0.16) 1.2px, transparent 1.2px),
    linear-gradient(to right, rgba(16, 185, 129, 0.045) 1px, transparent 1px),
    linear-gradient(to bottom, rgba(16, 185, 129, 0.045) 1px, transparent 1px);
  mask-image: radial-gradient(ellipse 90% 85% at 50% 35%, black 45%, transparent 95%);
  -webkit-mask-image: radial-gradient(ellipse 90% 85% at 50% 35%, black 45%, transparent 95%);
}

.soal-grid-scanner {
  position: absolute;
  left: 0;
  right: 0;
  height: 140px;
  background: linear-gradient(180deg, transparent, rgba(16, 185, 129, 0.07), transparent);
  animation: gridScanner 14s cubic-bezier(0.4, 0, 0.2, 1) infinite;
  pointer-events: none;
}
html:not(.dark) .soal-grid-scanner {
  background: linear-gradient(180deg, transparent, rgba(16, 185, 129, 0.09), transparent);
}
@keyframes gridScanner {
  0% { top: -150px; opacity: 0; }
  15% { opacity: 1; }
  85% { opacity: 1; }
  100% { top: 100vh; opacity: 0; }
}

/* Interactive Quantum Constellation Canvas */
.soal-constellation-canvas {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
  z-index: 2;
}

/* Subtle Interactive Cursor Glow */
.soal-cursor-glow {
  position: absolute;
  top: var(--cursor-y, 50vh);
  left: var(--cursor-x, 50vw);
  width: 560px;
  height: 560px;
  transform: translate(-50%, -50%);
  border-radius: 50%;
  pointer-events: none;
  background: radial-gradient(circle, rgba(16, 185, 129, 0.09) 0%, rgba(6, 182, 212, 0.04) 40%, transparent 70%);
  filter: blur(65px);
  will-change: top, left;
  opacity: 0.9;
}
html:not(.dark) .soal-cursor-glow {
  background: radial-gradient(circle, rgba(16, 185, 129, 0.07) 0%, rgba(6, 182, 212, 0.03) 40%, transparent 70%);
}

/* Floating Micro-Particles */
.soal-particle {
  position: absolute;
  border-radius: 50%;
  pointer-events: none;
  background: #10B981;
  box-shadow: 0 0 10px rgba(16, 185, 129, 0.7);
  opacity: 0;
  animation: particleFloat linear infinite;
}
html:not(.dark) .soal-particle {
  background: #059669;
  box-shadow: 0 0 8px rgba(5, 150, 105, 0.45);
}
.soal-particle.p-cyan {
  background: #06B6D4;
  box-shadow: 0 0 10px rgba(6, 182, 212, 0.7);
}
html:not(.dark) .soal-particle.p-cyan {
  background: #0284C7;
  box-shadow: 0 0 8px rgba(2, 132, 199, 0.45);
}
.soal-particle.p1 { width: 4px; height: 4px; left: 8%; animation-duration: 22s; animation-delay: 0s; }
.soal-particle.p2 { width: 5px; height: 5px; left: 22%; animation-duration: 26s; animation-delay: 5s; }
.soal-particle.p3 { width: 3px; height: 3px; left: 35%; animation-duration: 19s; animation-delay: 2s; }
.soal-particle.p4 { width: 5px; height: 5px; left: 68%; animation-duration: 24s; animation-delay: 7s; }
.soal-particle.p5 { width: 4px; height: 4px; left: 82%; animation-duration: 21s; animation-delay: 3s; }
.soal-particle.p6 { width: 3px; height: 3px; left: 93%; animation-duration: 28s; animation-delay: 9s; }

@keyframes particleFloat {
  0% { transform: translateY(105vh) translateX(0) scale(0.6); opacity: 0; }
  15% { opacity: 0.65; }
  85% { opacity: 0.65; }
  100% { transform: translateY(-5vh) translateX(25px) scale(1.1); opacity: 0; }
}

/* Enhanced Glassmorphism & Elevation for Cards */
.infobar,
.content-text-card,
.file-card,
.task-card {
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
  transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
}

html:not(.dark) .infobar,
html:not(.dark) .content-text-card,
html:not(.dark) .file-card,
html:not(.dark) .task-card {
  background: rgba(255, 255, 255, 0.94) !important;
  box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05), 0 0 20px rgba(16, 185, 129, 0.04) !important;
}

html.dark .infobar,
html.dark .content-text-card,
html.dark .file-card,
html.dark .task-card,
:root .infobar,
:root .content-text-card,
:root .file-card,
:root .task-card {
  background: rgba(15, 23, 19, 0.90) !important;
  box-shadow: 0 15px 40px -10px rgba(0, 0, 0, 0.75), 0 0 25px rgba(16, 185, 129, 0.08) !important;
}

@media (prefers-reduced-motion: reduce) {
  .soal-aurora-blob,
  .soal-light-shaft,
  .soal-grid-scanner,
  .soal-particle,
  .soal-constellation-canvas {
    animation: none !important;
  }
}

/* ─── Ultra "Wah" SweetAlert & Cyber Lock Modal Backdrop Blur ─── */
.swal2-container {
  backdrop-filter: blur(16px) !important;
  -webkit-backdrop-filter: blur(16px) !important;
}

/* ─── Ultra "Wah" Cyber Access Ended Lock Modal (Backdrop Blur & High-End Glassmorphism) ─── */
.soal-lock-overlay {
  position: fixed;
  inset: 0;
  z-index: 99999;
  background: rgba(9, 13, 11, 0.85);
  backdrop-filter: blur(18px);
  -webkit-backdrop-filter: blur(18px);
  display: none;
  align-items: center;
  justify-content: center;
  padding: 20px;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}
.soal-lock-overlay.is-active {
  opacity: 1;
  pointer-events: auto;
}
html:not(.dark) .soal-lock-overlay {
  background: rgba(244, 247, 245, 0.88);
}

.soal-lock-card {
  max-width: 480px;
  width: 100%;
  background: radial-gradient(circle at top, rgba(239, 68, 68, 0.12), transparent 70%), rgba(15, 23, 19, 0.95);
  border: 1px solid rgba(239, 68, 68, 0.45);
  border-radius: 28px;
  padding: 34px 28px;
  text-align: center;
  box-shadow: 0 30px 70px -10px rgba(0, 0, 0, 0.9), 0 0 45px rgba(239, 68, 68, 0.22);
  transform: scale(0.92) translateY(12px);
  transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
  position: relative;
  overflow: hidden;
}
.soal-lock-overlay.is-active .soal-lock-card {
  transform: scale(1) translateY(0);
}
html:not(.dark) .soal-lock-card {
  background: radial-gradient(circle at top, rgba(239, 68, 68, 0.08), transparent 70%), rgba(255, 255, 255, 0.98);
  border-color: rgba(239, 68, 68, 0.35);
  box-shadow: 0 25px 60px rgba(0, 0, 0, 0.12), 0 0 35px rgba(239, 68, 68, 0.12);
}

/* Animated Lock Icon */
.soal-lock-icon-wrap {
  position: relative;
  width: 76px;
  height: 76px;
  margin: 0 auto 18px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.soal-lock-icon-ping {
  position: absolute;
  inset: 0;
  border-radius: 24px;
  background: rgba(239, 68, 68, 0.3);
  animation: lockIconPulse 2s cubic-bezier(0, 0, 0.2, 1) infinite;
}
@keyframes lockIconPulse {
  0% { transform: scale(0.9); opacity: 0.8; }
  70%, 100% { transform: scale(1.35); opacity: 0; }
}
.soal-lock-icon-box {
  position: relative;
  z-index: 2;
  width: 64px;
  height: 64px;
  border-radius: 20px;
  background: linear-gradient(135deg, rgba(239, 68, 68, 0.25), rgba(185, 28, 28, 0.4));
  border: 1.5px solid rgba(239, 68, 68, 0.6);
  color: #F87171;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 0 25px rgba(239, 68, 68, 0.35);
}
html:not(.dark) .soal-lock-icon-box {
  background: linear-gradient(135deg, rgba(254, 226, 226, 0.9), rgba(254, 202, 202, 0.7));
  border-color: rgba(239, 68, 68, 0.5);
  color: #DC2626;
}

/* Status Chip */
.soal-lock-chip {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 4px 14px;
  border-radius: 50px;
  background: rgba(239, 68, 68, 0.12);
  border: 1px solid rgba(239, 68, 68, 0.35);
  color: #F87171;
  font-size: 11px;
  font-weight: 800;
  font-family: 'Syne', sans-serif;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  margin-bottom: 14px;
}
.soal-lock-chip-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #EF4444;
  box-shadow: 0 0 8px #EF4444;
}

.soal-lock-title {
  font-family: 'Syne', sans-serif;
  font-size: 20px;
  font-weight: 800;
  color: #FFFFFF;
  margin-bottom: 10px;
  line-height: 1.35;
}
html:not(.dark) .soal-lock-title {
  color: #0F1F17;
}

.soal-lock-desc {
  font-size: 13.5px;
  color: #9CA3AF;
  line-height: 1.6;
  margin-bottom: 20px;
}
html:not(.dark) .soal-lock-desc {
  color: #4B5563;
}

/* Lock Meta Table */
.soal-lock-meta {
  background: rgba(0, 0, 0, 0.35);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 16px;
  padding: 12px 16px;
  margin-bottom: 16px;
  text-align: left;
  display: flex;
  flex-direction: column;
  gap: 8px;
  font-size: 12px;
}
html:not(.dark) .soal-lock-meta {
  background: #F9FAFB;
  border-color: #E5E7EB;
}
.soal-lock-meta-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
}
.soal-lock-meta-label {
  color: #6B7280;
  font-weight: 500;
  flex-shrink: 0;
}
.soal-lock-meta-val {
  font-weight: 700;
  color: #F3F4F6;
  text-align: right;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  max-width: 250px;
}
html:not(.dark) .soal-lock-meta-val {
  color: #111827;
}
.soal-lock-meta-val.text-amber {
  color: #FBBF24 !important;
}
html:not(.dark) .soal-lock-meta-val.text-amber {
  color: #D97706 !important;
}

/* Notice box */
.soal-lock-notice {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  background: rgba(245, 158, 11, 0.08);
  border: 1px solid rgba(245, 158, 11, 0.25);
  border-radius: 14px;
  padding: 10px 14px;
  font-size: 11.5px;
  color: #FCD34D;
  text-align: left;
  line-height: 1.5;
  margin-bottom: 22px;
}
html:not(.dark) .soal-lock-notice {
  background: #FEF3C7;
  border-color: #FDE68A;
  color: #92400E;
}

/* Actions */
.soal-lock-actions {
  display: flex;
  align-items: center;
  gap: 10px;
}
.soal-lock-btn-primary {
  flex: 1;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px 18px;
  border-radius: 12px;
  background: linear-gradient(135deg, #10B981, #0D9488);
  color: #FFFFFF !important;
  font-family: 'Syne', sans-serif;
  font-weight: 700;
  font-size: 13px;
  text-decoration: none;
  box-shadow: 0 4px 18px rgba(16, 185, 129, 0.35);
  transition: all 0.2s ease;
  cursor: pointer;
}
.soal-lock-btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 24px rgba(16, 185, 129, 0.45);
}
.soal-lock-btn-secondary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 12px 16px;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.12);
  color: #E5E7EB;
  font-family: 'Syne', sans-serif;
  font-weight: 600;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.2s ease;
}
.soal-lock-btn-secondary:hover {
  background: rgba(255, 255, 255, 0.1);
  color: #FFFFFF;
}
html:not(.dark) .soal-lock-btn-secondary {
  background: #F3F4F6;
  border-color: #E5E7EB;
  color: #374151;
}
html:not(.dark) .soal-lock-btn-secondary:hover {
  background: #E5E7EB;
  color: #111827;
}
</style>
</head>
<body>

{{-- ─── AMBIENT CYBER BACKGROUND (High-End Aurora, Light Rays & Interactive Quantum Mesh) ─── --}}
<div class="soal-ambient-canvas" aria-hidden="true">
    {{-- Fluid Cosmic Morphing Aurora Blobs --}}
    <div class="soal-aurora-blob blob-1"></div>
    <div class="soal-aurora-blob blob-2"></div>
    <div class="soal-aurora-blob blob-3"></div>
    <div class="soal-aurora-blob blob-4"></div>

    {{-- Cinematic Cyber Light Rays (Atmospheric shafts) --}}
    <div class="soal-light-shaft shaft-left"></div>
    <div class="soal-light-shaft shaft-right"></div>

    {{-- Micro Cyber Matrix Grid & Hologram Scanner --}}
    <div class="soal-ambient-grid"></div>
    <div class="soal-grid-scanner"></div>

    {{-- Interactive Quantum Constellation Canvas (Plexus / Synaptic Nodes) --}}
    <canvas id="ambientConstellationCanvas" class="soal-constellation-canvas"></canvas>

    {{-- Subtle Floating Horizon Glow & Particles --}}
    <div class="soal-particle p1"></div>
    <div class="soal-particle p2 p-cyan"></div>
    <div class="soal-particle p3"></div>
    <div class="soal-particle p4 p-cyan"></div>
    <div class="soal-particle p5"></div>
    <div class="soal-particle p6"></div>

    {{-- Interactive Cursor Aura Halo --}}
    <div class="soal-cursor-glow"></div>
</div>

{{-- ─── TOP BAR ─── --}}
<header class="topbar">
    <div class="topbar-left">
        {{-- Platform Brand --}}
        <a href="{{ route('home') }}" class="topbar-brand-link" title="Beranda IC-Labs Repository">
            <div class="brand-logo-box">
                <img src="{{ asset('images/iclabs-logo.png') }}" alt="IC-Labs Logo" class="brand-logo-img" draggable="false">
            </div>
            <div class="brand-text-block">
                <span class="brand-name">IC-LABS</span>
                <span class="brand-tag">REPOSITORY</span>
            </div>
        </a>

        {{-- Cyber Divider --}}
        <div class="topbar-divider" aria-hidden="true"></div>

        {{-- Document Title & Category Metadata --}}
        <div class="topbar-doc-info">
            <div class="topbar-title-row">
                <h1 class="topbar-doc-title" title="{{ $soal->title }}">{{ $soal->title }}</h1>
            </div>
            <div class="topbar-meta-row">
                @if($soal->category)
                    <span class="category-pill" style="background:{{ $soal->category->color }}18;color:{{ $soal->category->color }};border:1px solid {{ $soal->category->color }}40;">
                        <span class="category-dot" style="background:{{ $soal->category->color }};box-shadow:0 0 6px {{ $soal->category->color }};"></span>
                        {{ $soal->category->name }}
                    </span>
                @else
                    <span class="category-pill" style="background:rgba(16,185,129,0.12);color:#34D399;border:1px solid rgba(16,185,129,0.25);">
                        <span class="category-dot" style="background:#10B981;box-shadow:0 0 6px #10B981;"></span>
                        Dokumen Soal
                    </span>
                @endif
                @if($soal->access_type === 'scheduled' && $soal->active_until)
                    <span class="schedule-pill">
                        <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Berakhir {{ $soal->active_until->format('d M Y, H:i') }}
                    </span>
                @endif
            </div>
        </div>
    </div>
    <div style="display:flex;align-items:center;gap:12px;flex-shrink:0;">
        <x-theme-switcher :compact="true" />
        <div class="badge-active">
            <span class="badge-dot"></span>
            Akses Aktif
        </div>
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
                <button type="button" class="btn-copy-text" onclick="navigator.clipboard.writeText(document.getElementById('soal-raw-text').innerText).then(()=>{ if(window.Swal) Swal.fire({toast:true,position:'top-end',icon:'success',title:'Teks instruksi soal berhasil disalin!',showConfirmButton:false,timer:2500,background:'#0F1713',color:'#F3F4F6',customClass:{popup:'border border-emerald-500/30 rounded-xl'}}); else alert('Teks soal berhasil disalin!'); })">
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
                        'token'      => $soal->slug ?: $soal->unique_token,
                        'attachment' => $attachment->id,
                    ]);
                    $downloadUrl = route('soal.download', [
                        'token'      => $soal->slug ?: $soal->unique_token,
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
                    <div class="assistant-note-card">
                        <div class="note-badge">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Catatan Asisten</span>
                        </div>
                        <div class="note-text">
                            {!! nl2br(e($soal->submission_instructions)) !!}
                        </div>
                    </div>
                @endif

                @if(isset($isAdminPreview) && $isAdminPreview)
                    <div class="deadline-passed-notice preview-notice" style="border-color:rgba(59,130,246,0.25);color:var(--blue);">
                        Mode preview admin — pengumpulan tugas tidak tersedia.
                    </div>
                @elseif($isDeadlinePassed)
                    <div class="deadline-passed-notice">
                        Maaf, batas waktu pengumpulan tugas telah berakhir.
                    </div>
                @else
                    {{-- Kebijakan Ringkas (Aktif saat belum ada tugas lama) --}}
                    <div id="submission-policy-tip" class="sub-policy-tip">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Satu NIM hanya menyimpan satu data tugas. Pengunggahan baru dengan NIM sama otomatis memperbarui berkas sebelumnya.</span>
                    </div>

                    {{-- Card Status Terdaftar (Tampil Dinamis menggantikan tip umum saat NIM memiliki tugas lama) --}}
                    <div id="existing-submission-banner" class="prior-submission-card" style="display:none;">
                        <div class="prior-card-header">
                            <div class="prior-status-pill">
                                <span class="prior-pulse-dot"></span>
                                <span>DATA TUGAS TERDAFTAR</span>
                            </div>
                            <span class="prior-mode-tag">Mode Pembaruan</span>
                        </div>
                        <div class="prior-card-body">
                            <div class="prior-meta-row">
                                <div class="prior-meta-item">
                                    <span class="p-label">Mahasiswa:</span>
                                    <strong id="existing-sub-name" class="p-val">—</strong>
                                </div>
                                <div class="prior-meta-item">
                                    <span class="p-label">Waktu Pengumpulan:</span>
                                    <span id="existing-sub-time" class="p-val">—</span>
                                </div>
                            </div>
                            <div class="prior-file-row">
                                <span class="p-label">Berkas Tersimpan:</span>
                                <span id="existing-sub-file" class="p-val file-tag">—</span>
                            </div>
                        </div>
                        <div class="prior-warning-bar">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span>Pengiriman ulang akan <strong>menimpa dan menggantikan</strong> seluruh berkas sebelumnya.</span>
                        </div>
                        {{-- Hidden legacy element to ensure backward compatibility --}}
                        <div id="existing-submission-info" style="display:none;"></div>
                    </div>

                    <form id="submission-form" autocomplete="off">
                        @csrf

                        <div class="form-grid {{ $soal->hasClasses() ? 'has-classes' : '' }}">
                            <div class="field-group">
                                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                                    <label class="field-label" style="margin-bottom:0;">
                                        NIM Mahasiswa <span class="req">*</span>
                                    </label>
                                    <span id="sub_nim_badge" style="font-size:11px;font-family:monospace;font-weight:600;color:var(--text3);">0/11 digit</span>
                                </div>
                                <div class="field-wrap">
                                    <input type="text" id="sub_nim" name="nim" required
                                           inputmode="numeric" pattern="[0-9]{11}" maxlength="11" minlength="11"
                                           placeholder="Wajib 11 digit angka (0-9)"
                                           autocomplete="off"
                                           class="field-input"
                                           oninput="this.value = this.value.replace(/\D/g, '').slice(0, 11);">
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
                            @if($soal->hasClasses())
                            <div class="field-group" x-data="{
                                open: false,
                                selected: (function() {
                                    try {
                                        const sk = localStorage.getItem('labsoal_student_kelas');
                                        return (sk && @js($soal->getClassesList()).includes(sk)) ? sk : '';
                                    } catch(e) { return ''; }
                                })(),
                                selectedLabel: '— Pilih Kelas Anda —',
                                init() {
                                    if (this.selected) {
                                        this.selectedLabel = 'Kelas ' + this.selected;
                                        const hiddenEl = document.getElementById('sub_kelas');
                                        if (hiddenEl) hiddenEl.value = this.selected;
                                    }
                                },
                                selectClass(val, label) {
                                    this.selected = val;
                                    this.selectedLabel = label;
                                    this.open = false;
                                    try { localStorage.setItem('labsoal_student_kelas', val); } catch(e) {}
                                    const hiddenEl = document.getElementById('sub_kelas');
                                    if (hiddenEl) {
                                        hiddenEl.value = val;
                                        hiddenEl.dispatchEvent(new Event('change', { bubbles: true }));
                                    }
                                }
                            }" @click.outside="open = false" @keydown.escape.window="open = false">
                                <label class="field-label">
                                    Kelas <span class="req">*</span>
                                </label>
                                <div class="custom-cyber-dropdown">
                                    <input type="hidden" id="sub_kelas" name="kelas" :value="selected" required>
                                    
                                    <button type="button" 
                                            @click="open = !open" 
                                            class="custom-cyber-trigger"
                                            :class="{ 'is-open': open }">
                                        <span class="trigger-text font-medium" x-text="selectedLabel" :style="selected ? 'color:var(--text1);font-weight:600;' : 'color:var(--text3);'"></span>
                                        <svg class="trigger-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>

                                    <svg class="field-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="pointer-events:none;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>

                                    {{-- Cyber Dropdown Options Menu Popup --}}
                                    <div x-show="open" 
                                         x-cloak
                                         x-transition:enter="transition ease-out duration-150"
                                         x-transition:enter-start="opacity-0 -translate-y-2 scale-98"
                                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                         x-transition:leave="transition ease-in duration-100"
                                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                         x-transition:leave-end="opacity-0 -translate-y-2 scale-98"
                                         class="custom-cyber-menu">
                                         <div class="custom-cyber-menu-header">
                                             <span>Pilih Kelas Praktikum</span>
                                             <span style="font-size:9px;color:#10B981;font-weight:600;">{{ count($soal->getClassesList()) }} Kelas</span>
                                         </div>
                                         @foreach($soal->getClassesList() as $kelasOption)
                                             <button type="button" 
                                                     @click="selectClass('{{ $kelasOption }}', 'Kelas {{ $kelasOption }}')"
                                                     class="custom-cyber-menu-item"
                                                     :class="{ 'is-selected': selected === '{{ $kelasOption }}' }">
                                                 <span style="display:flex;align-items:center;">
                                                     <span class="item-dot"></span>
                                                     <span>Kelas {{ $kelasOption }}</span>
                                                 </span>
                                                 <svg x-show="selected === '{{ $kelasOption }}'" class="check-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                 </svg>
                                             </button>
                                         @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif
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
                            @php $maxAllowedFiles = $soal->getMaxFiles(); @endphp
                            <div class="sub-pane {{ $defaultSubType === 'file' ? 'active' : '' }}" id="pane-file">
                                <label class="field-label" style="margin-bottom:7px">
                                    File Tugas <span class="req">*</span>
                                    <span class="note">Maks. {{ $maxAllowedFiles }} Berkas · Maks. 10 MB/file (Check Keamanan Otomatis)</span>
                                </label>

                                <div class="dropzone" id="dropzone"
                                     onclick="document.getElementById('sub_file').click()">
                                    <div id="dz-empty">
                                        <div class="dz-icon-wrap">
                                            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                            </svg>
                                        </div>
                                        <div class="dz-text">
                                            @if($maxAllowedFiles > 1)
                                                Klik atau seret hingga {{ $maxAllowedFiles }} file ke sini
                                            @else
                                                Klik atau seret file ke sini
                                            @endif
                                        </div>
                                        <div class="dz-sub">{{ $extText }} · Maks. {{ $maxAllowedFiles }} Berkas (10 MB/file)</div>
                                    </div>
                                    <div class="dz-success" id="dz-success" style="display:none;flex-direction:column;gap:10px;width:100%;text-align:left;">
                                        <div style="display:flex;align-items:center;justify-content:space-between;width:100%;flex-wrap:wrap;gap:8px;">
                                            <div style="display:flex;align-items:center;gap:8px;">
                                                <div class="dz-success-icon" style="flex-shrink:0;">
                                                    <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                </div>
                                                <span id="dz-files-summary" style="font-size:12px;font-weight:600;color:var(--emerald);"></span>
                                            </div>
                                            <div style="display:flex;align-items:center;gap:8px;">
                                                @if($maxAllowedFiles > 1)
                                                    <button type="button" class="btn-dz-add"
                                                            onclick="event.stopPropagation();document.getElementById('sub_file').click()"
                                                            title="Pilih dan tambahkan berkas dari folder lain">
                                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                                        </svg>
                                                        <span>Tambah Berkas</span>
                                                    </button>
                                                @endif
                                                <button type="button" class="btn-dz-reset"
                                                        onclick="event.stopPropagation();resetAllSelectedFiles()"
                                                        title="Kosongkan seluruh berkas yang dipilih">
                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                    <span>Reset Semua</span>
                                                </button>
                                            </div>
                                        </div>
                                        <div id="dz-files-list" style="display:flex;flex-direction:column;gap:6px;width:100%;max-height:180px;overflow-y:auto;padding-right:4px;"></div>
                                        @if($maxAllowedFiles > 1)
                                            <div style="font-size:11px;color:var(--text3);text-align:center;padding-top:4px;border-top:1px dashed var(--border);">
                                                💡 Klik di sini atau seret berkas lagi untuk menambahkan file dari folder lain
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <input type="file"
                                       id="sub_file"
                                       name="files[]"
                                       {{ $maxAllowedFiles > 1 ? 'multiple' : '' }}
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

// ─── Cyber Lock Modal for Stopped / Expired Access ───
let isLockModalShown = false;

function showAccessEndedModal(statusData) {
    if (isLockModalShown) return;
    isLockModalShown = true;

    const modal = document.getElementById('soal-access-lock-modal');
    if (!modal) return;

    const titleEl = document.getElementById('soal-lock-title');
    const descEl = document.getElementById('soal-lock-desc');
    const badgeEl = document.getElementById('soal-lock-status-badge');
    const timeValEl = document.getElementById('soal-lock-time-val');
    const noticeTextEl = document.getElementById('soal-lock-notice-text');

    const status = statusData ? statusData.access_status : 'expired';

    if (status === 'inactive') {
        if (titleEl) titleEl.textContent = 'Akses Dokumen Dihentikan Pengawas';
        if (descEl) descEl.textContent = (statusData && statusData.message) ? statusData.message : 'Pengawas atau instruktur laboratorium telah menghentikan akses ke dokumen soal ini. Sesi pengerjaan dinyatakan selesai.';
        if (badgeEl) badgeEl.textContent = 'AKSES DIHENTIKAN';
        if (noticeTextEl) noticeTextEl.textContent = 'Akses telah ditutup dari ruang kontrol pengawas laboratorium. Hubungi asisten atau instruktur lab jika Anda memerlukan info lanjutan.';
    } else if (status === 'expired') {
        if (titleEl) titleEl.textContent = 'Waktu Akses Soal Telah Berakhir';
        if (descEl) descEl.textContent = (statusData && statusData.message) ? statusData.message : 'Batas waktu pengerjaan atau akses dokumen soal ini telah berakhir sesuai jadwal pelaksanaan laboratorium. Lembar tugas telah ditutup secara otomatis.';
        if (badgeEl) badgeEl.textContent = 'WAKTU AKSES HABIS';
    } else if (status === 'deleted') {
        if (titleEl) titleEl.textContent = 'Dokumen Soal Tidak Tersedia';
        if (descEl) descEl.textContent = 'Dokumen soal ini telah dihapus atau dipindahkan oleh administrator sistem.';
        if (badgeEl) badgeEl.textContent = 'TIDAK TERSEDIA';
    } else if (statusData && statusData.is_deadline_passed) {
        if (titleEl) titleEl.textContent = 'Batas Waktu Pengumpulan Berakhir';
        if (descEl) descEl.textContent = 'Batas waktu pengumpulan tugas untuk soal ini telah berakhir. Pengiriman berkas baru tidak dapat lagi diproses.';
        if (badgeEl) badgeEl.textContent = 'DEADLINE BERAKHIR';
    }

    if (statusData && statusData.active_until && timeValEl) {
        timeValEl.textContent = statusData.active_until;
    } else if (statusData && statusData.deadline_at && timeValEl) {
        timeValEl.textContent = statusData.deadline_at;
    }

    // Blur main content and disable all interactions
    const mainWrap = document.querySelector('main');
    if (mainWrap) {
        mainWrap.style.filter = 'blur(12px)';
        mainWrap.style.pointerEvents = 'none';
        mainWrap.style.transition = 'filter 0.4s ease';
    }

    modal.style.display = 'flex';
    requestAnimationFrame(() => {
        modal.classList.add('is-active');
    });
}

function hideAccessEndedModal() {
    isLockModalShown = false;
    const modal = document.getElementById('soal-access-lock-modal');
    if (modal) {
        modal.classList.remove('is-active');
        setTimeout(() => { modal.style.display = 'none'; }, 300);
    }
    const mainWrap = document.querySelector('main');
    if (mainWrap) {
        mainWrap.style.filter = '';
        mainWrap.style.pointerEvents = '';
    }
}

// ─── Auto-trigger popup when scheduled access expires ───
@if($soal->access_type === 'scheduled' && $soal->active_until)
    @php $remainingSeconds = max(0, now()->diffInSeconds($soal->active_until, false)); @endphp
    @if($remainingSeconds > 0)
        setTimeout(() => {
            showAccessEndedModal({
                access_status: 'expired',
                title: 'Waktu Akses Soal Telah Berakhir',
                message: 'Batas waktu pengerjaan atau akses dokumen soal ini telah berakhir sesuai jadwal laboratorium. Lembar tugas telah ditutup secara otomatis.',
                active_until: '{{ $soal->active_until->format('d M Y, H:i') }}'
            });
        }, {{ $remainingSeconds }} * 1000);
    @else
        setTimeout(() => {
            showAccessEndedModal({
                access_status: 'expired',
                title: 'Waktu Akses Soal Telah Berakhir',
                message: 'Batas waktu pengerjaan atau akses dokumen soal ini telah berakhir sesuai jadwal laboratorium. Lembar tugas telah ditutup secara otomatis.',
                active_until: '{{ $soal->active_until->format('d M Y, H:i') }}'
            });
        }, 300);
    @endif
@endif

// ─── Polling: check if admin changes access or access expires ───
@if(!isset($isAdminPreview) || !$isAdminPreview)
function checkSoalStatusNow(isManual = false) {
    const statusUrl = '{{ route('soal.status', $soal->slug ?: $soal->unique_token, false) }}';
    const recheckBtn = document.getElementById('btn-recheck-soal-status');
    if (isManual && recheckBtn) {
        recheckBtn.disabled = true;
        recheckBtn.style.opacity = '0.6';
    }

    fetch(statusUrl, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
            if (isManual && recheckBtn) {
                recheckBtn.disabled = false;
                recheckBtn.style.opacity = '1';
            }

            if (data.access_status !== 'active') {
                showAccessEndedModal(data);
            } else if (isLockModalShown) {
                hideAccessEndedModal();
            }

            if (data.is_downloadable !== {{ $soal->is_downloadable ? 'true' : 'false' }}) {
                window.location.reload();
            }
        })
        .catch(() => {
            if (isManual && recheckBtn) {
                recheckBtn.disabled = false;
                recheckBtn.style.opacity = '1';
            }
        });
}

// Polling interval: every 7 seconds for responsive supervisor control
setInterval(checkSoalStatusNow, 7000);
@endif

// ─── Submission form logic ───
@if($soal->is_submission_enabled && (!isset($isDeadlinePassed) || !$isDeadlinePassed) && (!isset($isAdminPreview) || !$isAdminPreview))

const dropzone     = document.getElementById('dropzone');
const fileInput    = document.getElementById('sub_file');
const dzEmpty      = document.getElementById('dz-empty');
const dzSuccess    = document.getElementById('dz-success');
const dzFilename   = document.getElementById('dz-filename');
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
    if (window.Swal) {
        const isDark = document.documentElement.classList.contains('dark');
        Swal.fire({
            icon: type === 'success' ? 'success' : 'error',
            title: type === 'success' ? 'Tugas Berhasil Terkirim!' : 'Perhatian',
            text: msg,
            background: isDark ? '#0F1713' : '#FFFFFF',
            color: isDark ? '#F3F4F6' : '#111827',
            backdrop: isDark ? 'rgba(9, 13, 11, 0.75)' : 'rgba(0, 0, 0, 0.45)',
            confirmButtonText: 'Oke, Mengerti',
            customClass: {
                popup: 'cyber-swal-popup ' + (type === 'success' ? 'cyber-swal-success' : 'cyber-swal-error'),
                title: 'cyber-swal-title',
                htmlContainer: 'cyber-swal-text',
                confirmButton: 'cyber-swal-btn ' + (type === 'success' ? 'cyber-swal-btn-success' : 'cyber-swal-btn-error')
            }
        });
    } else {
        alert(msg);
    }
}

const maxFilesAllowed = {{ (int) $soal->getMaxFiles() }};
let selectedFiles = [];

function formatFileSize(bytes) {
    if (bytes > 1024 * 1024) return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    return (bytes / 1024).toFixed(0) + ' KB';
}

function resetAllSelectedFiles() {
    selectedFiles = [];
    if (fileInput) fileInput.value = '';
    renderSelectedFilesUI();
}

function handleFilesSelected(incomingFiles) {
    const list = Array.from(incomingFiles);
    if (!list.length) return;

    // Filter duplikat: jika file dengan nama, ukuran, dan waktu modifikasi sama sudah dipilih sebelumnya
    const newFiles = [];
    const duplicateNames = [];

    for (let f of list) {
        const isDuplicate = selectedFiles.some(existing => 
            existing.name === f.name && existing.size === f.size && existing.lastModified === f.lastModified
        );
        if (isDuplicate) {
            duplicateNames.push(f.name);
        } else {
            newFiles.push(f);
        }
    }

    if (duplicateNames.length > 0 && newFiles.length === 0) {
        showAlert('error', `Berkas "${duplicateNames.join(', ')}" sudah ada di dalam daftar pilihan.`);
        return;
    }

    // Mode berkas tunggal (jika hanya 1 berkas yang diizinkan)
    if (maxFilesAllowed === 1) {
        if (newFiles.length > 1) {
            showAlert('error', `Tugas ini hanya mengizinkan pengumpulan maksimal 1 berkas.`);
            return;
        }
        const singleFile = newFiles[0] || list[0];
        if (singleFile.size > 10 * 1024 * 1024) {
            showAlert('error', `Berkas "${singleFile.name}" (${formatFileSize(singleFile.size)}) melebihi batas maksimal 10 MB per berkas!`);
            return;
        }
        selectedFiles = [singleFile];
        renderSelectedFilesUI();
        return;
    }

    // Mode multi-berkas: akumulasikan berkas yang sudah ada dengan berkas baru
    const combined = [...selectedFiles, ...newFiles];

    if (combined.length > maxFilesAllowed) {
        showAlert('error', `Maksimal ${maxFilesAllowed} berkas yang dapat dikumpulkan. Saat ini sudah ada ${selectedFiles.length} berkas dipilih, Anda mencoba menambahkan ${newFiles.length} berkas baru (Total: ${combined.length} berkas).`);
        return;
    }

    // Validasi ukuran masing-masing file baru
    for (let f of newFiles) {
        if (f.size > 10 * 1024 * 1024) {
            showAlert('error', `Berkas "${f.name}" (${formatFileSize(f.size)}) melebihi batas maksimal 10 MB per berkas!`);
            return;
        }
    }

    // Validasi total ukuran seluruh berkas yang telah diakumulasi
    const totalBytes = combined.reduce((acc, f) => acc + f.size, 0);
    const maxTotalBytes = 35 * 1024 * 1024;
    if (totalBytes > maxTotalBytes) {
        showAlert('error', `Total ukuran seluruh berkas (${formatFileSize(totalBytes)}) melebihi batas aman maksimal 35 MB. Silakan kompres atau pilih berkas yang lebih ringkas.`);
        return;
    }

    selectedFiles = combined;
    renderSelectedFilesUI();

    // Notifikasi jika ada berkas duplikat yang dilewati
    if (duplicateNames.length > 0 && window.Swal) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: `Berkas duplikat "${duplicateNames.join(', ')}" dilewati. ${newFiles.length} berkas baru berhasil ditambahkan.`,
            showConfirmButton: false,
            timer: 3500,
            background: document.documentElement.classList.contains('dark') ? '#0F1713' : '#FFFFFF',
            color: document.documentElement.classList.contains('dark') ? '#F3F4F6' : '#111827',
            customClass: { popup: 'border border-emerald-500/30 rounded-xl' }
        });
    }
}

function removeSelectedFile(index, evt) {
    if (evt) evt.stopPropagation();
    selectedFiles.splice(index, 1);
    if (fileInput) fileInput.value = '';
    renderSelectedFilesUI();
}

function renderSelectedFilesUI() {
    const listEl = document.getElementById('dz-files-list');
    const summaryEl = document.getElementById('dz-files-summary');
    const dzEmptyEl = document.getElementById('dz-empty');
    const dzSuccessEl = document.getElementById('dz-success');

    if (!selectedFiles.length) {
        if (dzEmptyEl) dzEmptyEl.style.display = 'block';
        if (dzSuccessEl) dzSuccessEl.style.display = 'none';
        if (dropzone) dropzone.classList.remove('has-file');
        if (fileInput) fileInput.value = '';
        return;
    }

    if (dzEmptyEl) dzEmptyEl.style.display = 'none';
    if (dzSuccessEl) dzSuccessEl.style.display = 'flex';
    if (dropzone) dropzone.classList.add('has-file');

    if (summaryEl) {
        summaryEl.textContent = `${selectedFiles.length} Berkas Dipilih (Maks. ${maxFilesAllowed})`;
    }

    if (listEl) {
        listEl.innerHTML = '';
        selectedFiles.forEach((file, idx) => {
            const row = document.createElement('div');
            row.style.cssText = 'display:flex;align-items:center;justify-content:space-between;gap:8px;background:var(--surface2);border:1px solid var(--border-emerald);padding:6px 10px;border-radius:10px;font-size:12px;';
            row.innerHTML = `
                <div style="display:flex;align-items:center;gap:8px;min-width:0;flex:1;">
                    <span style="font-size:14px;flex-shrink:0;">📄</span>
                    <span style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:var(--text1);font-weight:500;">${file.name}</span>
                    <span style="color:var(--text3);font-family:monospace;font-size:11px;flex-shrink:0;">(${formatFileSize(file.size)})</span>
                </div>
                <button type="button" onclick="removeSelectedFile(${idx}, event)" style="background:none;border:none;color:#EF4444;cursor:pointer;padding:2px 6px;border-radius:6px;font-size:15px;line-height:1;font-weight:bold;" title="Hapus berkas ini">&times;</button>
            `;
            listEl.appendChild(row);
        });
    }
}

// ─── Real-time Submission Check & Auto-Remember ───
let checkNimTimeout = null;
let currentExistingSubmission = null;

function checkExistingSubmission(nim) {
    const banner = document.getElementById('existing-submission-banner');
    const policyTip = document.getElementById('submission-policy-tip');
    const info = document.getElementById('existing-submission-info');
    const nameEl = document.getElementById('existing-sub-name');
    const timeEl = document.getElementById('existing-sub-time');
    const fileEl = document.getElementById('existing-sub-file');

    if (!nim || nim.length < 3) {
        currentExistingSubmission = null;
        if (banner) banner.style.display = 'none';
        if (policyTip) policyTip.style.display = 'flex';
        btnLabel.textContent = 'Kirim Tugas Sekarang';
        return;
    }

    const checkUrl = '{{ route('soal.checkSubmission', ['token' => $soal->slug ?: $soal->unique_token], false) }}?nim=' + encodeURIComponent(nim);
    fetch(checkUrl, {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data && data.has_submitted) {
            data.nim = nim;
            currentExistingSubmission = data;
            if (policyTip) policyTip.style.display = 'none';
            if (banner) banner.style.display = 'block';

            const subTipe = data.submission_type === 'link' ? 'Tautan' : (data.submission_type === 'text' ? 'Jawaban Teks' : 'Berkas File');
            if (info) {
                info.textContent = `Tercatat atas nama ${data.nama}${data.kelas ? ' (Kelas ' + data.kelas + ')' : ''}, dikumpulkan pada ${data.submitted_at}. (${subTipe}: ${data.identifier})`;
            }
            if (nameEl) {
                nameEl.textContent = `${data.nama}${data.kelas ? ' (' + (data.kelas.startsWith('Kelas') ? data.kelas : 'Kelas ' + data.kelas) + ')' : ''}`;
            }
            if (timeEl) {
                timeEl.textContent = data.submitted_at;
            }
            if (fileEl) {
                fileEl.textContent = `${subTipe}: ${data.identifier}`;
            }

            const namaEl = document.getElementById('sub_nama');
            if (namaEl && !namaEl.value) {
                namaEl.value = data.nama;
                localStorage.setItem('labsoal_student_nama', data.nama);
            }
            btnLabel.textContent = 'Perbarui Tugas Sekarang';
        } else {
            currentExistingSubmission = null;
            if (banner) banner.style.display = 'none';
            if (policyTip) policyTip.style.display = 'flex';
            btnLabel.textContent = 'Kirim Tugas Sekarang';
        }
    })
    .catch(() => {});
}

// Inisialisasi Auto-Fill Identitas dari localStorage
document.addEventListener('DOMContentLoaded', function() {
    const storedNim = localStorage.getItem('labsoal_student_nim');
    const storedNama = localStorage.getItem('labsoal_student_nama');
    const storedKelas = localStorage.getItem('labsoal_student_kelas');

    const nimInput = document.getElementById('sub_nim');
    const namaInput = document.getElementById('sub_nama');
    const kelasInput = document.getElementById('sub_kelas');

    function updateSubNimFeedback(inputEl) {
        if (!inputEl) return;
        inputEl.value = inputEl.value.replace(/\D/g, '').slice(0, 11);
        const len = inputEl.value.length;
        const badge = document.getElementById('sub_nim_badge');
        if (badge) {
            badge.textContent = `${len}/11 digit`;
            if (len === 11) {
                badge.style.color = '#10B981';
            } else if (len > 0) {
                badge.style.color = '#F59E0B';
            } else {
                badge.style.color = 'var(--text3)';
            }
        }
    }

    if (nimInput) {
        if (!nimInput.value && storedNim) nimInput.value = storedNim;
        updateSubNimFeedback(nimInput);

        nimInput.addEventListener('input', function() {
            updateSubNimFeedback(this);
            const val = this.value.trim();
            localStorage.setItem('labsoal_student_nim', val);
            clearTimeout(checkNimTimeout);
            if (val.length === 11) {
                checkNimTimeout = setTimeout(() => checkExistingSubmission(val), 350);
            }
        });
        nimInput.addEventListener('change', function() {
            updateSubNimFeedback(this);
            const val = this.value.trim();
            localStorage.setItem('labsoal_student_nim', val);
            if (val.length === 11) {
                checkExistingSubmission(val);
            }
        });
        if (nimInput.value && nimInput.value.length === 11) {
            checkExistingSubmission(nimInput.value.trim());
        }
    }

    if (namaInput) {
        if (!namaInput.value && storedNama) namaInput.value = storedNama;
        namaInput.addEventListener('input', function() {
            localStorage.setItem('labsoal_student_nama', this.value.trim());
        });
    }

    if (kelasInput) {
        if (!kelasInput.value && storedKelas) {
            kelasInput.value = storedKelas;
        }
        kelasInput.addEventListener('change', function() {
            localStorage.setItem('labsoal_student_kelas', this.value.trim());
        });
    }
});

function setLoading(loading, progressText) {
    subBtn.disabled = loading;
    if (loading) {
        btnLabel.textContent = progressText || 'Mengirim Tugas...';
        const arrow = document.getElementById('btn-arrow');
        if (arrow) arrow.outerHTML = '<div class="spinner" id="btn-arrow"></div>';
        if (window.showLoading) {
            window.showLoading(
                'MENGIRIM TUGAS...',
                progressText || 'Mengunggah berkas aman dan memverifikasi data ke server...'
            );
        }
    } else {
        btnLabel.textContent = 'Kirim Tugas Sekarang';
        const spinner = document.getElementById('btn-arrow');
        if (spinner) spinner.outerHTML = '<svg id="btn-arrow" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>';
        if (window.hideLoading) {
            window.hideLoading();
        }
    }
}

if (fileInput) {
    fileInput.addEventListener('change', function() {
        if (this.files && this.files.length) handleFilesSelected(this.files);
        this.value = '';
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
        if (e.dataTransfer.files && e.dataTransfer.files.length) {
            handleFilesSelected(e.dataTransfer.files);
        }
    });
}

document.getElementById('submission-form').addEventListener('submit', async function(e) {
    e.preventDefault();

    const nimInput = document.getElementById('sub_nim');
    const nimVal = nimInput ? nimInput.value.replace(/\D/g, '') : '';
    if (!nimVal || nimVal.length !== 11) {
        showAlert('error', 'NIM harus terdiri dari tepat 11 digit angka (0-9). Saat ini: ' + (nimVal ? nimVal.length : 0) + ' digit.');
        if (nimInput) nimInput.focus();
        return;
    }

    const subKelasEl = document.getElementById('sub_kelas');
    if (subKelasEl && !subKelasEl.value) {
        showAlert('error', 'Silakan pilih kelas Anda terlebih dahulu.');
        return;
    }

    const currentType = subTypeInput ? subTypeInput.value : 'file';

    if (currentType === 'file') {
        if (!selectedFiles.length) {
            showAlert('error', 'Silakan pilih file tugas terlebih dahulu.');
            return;
        }
        if (selectedFiles.length > maxFilesAllowed) {
            showAlert('error', `Jumlah berkas melebihi batas maksimal (${maxFilesAllowed} file). Anda memilih ${selectedFiles.length} berkas.`);
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

    // ─── Konfirmasi Pengumpulan Ulang (Peringatan Timpa Berkas) ───
    let existingData = (currentExistingSubmission && currentExistingSubmission.has_submitted && currentExistingSubmission.nim === nimVal)
        ? currentExistingSubmission
        : null;

    if (!existingData && nimVal.length === 11) {
        try {
            const checkUrl = '{{ route('soal.checkSubmission', ['token' => $soal->slug ?: $soal->unique_token], false) }}?nim=' + encodeURIComponent(nimVal);
            const r = await fetch(checkUrl, { headers: { 'Accept': 'application/json' } });
            const res = await r.json();
            if (res && res.has_submitted) {
                res.nim = nimVal;
                existingData = res;
                currentExistingSubmission = res;
            }
        } catch (err) {}
    }

    if (existingData && existingData.has_submitted) {
        const isDark = document.documentElement.classList.contains('dark');
        const prevTime = existingData.submitted_at || '-';
        const prevId = existingData.identifier || 'berkas sebelumnya';
        const studentLabel = existingData.nama ? `${existingData.nama} (${nimVal})` : `NIM ${nimVal}`;

        if (window.Swal) {
            const confirmResult = await Swal.fire({
                icon: 'warning',
                title: 'Konfirmasi Pengumpulan Ulang',
                html: `
                    <div style="text-align: left; font-size: 13px; line-height: 1.6; color: ${isDark ? '#E5E7EB' : '#374151'};">
                        <p style="margin-bottom: 8px;">
                            Mahasiswa atas nama <strong>${studentLabel}</strong> terdeteksi <strong>sudah pernah mengumpulkan tugas</strong> sebelumnya:
                        </p>
                        <div style="background: ${isDark ? 'rgba(255,255,255,0.05)' : '#F3F4F6'}; border: 1px solid ${isDark ? 'rgba(255,255,255,0.1)' : '#E5E7EB'}; padding: 10px 14px; border-radius: 10px; margin-bottom: 12px; font-family: monospace; font-size: 12px;">
                            <div style="margin-bottom: 4px;">📅 Waktu: <strong style="color: ${isDark ? '#34D399' : '#059669'};">${prevTime}</strong></div>
                            <div>📦 Berkas: <strong style="color: ${isDark ? '#FCD34D' : '#D97706'};">${prevId}</strong></div>
                        </div>
                        <div style="background: ${isDark ? 'rgba(239, 68, 68, 0.15)' : '#FEF2F2'}; border-left: 4px solid #EF4444; padding: 10px 14px; border-radius: 8px; margin-bottom: 12px; color: ${isDark ? '#FCA5A5' : '#B91C1C'};">
                            <strong style="display:flex;align-items:center;gap:4px;margin-bottom:2px;">⚠️ PERINGATAN TIMPA BERKAS:</strong>
                            Pengunggahan baru ini akan <strong>menimpa & menghapus seluruh berkas lama Anda</strong> secara permanen. Pastikan berkas yang Anda pilih saat ini sudah lengkap.
                        </div>
                        <p style="margin-bottom: 0;">Apakah Anda yakin ingin mengganti berkas lama dengan pengumpulan baru ini?</p>
                    </div>
                `,
                background: isDark ? '#0F1713' : '#FFFFFF',
                color: isDark ? '#F3F4F6' : '#111827',
                backdrop: isDark ? 'rgba(9, 13, 11, 0.8)' : 'rgba(0, 0, 0, 0.5)',
                showCancelButton: true,
                confirmButtonText: 'Ya, Timpa & Kirim Sekarang',
                cancelButtonText: 'Batal / Periksa Kembali',
                focusCancel: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                customClass: {
                    popup: 'cyber-swal-popup border border-amber-500/40 rounded-2xl shadow-2xl',
                    title: 'text-lg font-extrabold text-amber-500',
                    confirmButton: 'px-5 py-2.5 rounded-xl font-bold text-sm shadow-md',
                    cancelButton: 'px-5 py-2.5 rounded-xl font-medium text-sm'
                }
            });

            if (!confirmResult.isConfirmed) {
                return; // Batalkan kirim, beri kesempatan mahasiswa periksa berkasnya
            }
        } else {
            const confirmed = confirm(`PERINGATAN TIMPA BERKAS:\n\nNIM ${nimVal} sudah pernah mengumpulkan tugas sebelumnya (${prevId} pada ${prevTime}).\n\nPengunggahan baru ini akan MENIMPA dan MENGGANTIKAN seluruh berkas lama Anda.\n\nApakah Anda yakin ingin melanjutkan?`);
            if (!confirmed) {
                return;
            }
        }
    }

    const formData = new FormData(this);
    if (currentType === 'file') {
        formData.delete('files[]');
        formData.delete('file');
        selectedFiles.forEach(f => {
            formData.append('files[]', f);
        });
        if (selectedFiles[0]) {
            formData.append('file', selectedFiles[0]);
        }
    }
    setLoading(true);

    const activeCsrfToken = document.querySelector('input[name="_token"]')?.value || '{{ csrf_token() }}';
    const submitUrl = '{{ route('soal.submitTask', ['token' => $soal->slug ?: $soal->unique_token], false) }}';

    const xhr = new XMLHttpRequest();
    xhr.open('POST', submitUrl, true);
    xhr.setRequestHeader('X-CSRF-TOKEN', activeCsrfToken);
    xhr.setRequestHeader('Accept', 'application/json');
    xhr.timeout = 180000; // 3 menit batas waktu maksimal unggahan

    if (xhr.upload && currentType === 'file') {
        xhr.upload.onprogress = function(e) {
            if (e.lengthComputable) {
                const percent = Math.round((e.loaded / e.total) * 100);
                setLoading(true, `Mengunggah berkas (${percent}%)...`);
            }
        };
    }

    xhr.onload = function() {
        setLoading(false);
        let result;
        try {
            result = JSON.parse(xhr.responseText);
        } catch {
            showAlert('error', 'Server error: ' + xhr.responseText.substring(0, 120));
            return;
        }

        if (xhr.status === 419) {
            if (result && result.csrf_token) {
                const tokenInput = document.querySelector('input[name="_token"]');
                if (tokenInput) tokenInput.value = result.csrf_token;
            }
            showAlert('error', 'Sesi pengiriman sempat terputus karena padatnya akses bersamaan. Token keamanan telah disegarkan, silakan tekan tombol Kirim Tugas sekali lagi.');
            return;
        }

        if (xhr.status >= 200 && xhr.status < 300) {
            // Simpan identitas mahasiswa agar tidak perlu ketik ulang
            const nimVal = document.getElementById('sub_nim')?.value.trim();
            const namaVal = document.getElementById('sub_nama')?.value.trim();
            const kelasVal = document.getElementById('sub_kelas')?.value.trim();
            if (nimVal) localStorage.setItem('labsoal_student_nim', nimVal);
            if (namaVal) localStorage.setItem('labsoal_student_nama', namaVal);
            if (kelasVal) localStorage.setItem('labsoal_student_kelas', kelasVal);

            // Bersihkan hanya input materi tugas (file/link/text), pertahankan identitas
            if (fileInput) fileInput.value = '';
            selectedFiles = [];
            renderSelectedFilesUI();
            const linkEl = document.getElementById('sub_link');
            if (linkEl) linkEl.value = '';
            const textEl = document.getElementById('sub_text');
            if (textEl) textEl.value = '';

            switchSubmissionType(currentType);
            if (dzEmpty) dzEmpty.style.display   = 'block';
            if (dzSuccess) dzSuccess.style.display = 'none';
            if (dropzone) dropzone.classList.remove('has-file');

            showAlert('success', result.message || 'Tugas berhasil dikirim!');
            btnLabel.textContent = 'Perbarui Tugas Sekarang';

            // Segarkan status pengumpulan secara langsung
            if (nimVal) {
                checkExistingSubmission(nimVal);
            }
        } else {
            let errorMsg = result.message || result.error || 'Terjadi kesalahan saat mengunggah.';
            if (result.errors) {
                const firstKey = Object.keys(result.errors)[0];
                errorMsg = result.errors[firstKey][0];
            }
            if (xhr.status === 403 && (errorMsg.includes('waktu akses') || errorMsg.includes('Batas waktu') || errorMsg.includes('tidak aktif'))) {
                showAccessEndedModal({
                    access_status: errorMsg.includes('tidak aktif') ? 'inactive' : 'expired',
                    title: errorMsg.includes('tidak aktif') ? 'Akses Dihentikan Pengawas' : 'Waktu Akses Telah Berakhir',
                    message: errorMsg
                });
                return;
            }
            showAlert('error', errorMsg);
        }
    };

    xhr.ontimeout = function() {
        setLoading(false);
        showAlert('error', 'Waktu pengunggahan habis (timeout). Silakan periksa koneksi jaringan atau perkecil ukuran berkas.');
    };

    xhr.onerror = function() {
        setLoading(false);
        if (!navigator.onLine) {
            showAlert('error', 'Koneksi internet Anda terputus. Mohon periksa kembali sambungan jaringan/Wi-Fi Anda.');
        } else {
            showAlert('error', 'Gagal menghubungi server. Kemungkinan ukuran berkas melebihi kapasitas server atau koneksi terputus. Silakan coba beberapa saat lagi.');
        }
    };

    xhr.send(formData);
});

// Auto refresh CSRF token periodically while viewing task
setInterval(() => {
    fetch('{{ Route::has('csrf.refresh') ? route('csrf.refresh', [], false) : '/csrf-refresh' }}')
        .then(r => r.json())
        .then(data => {
            if (data && data.csrf_token) {
                const tokenInput = document.querySelector('input[name="_token"]');
                if (tokenInput) tokenInput.value = data.csrf_token;
            }
        })
        .catch(() => {});
}, 5 * 60 * 1000);

@endif
</script>

<script>
// ─── High-End Cyber Constellation & Ambient Cursor Engine ───
(function() {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    const ambientCanvas = document.querySelector('.soal-ambient-canvas');
    const constCanvas = document.getElementById('ambientConstellationCanvas');
    if (!constCanvas) return;

    const ctx = constCanvas.getContext('2d');
    if (!ctx) return;

    let width = 0;
    let height = 0;
    let dpr = 1;
    let particles = [];
    const particleCount = 42;
    const maxDistance = 120;
    let mouse = { x: null, y: null, targetX: null, targetY: null, maxDist: 140 };
    let animationId = null;

    function isDark() {
        return document.documentElement.classList.contains('dark') ||
               document.documentElement.getAttribute('data-theme') === 'dark';
    }

    function resize() {
        dpr = Math.min(window.devicePixelRatio || 1, 2);
        width = window.innerWidth;
        height = window.innerHeight;
        constCanvas.width = width * dpr;
        constCanvas.height = height * dpr;
        ctx.setTransform(1, 0, 0, 1, 0, 0);
        ctx.scale(dpr, dpr);
    }

    function initParticles() {
        particles = [];
        for (let i = 0; i < particleCount; i++) {
            particles.push({
                x: Math.random() * width,
                y: Math.random() * height,
                vx: (Math.random() - 0.5) * 0.4,
                vy: (Math.random() - 0.5) * 0.4,
                radius: Math.random() * 2 + 1.2,
                colorType: Math.random() > 0.4 ? 'emerald' : 'cyan',
                pulse: Math.random() * Math.PI * 2
            });
        }
    }

    function render() {
        ctx.clearRect(0, 0, width, height);
        const dark = isDark();

        const colorEmerald = dark ? 'rgba(52, 211, 153,' : 'rgba(5, 150, 105,';
        const colorCyan = dark ? 'rgba(56, 189, 248,' : 'rgba(2, 132, 199,';
        const lineBase = dark ? '52, 211, 153' : '16, 185, 129';

        // Update mouse position smoothly
        if (mouse.targetX !== null && mouse.targetY !== null) {
            if (mouse.x === null) {
                mouse.x = mouse.targetX;
                mouse.y = mouse.targetY;
            } else {
                mouse.x += (mouse.targetX - mouse.x) * 0.12;
                mouse.y += (mouse.targetY - mouse.y) * 0.12;
            }

            if (ambientCanvas) {
                ambientCanvas.style.setProperty('--cursor-x', mouse.x.toFixed(1) + 'px');
                ambientCanvas.style.setProperty('--cursor-y', mouse.y.toFixed(1) + 'px');
            }
        }

        // Render particles and connections
        for (let i = 0; i < particles.length; i++) {
            const p = particles[i];
            p.x += p.vx;
            p.y += p.vy;
            p.pulse += 0.025;

            // Screen wrap
            if (p.x < -10) p.x = width + 10;
            else if (p.x > width + 10) p.x = -10;
            if (p.y < -10) p.y = height + 10;
            else if (p.y > height + 10) p.y = -10;

            const baseAlpha = dark ? 0.65 : 0.45;
            const dynamicAlpha = Math.max(0.2, baseAlpha + Math.sin(p.pulse) * 0.25);
            const pColor = (p.colorType === 'emerald' ? colorEmerald : colorCyan) + dynamicAlpha + ')';

            ctx.beginPath();
            ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
            ctx.fillStyle = pColor;
            if (dark) {
                ctx.shadowColor = p.colorType === 'emerald' ? '#10B981' : '#06B6D4';
                ctx.shadowBlur = 8;
            } else {
                ctx.shadowBlur = 0;
            }
            ctx.fill();

            // Connect lines between nearby particles
            for (let j = i + 1; j < particles.length; j++) {
                const p2 = particles[j];
                const dx = p.x - p2.x;
                const dy = p.y - p2.y;
                const dist = Math.sqrt(dx * dx + dy * dy);

                if (dist < maxDistance) {
                    const lineAlpha = (1 - dist / maxDistance) * (dark ? 0.22 : 0.12);
                    ctx.beginPath();
                    ctx.moveTo(p.x, p.y);
                    ctx.lineTo(p2.x, p2.y);
                    ctx.strokeStyle = `rgba(${lineBase}, ${lineAlpha})`;
                    ctx.lineWidth = 0.8;
                    ctx.stroke();
                }
            }

            // Connect to mouse cursor
            if (mouse.x !== null && mouse.y !== null) {
                const mdx = p.x - mouse.x;
                const mdy = p.y - mouse.y;
                const mdist = Math.sqrt(mdx * mdx + mdy * mdy);

                if (mdist < mouse.maxDist) {
                    const mLineAlpha = (1 - mdist / mouse.maxDist) * (dark ? 0.35 : 0.2);
                    ctx.beginPath();
                    ctx.moveTo(p.x, p.y);
                    ctx.lineTo(mouse.x, mouse.y);
                    ctx.strokeStyle = `rgba(${lineBase}, ${mLineAlpha})`;
                    ctx.lineWidth = 1;
                    ctx.stroke();
                }
            }
        }

        animationId = requestAnimationFrame(render);
    }

    window.addEventListener('resize', () => {
        resize();
        initParticles();
    });

    window.addEventListener('mousemove', (e) => {
        mouse.targetX = e.clientX;
        mouse.targetY = e.clientY;
    }, { passive: true });

    window.addEventListener('mouseleave', () => {
        mouse.targetX = null;
        mouse.targetY = null;
        mouse.x = null;
        mouse.y = null;
    });

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            if (animationId) cancelAnimationFrame(animationId);
        } else {
            animationId = requestAnimationFrame(render);
        }
    });

    resize();
    initParticles();
    animationId = requestAnimationFrame(render);
})();
</script>

    {{-- ─── CYBER MODAL: AKSES DITUTUP / WAKTU HABIS (POPUP DENGAN BACKDROP BLUR) ─── --}}
    <div id="soal-access-lock-modal" class="soal-lock-overlay" style="display: none;" aria-hidden="true">
        <div class="soal-lock-card">
            {{-- Animated Pulse Halo & Shield / Lock Icon --}}
            <div class="soal-lock-icon-wrap">
                <div class="soal-lock-icon-ping"></div>
                <div class="soal-lock-icon-box">
                    <svg id="soal-lock-svg-icon" width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
            </div>

            {{-- Status Chip --}}
            <div class="soal-lock-chip">
                <span class="soal-lock-chip-dot"></span>
                <span id="soal-lock-status-badge">AKSES SOAL DITUTUP</span>
            </div>

            {{-- Title & Message --}}
            <h3 id="soal-lock-title" class="soal-lock-title">Waktu Akses Soal Telah Berakhir</h3>
            <p id="soal-lock-desc" class="soal-lock-desc">
                Batas waktu pengerjaan atau akses dokumen soal ini telah berakhir sesuai jadwal pelaksanaan laboratorium. Lembar tugas telah ditutup secara otomatis.
            </p>

            {{-- Meta Information Table --}}
            <div class="soal-lock-meta">
                <div class="soal-lock-meta-row">
                    <span class="soal-lock-meta-label">Mata Kuliah / Soal:</span>
                    <span class="soal-lock-meta-val" title="{{ $soal->title }}">{{ $soal->title }}</span>
                </div>
                <div class="soal-lock-meta-row">
                    <span class="soal-lock-meta-label">Kode Unik:</span>
                    <span class="soal-lock-meta-val" style="font-family:ui-monospace,monospace;">{{ $soal->kode_unik }}</span>
                </div>
                <div class="soal-lock-meta-row" id="soal-lock-time-row">
                    <span class="soal-lock-meta-label">Batas Waktu:</span>
                    <span id="soal-lock-time-val" class="soal-lock-meta-val text-amber">
                        {{ $soal->active_until ? $soal->active_until->format('d M Y, H:i') : ($soal->deadline_at ? $soal->deadline_at->format('d M Y, H:i') : 'Selesai') }}
                    </span>
                </div>
            </div>

            {{-- Notice box --}}
            <div class="soal-lock-notice">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span id="soal-lock-notice-text">
                    Jika Anda merasa ini adalah kekeliruan atau membutuhkan perpanjangan waktu, silakan segera konfirmasi kepada pengawas atau asisten laboratorium yang bertugas.
                </span>
            </div>

            {{-- Action Buttons --}}
            <div class="soal-lock-actions">
                <a href="{{ url('/') }}" class="soal-lock-btn-primary">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Kembali ke Beranda</span>
                </a>
                <button type="button" onclick="checkSoalStatusNow(true)" class="soal-lock-btn-secondary" id="btn-recheck-soal-status">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>Cek Ulang Status</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ─── UNIFIED CYBER LOADING POPUP COMPONENT ─── --}}
    @include('components.cyber-loading')

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>