<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-white border border-slate-200/90 rounded-xl font-bold text-sm text-slate-700 shadow-sm hover:bg-slate-50 hover:border-slate-300 hover:text-slate-900 focus:outline-none focus:ring-4 focus:ring-slate-100 disabled:opacity-50 transition-all duration-200 select-none']) }}>
    {{ $slot }}
</button>
