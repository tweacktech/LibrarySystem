@props([
    'type' => 'submit',
    'variant' => 'primary'
])

@php
$baseClasses = 'inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150';

$variantClasses = [
    'primary' => 'bg-gray-800 hover:bg-gray-700 active:bg-gray-900 focus:border-gray-900 focus:ring-gray-300',
    'secondary' => 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50 active:bg-gray-100 focus:border-gray-300 focus:ring-gray-200',
    'danger' => 'bg-red-600 hover:bg-red-500 active:bg-red-700 focus:border-red-700 focus:ring-red-200',
    'success' => 'bg-green-600 hover:bg-green-500 active:bg-green-700 focus:border-green-700 focus:ring-green-200',
];

$classes = $baseClasses . ' ' . $variantClasses[$variant];
@endphp

<button {{ $attributes->merge(['type' => $type, 'class' => $classes]) }}>
    {{ $slot }}
</button>
