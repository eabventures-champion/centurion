@props(['href' => null])

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'inline-flex items-center px-8 py-3 bg-rose-600 border border-rose-500/50 rounded-xl font-black text-[10px] text-white uppercase tracking-[2px] hover:bg-rose-500 hover:shadow-[0_0_20_rgba(244,63,94,0.4)] focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition-all duration-300']) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-8 py-3 bg-rose-600 border border-rose-500/50 rounded-xl font-black text-[10px] text-white uppercase tracking-[2px] hover:bg-rose-500 hover:shadow-[0_0_20px_rgba(244,63,94,0.4)] focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition-all duration-300']) }}>
        {{ $slot }}
    </button>
@endif