@props(['variant' => 'purple'])

@php
    $logoFile = match($variant) {
        'white' => 'Logo blanco.png',
        'black' => 'Logo negro.png',
        default => 'Logo Morado.png',
    };
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-2']) }}>
    <img src="{{ asset('images/' . $logoFile) }}" 
         alt="Show Future" 
         class="h-9 w-auto object-contain transition-all duration-200 hover:scale-105" />
    <span class="font-extrabold tracking-wider text-xl uppercase font-sans {{ $variant === 'white' ? 'text-white' : 'text-[#511560]' }}">
        Show Future
    </span>
</div>
