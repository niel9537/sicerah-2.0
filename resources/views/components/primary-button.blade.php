<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-amber-600 via-amber-500 to-yellow-500 rounded-xl font-extrabold text-sm text-slate-950 shadow-[0_4px_16px_0_rgba(217,119,6,0.35)] hover:shadow-[0_6px_22px_rgba(217,119,6,0.45)] hover:brightness-105 hover:-translate-y-0.5 active:translate-y-0 focus:outline-none focus:ring-4 focus:ring-amber-200 disabled:opacity-50 transition-all duration-200 select-none']) }}>
    {{ $slot }}
</button>
