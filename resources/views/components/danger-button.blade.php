<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-red-600 rounded-xl font-bold text-sm text-white shadow-sm hover:bg-red-700 hover:shadow hover:-translate-y-0.5 active:translate-y-0 focus:outline-none focus:ring-4 focus:ring-red-100 disabled:opacity-50 transition-all duration-200 select-none']) }}>
    {{ $slot }}
</button>
