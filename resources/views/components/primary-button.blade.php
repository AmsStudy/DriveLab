<button {{ $attributes->merge(['type' => 'submit']) }}
    style="display:inline-flex;align-items:center;padding:10px 20px;background:linear-gradient(135deg,#F72585,#7209B7);border:none;border-radius:10px;font-weight:700;font-size:13px;color:#fff;cursor:pointer;transition:opacity .2s,box-shadow .2s;box-shadow:0 0 16px rgba(247,37,133,.25);font-family:Inter,sans-serif;"
    onmouseover="this.style.opacity='.85'; this.style.boxShadow='0 0 24px rgba(247,37,133,.4)';"
    onmouseout="this.style.opacity='1'; this.style.boxShadow='0 0 16px rgba(247,37,133,.25)';">
    {{ $slot }}
</button>
