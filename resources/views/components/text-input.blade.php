@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-white dark:bg-slate-900/50 border-slate-200 dark:border-white/10 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 transition-all duration-300 py-2.5 px-4 text-sm']) }}>