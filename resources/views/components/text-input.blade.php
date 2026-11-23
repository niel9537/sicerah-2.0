@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full rounded-xl border-slate-200 shadow-sm focus:border-amber-500 focus:ring-4 focus:ring-amber-100 placeholder:text-slate-400 text-sm transition-all']) }}>
