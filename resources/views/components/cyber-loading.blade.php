{{-- ─── UNIFIED CYBER LOADING POPUP COMPONENT (CLEAN, SLEEK & MODERN) ─── --}}
<style>
    @keyframes cyberSpinRingCW {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    @keyframes cyberSpinRingCCW {
        0% { transform: rotate(360deg); }
        100% { transform: rotate(0deg); }
    }
    @keyframes cyberCoreBreathing {
        0% { transform: scale(0.82); opacity: 0.75; box-shadow: 0 0 10px #10B981; }
        100% { transform: scale(1.15); opacity: 1; box-shadow: 0 0 24px #34D399, 0 0 40px rgba(16,185,129,0.85); }
    }
    @keyframes cyberCardGlowPulse {
        0%, 100% { box-shadow: 0 25px 60px rgba(0,0,0,0.85), 0 0 25px rgba(16,185,129,0.18), inset 0 0 20px rgba(16,185,129,0.04); }
        50% { box-shadow: 0 25px 60px rgba(0,0,0,0.9), 0 0 45px rgba(52,211,153,0.32), inset 0 0 30px rgba(16,185,129,0.08); }
    }
    @keyframes cyberBarScan {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    .cyber-loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100vw;
        height: 100vh;
        margin: 0;
        padding: 16px;
        z-index: 999999;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(4, 8, 6, 0.82);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        transition: opacity 0.25s ease;
    }

    .cyber-loading-card {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin: auto;
        max-width: 380px;
        width: 100%;
        background: rgba(12, 20, 16, 0.95);
        border: 1px solid rgba(16, 185, 129, 0.35);
        border-radius: 24px;
        padding: 34px 28px 30px 28px;
        text-align: center;
        overflow: hidden;
        animation: cyberCardGlowPulse 3s ease-in-out infinite;
    }

    .cyber-loading-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent, rgba(52, 211, 153, 0.8), #10B981, transparent);
    }

    .cyber-loading-ambient-glow {
        position: absolute;
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: rgba(16, 185, 129, 0.18);
        filter: blur(34px);
        pointer-events: none;
        top: 30px;
    }

    .pure-cyber-loader {
        position: relative;
        width: 84px;
        height: 84px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 22px;
    }
    .pure-cyber-loader .spinner-ring-1 {
        position: absolute;
        width: 84px;
        height: 84px;
        border-radius: 50%;
        border: 3px solid rgba(16, 185, 129, 0.15);
        border-top: 3px solid #10B981;
        border-right: 3px solid #34D399;
        box-shadow: 0 0 20px rgba(16, 185, 129, 0.55), inset 0 0 10px rgba(16, 185, 129, 0.2);
        animation: cyberSpinRingCW 1.3s cubic-bezier(0.4, 0, 0.2, 1) infinite;
    }
    .pure-cyber-loader .spinner-dot {
        position: absolute;
        width: 84px;
        height: 84px;
        border-radius: 50%;
        animation: cyberSpinRingCW 1.3s cubic-bezier(0.4, 0, 0.2, 1) infinite;
    }
    .pure-cyber-loader .spinner-dot::before {
        content: '';
        position: absolute;
        top: -4px;
        left: calc(50% - 4px);
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #FFFFFF;
        box-shadow: 0 0 14px #34D399, 0 0 6px #10B981;
    }
    .pure-cyber-loader .spinner-ring-2 {
        position: absolute;
        width: 62px;
        height: 62px;
        border-radius: 50%;
        border: 2.5px dashed #0D9488;
        opacity: 0.85;
        animation: cyberSpinRingCCW 2.4s linear infinite;
    }
    .pure-cyber-loader .spinner-ring-3 {
        position: absolute;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        border: 2px solid transparent;
        border-top: 2px solid #6EE7B7;
        border-left: 2px solid #34D399;
        box-shadow: 0 0 12px rgba(52, 211, 153, 0.6);
        animation: cyberSpinRingCW 0.85s ease-in-out infinite;
    }
    .pure-cyber-loader .spinner-core {
        position: relative;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: radial-gradient(circle, #34D399 0%, #10B981 60%, #0D9488 100%);
        box-shadow: 0 0 16px #10B981, 0 0 28px rgba(16, 185, 129, 0.8);
        animation: cyberCoreBreathing 1.2s ease-in-out infinite alternate;
    }

    .cyber-loading-scan-line {
        position: relative;
        width: 100%;
        height: 2px;
        background: rgba(16, 185, 129, 0.12);
        border-radius: 999px;
        margin-top: 20px;
        overflow: hidden;
    }
    .cyber-loading-scan-line::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 50%;
        background: linear-gradient(90deg, transparent, #10B981, #34D399, transparent);
        animation: cyberBarScan 1.6s ease-in-out infinite;
    }
</style>

<div id="cyberLoadingOverlay" class="cyber-loading-overlay" aria-live="assertive" role="alert">
    <div class="cyber-loading-card" id="cyberLoadingBox">
        <div class="cyber-loading-ambient-glow"></div>
        
        <div class="pure-cyber-loader">
            <div class="spinner-ring-1"></div>
            <div class="spinner-dot"></div>
            <div class="spinner-ring-2"></div>
            <div class="spinner-ring-3"></div>
            <div class="spinner-core"></div>
        </div>

        <p id="cyberLoadingTitle" style="font-family:'Orbitron',sans-serif;font-size:13.5px;font-weight:800;color:#FFFFFF;letter-spacing:0.08em;line-height:1.3;text-transform:uppercase;">MEMPROSES...</p>
        <p id="cyberLoadingSub" style="font-family:'Inter',sans-serif;font-size:12px;color:#9CA3AF;margin-top:6px;line-height:1.5;">Mohon tunggu sebentar, sistem sedang memproses data.</p>
        
        <div class="cyber-loading-scan-line"></div>
    </div>
</div>

<script>
    window.showLoading = function(title = 'MEMPROSES...', subtitle = 'Mohon tunggu sebentar, sistem sedang memproses data.') {
        const overlay = document.getElementById('cyberLoadingOverlay');
        const titleEl = document.getElementById('cyberLoadingTitle');
        const subEl = document.getElementById('cyberLoadingSub');
        if (titleEl) titleEl.textContent = title;
        if (subEl) subEl.textContent = subtitle;
        if (overlay) {
            overlay.style.display = 'flex';
            overlay.style.opacity = '1';
            overlay.style.pointerEvents = 'auto';
        }
    };

    window.hideLoading = function() {
        const overlay = document.getElementById('cyberLoadingOverlay');
        if (overlay) {
            overlay.style.display = 'none';
            overlay.style.opacity = '0';
            overlay.style.pointerEvents = 'none';
        }
    };

    window.addEventListener('pageshow', function() {
        window.hideLoading();
    });
</script>
