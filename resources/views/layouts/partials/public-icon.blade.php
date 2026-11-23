<svg class="portal-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
@switch($icon)
@case('home')<path d="m3 10 9-7 9 7v10H14v-6h-4v6H3z"/>@break
@case('structure')<rect x="9" y="2" width="6" height="5" rx="1"/><path d="M12 7v5M5 16v-4h14v4"/><rect x="2" y="16" width="6" height="5" rx="1"/><rect x="16" y="16" width="6" height="5" rx="1"/>@break
@case('chart')<path d="M3 21V12h4v9M10 21V7h4v14M17 21V2h4v19"/>@break
@case('map')<path d="m3 13 9 5 9-5M3 18l9 5 9-5M12 15s6-5 6-9a6 6 0 0 0-12 0c0 4 6 9 6 9Z"/><circle cx="12" cy="6" r="2"/>@break
@case('law')<path d="M12 3v18M7 21h10M3 6h18M5 6 1 15h8L5 6Zm14 0-4 9h8l-4-9Z"/>@break
@default<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M7 7h3v4H7zM14 7h3M14 11h3M7 15h10M7 18h10"/>
@endswitch
</svg>
