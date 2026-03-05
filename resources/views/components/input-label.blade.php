@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-black text-[9px] uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-1.5']) }}>
    {{ $value ?? $slot }}
</label>