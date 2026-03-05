@props(['href' => null])

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'inline-flex items-center px-6 py-2.5 bg-indigo-600 border border-indigo-500/50 rounded-xl font-black text-[10px] text-white uppercase tracking-[2px] hover:bg-indigo-500 hover:shadow-[0_0_20px_rgba(99,102,241,0.4)] focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-slate-900 transition-all duration-300']) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-6 py-2.5 bg-indigo-600 border border-indigo-500/50 rounded-xl font-black text-[10px] text-white uppercase tracking-[2px] hover:bg-indigo-500 hover:shadow-[0_0_20px_rgba(99,102,241,0.4)] focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-slate-900 transition-all duration-300']) }}>
        {{ $slot }}
    </button>
@endif