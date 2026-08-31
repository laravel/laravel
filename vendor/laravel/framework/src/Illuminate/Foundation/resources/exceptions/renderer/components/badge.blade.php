@props(['type' => 'default', 'variant' => 'soft'])

@php
$baseClasses = 'inline-flex w-fit shrink-0 items-center justify-center gap-1 font-mono leading-3 uppercase transition-colors dark:border [&_svg]:size-2.5 h-6 min-w-5 rounded-md px-1.5 text-xs/none';

$types = [
    'default' => [
        'soft' => 'bg-black/8 text-neutral-900 dark:border-neutral-700 dark:bg-white/10 dark:text-neutral-100',
        'solid' => 'bg-neutral-600 text-neutral-100 dark:border-neutral-500 dark:bg-neutral-600',
    ],
    'success' => [
        'soft' => 'bg-emerald-200 text-emerald-900 dark:border-emerald-600 dark:bg-emerald-900/70 dark:text-emerald-400',
        'solid' => 'bg-emerald-600 dark:border-emerald-500 dark:bg-emerald-600',
    ],
    'primary' => [
        'soft' => 'bg-blue-100 text-blue-900 dark:border-blue-800 dark:bg-blue-950 dark:text-blue-300',
        'solid' => 'bg-blue-700 dark:border-blue-600 dark:bg-blue-700',
    ],
    'error' => [
        'soft' => 'bg-rose-200 text-rose-900 dark:border-rose-900 dark:bg-rose-950 dark:text-rose-100 dark:[&_svg]:!text-white',
        'solid' => 'bg-rose-600 dark:border-rose-500 dark:bg-rose-600',
    ],
    'alert' => [
        'soft' => 'bg-amber-200 text-amber-900 dark:border-amber-800 dark:bg-amber-950 dark:text-amber-300',
        'solid' => 'bg-amber-600 dark:border-amber-500 dark:bg-amber-600',
    ],
    'white' => [
        'soft' => 'bg-white text-neutral-900 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100',
        'solid' => 'bg-black/10 text-neutral-900 dark:text-neutral-900 dark:bg-white',
    ],
];

$variants = [
    'soft' => '',
    'solid' => 'text-white dark:text-white [&_svg]:!text-white',
];

$typeClasses = $types[$type][$variant] ?? $types['default']['soft'];
$variantClasses = $variants[$variant] ?? $variants['soft'];

$classes = implode(' ', [$baseClasses, $typeClasses, $variantClasses]);

@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>
