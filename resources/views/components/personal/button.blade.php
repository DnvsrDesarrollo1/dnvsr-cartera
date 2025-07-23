@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'submit' => false,
    'disabled' => false,
    'href' => null,
    'to' => null,
    'iconLeft' => null,
    'iconRight' => null,
    'iconCenter' => null,
])

@php
    $baseClasses =
        'inline-flex items-center justify-center font-medium rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition duration-200 ease-in-out disabled:opacity-50';

    $sizes = [
        'xs' => 'px-2.5 py-1.5 text-xs',
        'sm' => 'px-3 py-2 text-sm',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-3 text-base',
        'xl' => 'px-8 py-4 text-lg',
    ];

    $variants = [
        'primary' =>
            'bg-blue-500 text-white px-4 py-2 rounded-full transition duration-200 ease-in-out hover:bg-blue-700 active:bg-blue-900 focus:outline-none',
        'secondary' =>
            'bg-gray-500 text-white px-4 py-2 rounded-full transition duration-200 ease-in-out hover:bg-gray-700 active:bg-gray-900 focus:outline-none',
        'success' =>
            'bg-green-600 text-white px-4 py-2 rounded-full transition duration-200 ease-in-out hover:bg-green-700 active:bg-green-900 focus:outline-none',
        'danger' =>
            'bg-red-500 text-white px-4 py-2 rounded-full transition duration-200 ease-in-out hover:bg-red-700 active:bg-red-900 focus:outline-none',
        'warning' =>
            'bg-yellow-500 text-white px-4 py-2 rounded-full transition duration-200 ease-in-out hover:bg-yellow-700 active:bg-yellow-900 focus:outline-none',
        'info' =>
            'bg-cyan-500 text-white px-4 py-2 rounded-full transition duration-200 ease-in-out hover:bg-cyan-700 active:bg-cyan-900 focus:outline-none',
        'light' =>
            'bg-gray-100 text-gray-800 px-4 py-2 rounded-full transition duration-200 ease-in-out hover:bg-gray-200 active:bg-gray-300 focus:outline-none',
        'dark' =>
            'bg-teal-800 text-white px-4 py-2 rounded-full transition duration-200 ease-in-out hover:bg-teal-900 active:bg-teal-950 focus:outline-none',

        'outline-primary' =>
            'border border-blue-500 text-blue-500 bg-transparent px-4 py-2 rounded-full transition duration-200 ease-in-out hover:bg-blue-50 active:bg-blue-100 focus:outline-none',
        'outline-secondary' =>
            'border border-gray-500 text-gray-500 bg-transparent px-4 py-2 rounded-full transition duration-200 ease-in-out hover:bg-gray-50 active:bg-gray-100 focus:outline-none',
        'outline-success' =>
            'border border-green-600 text-green-600 bg-transparent px-4 py-2 rounded-full transition duration-200 ease-in-out hover:bg-green-50 active:bg-green-100 focus:outline-none',
        'outline-danger' =>
            'border border-red-500 text-red-500 bg-transparent px-4 py-2 rounded-full transition duration-200 ease-in-out hover:bg-red-50 active:bg-red-100 focus:outline-none',
        'outline-warning' =>
            'border border-yellow-500 text-yellow-500 bg-transparent px-4 py-2 rounded-full transition duration-200 ease-in-out hover:bg-yellow-50 active:bg-yellow-100 focus:outline-none',
        'outline-info' =>
            'border border-cyan-500 text-cyan-500 bg-transparent px-4 py-2 rounded-full transition duration-200 ease-in-out hover:bg-cyan-50 active:bg-cyan-100 focus:outline-none',
        'outline-light' =>
            'border border-gray-300 text-gray-600 bg-transparent px-4 py-2 rounded-full transition duration-200 ease-in-out hover:bg-gray-50 active:bg-gray-100 focus:outline-none',
        'outline-dark' =>
            'border border-teal-800 text-teal-800 bg-transparent px-4 py-2 rounded-full transition duration-200 ease-in-out hover:bg-teal-50 active:bg-teal-100 focus:outline-none',

        'link' =>
            'bg-sky-100 text-purple-500 px-4 py-2 rounded-full transition duration-200 ease-in-out hover:underline focus:outline-none',
    ];

    $classes =
        $baseClasses . ' ' . ($sizes[$size] ?? $sizes['md']) . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }} target="{{ $to ? '_blank' : '_self' }}"
        {{ $disabled ? 'disabled' : '' }}>
        @if ($iconLeft)
            <span class="mr-2">
                <i class="{{ $iconLeft }}"></i>
            </span>
            {{ $slot }}
        @endif
        @if ($iconCenter)
            <span class="mx-auto">
                <i class="{{ $iconCenter }}"></i>
            </span>
        @endif
        @if ($iconRight)
            {{ $slot }}
            <span class="ml-2">
                <i class="{{ $iconRight }}"></i>
            </span>
        @endif
    </a>
@else
    <button type="{{ $submit ? 'submit' : 'button' }}" {{ $attributes->merge(['class' => $classes]) }}
        {{ $disabled ? 'disabled' : '' }}>
        @if ($iconLeft)
            <span class="mr-2">
                <i class="{{ $iconLeft }}"></i>
            </span>
            {{ $slot }}
        @endif
        @if ($iconCenter)
            <span class="mx-auto flex-col">
                <i class="{{ $iconCenter }}"></i>
                <p>{{ $slot }}</p>
            </span>
            <br />
        @endif
        @if ($iconRight)
            {{ $slot }}
            <span class="ml-2">
                <i class="{{ $iconRight }}"></i>
            </span>
        @endif
    </button>
@endif
