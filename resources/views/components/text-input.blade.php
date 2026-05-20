@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full rounded-xl text-sm focus:outline-none transition-all duration-200', 'style' => 'background:#0D0D14; border:1px solid #1E1E2E; color:#E5E7EB; padding:12px 16px; font-family:Inter,sans-serif;']) }}
    onfocus="this.style.borderColor='#F72585'; this.style.boxShadow='0 0 0 3px rgba(247,37,133,.15)';"
    onblur="this.style.borderColor='#1E1E2E'; this.style.boxShadow='none';">
