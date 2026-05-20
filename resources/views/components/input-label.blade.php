@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-semibold mb-1.5', 'style' => 'color:#9CA3AF;']) }}>
    {{ $value ?? $slot }}
</label>
