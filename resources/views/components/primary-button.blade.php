<button {{ $attributes->merge(['type' => 'submit']) }}
    style="display:inline-flex;align-items:center;padding:10px 20px;background:linear-gradient(135deg,#10B981,#0D9488);border:none;border-radius:10px;font-weight:700;font-size:13px;color:#fff;cursor:pointer;transition:opacity .2s,box-shadow .2s;box-shadow:0 0 16px rgba(16,185,129,.25);font-family:Inter,sans-serif;"
    onmouseover="this.style.opacity='.85'; this.style.boxShadow='0 0 24px rgba(16,185,129,.4)';"
    onmouseout="this.style.opacity='1'; this.style.boxShadow='0 0 16px rgba(16,185,129,.25)';">
    {{ $slot }}
</button>
