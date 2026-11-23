<a href="https://www.bmkg.go.id/cuaca/prakiraan-cuaca/12.25.01.2008" target="_blank" rel="noopener noreferrer" class="navbar-weather"
   x-data="{ weather: null, async init() { try { const response = await fetch(@js(route('weather.nias-barat')), { headers: { Accept: 'application/json' }, signal: AbortSignal.timeout(12000) }); if (response.ok) this.weather = await response.json(); } catch (error) { this.weather = null; } } }"
   :title="weather ? 'Prakiraan ' + weather.location + ' • ' + weather.forecast_at + ' • Sumber: BMKG' : 'Lihat prakiraan Onolimbu, Lahomi, Nias Barat di BMKG'">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M8 16a4 4 0 1 1 1-7.87A5 5 0 1 1 17 16Z"/><path d="M8 19h8M10 22h4"/></svg>
    <span><strong>Nias Barat <span x-show="weather" x-cloak x-text="weather ? weather.temperature + '°C' : ''"></span></strong>
    <small x-text="weather ? weather.description + ' · BMKG' : 'Lihat cuaca · BMKG'">Lihat cuaca · BMKG</small></span>
</a>
@once
<style>
    .navbar-weather{display:inline-flex;align-items:center;gap:8px;flex-shrink:0;padding:6px 10px;border:1px solid #e2e8f0;border-radius:12px;background:#f8fafc;color:#475569;text-decoration:none;line-height:1.4}
    .navbar-weather strong{display:block;font-size:11px;color:#334155;white-space:nowrap}
    .navbar-weather small{display:block;font-size:10px;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .navbar-weather:hover{border-color:#d4a017;background:#fffbeb}
    .portal-nav-inner>.navbar-weather{margin-left:auto;align-self:center;margin-right:8px}
    @media(max-width:1023px){.portal-nav-inner>.navbar-weather{margin:8px 16px;align-self:flex-start}}
</style>
@endonce
