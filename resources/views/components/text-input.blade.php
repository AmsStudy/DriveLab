@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full rounded-xl text-sm focus:outline-none transition-all duration-200', 'style' => 'background:#090E0B; border:1px solid #1A2820; color:#E5E7EB; padding:12px 16px; font-family:Inter,sans-serif;']) }}
    onfocus="this.style.borderColor='#10B981'; this.style.boxShadow='0 0 0 3px rgba(16,185,129,.15)';"
    onblur="this.style.borderColor='#1A2820'; this.style.boxShadow='none';">
