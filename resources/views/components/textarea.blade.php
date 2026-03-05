@props(['disabled' => false])

<textarea @disabled($disabled) {{ $attributes->merge(['class' => 'bg-slate-900/50 border-white/10 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-white placeholder-slate-500 transition-all duration-300 py-3 px-4']) }}>{{ $slot }}</textarea>