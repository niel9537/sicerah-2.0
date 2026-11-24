<style>
/* Reset & Base WebGIS Container */
.wg-container {
    color: #21191A;
    font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
}

/* Card Style Selaras Portal */
.wg-card {
    background: #ffffff;
    border: 1px solid rgba(226, 232, 240, 0.9);
    border-radius: 1.25rem;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(116,20,33, 0.05);
}

/* Form Filters */
.wg-filters {
    display: grid;
    grid-template-columns: repeat(1, minmax(0, 1fr));
    gap: 1rem;
}
@media (min-width: 640px) {
    .wg-filters {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
@media (min-width: 1024px) {
    .wg-filters {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }
}

.wg-filters label {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    font-size: 0.75rem;
    font-weight: 700;
    color: #741421;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.wg-filters select {
    padding: 0.65rem 0.85rem;
    border: 1px solid #cbd5e1;
    border-radius: 0.75rem;
    width: 100%;
    background: #ffffff;
    color: #0F172A;
    font-size: 0.8125rem;
    font-weight: 600;
    outline: none;
    transition: all 0.15s ease;
}
.wg-filters select:focus {
    border-color: #AD1D30;
    box-shadow: 0 0 0 3px rgba(185, 28, 28, 0.12);
}

/* Status Bar */
.wg-status {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.875rem;
    background: #FEF2F2;
    color: #861827;
    border: 1px solid #FEE2E2;
    border-radius: 0.75rem;
    font-size: 0.75rem;
    font-weight: 700;
    margin: 1rem 0 0.5rem;
}

/* Action Button */
.wg-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    background: linear-gradient(135deg, #861827 0%, #AD1D30 100%);
    color: #ffffff !important;
    padding: 0.6rem 1.1rem;
    border-radius: 0.75rem;
    border: 0;
    font-size: 0.75rem;
    font-weight: 800;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(185, 28, 28, 0.25);
    transition: all 0.2s ease;
    text-decoration: none;
}
.wg-button:hover {
    background: linear-gradient(135deg, #68121D 0%, #861827 100%);
    box-shadow: 0 4px 14px rgba(185, 28, 28, 0.35);
    transform: translateY(-1px);
}

.wg-button-gold {
    background: linear-gradient(135deg, #D4A017 0%, #FFDA65 100%);
    color: #741421 !important;
    box-shadow: 0 2px 8px rgba(212, 160, 23, 0.3);
}
.wg-button-gold:hover {
    background: linear-gradient(135deg, #B8860B 0%, #D4A017 100%);
    color: #ffffff !important;
    box-shadow: 0 4px 14px rgba(212, 160, 23, 0.45);
}

/* Map Canvas Container */
.wg-map {
    height: clamp(480px, 68vh, 760px);
    border-radius: 1rem;
    z-index: 0;
    isolation: isolate;
    border: 1px solid #e2e8f0;
    margin-top: 1rem;
    background: #e2e8f0;
}

/* Legend Box */
.wg-legend {
    display: flex;
    gap: 0.85rem;
    flex-wrap: wrap;
    margin: 1.25rem 0 0.5rem;
    padding: 0.75rem 1rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: #334155;
}
.wg-legend span {
    display: inline-flex;
    align-items: center;
}
.wg-chip {
    display: inline-block;
    width: 14px;
    height: 14px;
    margin-right: 6px;
    border-radius: 4px;
    border: 1px solid rgba(0, 0, 0, 0.1);
}

/* Leaflet Customization */
.leaflet-container {
    font-family: 'Plus Jakarta Sans', system-ui, sans-serif !important;
    border-radius: 1rem;
}
.leaflet-popup-content-wrapper {
    border-radius: 1rem !important;
    box-shadow: 0 16px 40px -12px rgba(116,20,33, 0.35) !important;
    border: 1px solid rgba(226, 232, 240, 0.9) !important;
}
.leaflet-popup-content {
    margin: 0.875rem 1rem !important;
    line-height: 1.4 !important;
}
.leaflet-popup-content strong {
    color: #741421;
}
.wg-surface-controls{display:flex;flex-wrap:wrap;align-items:center;gap:1rem;margin-bottom:1rem}.wg-surface-controls label{font-size:13px;font-weight:700;color:#741421}.wg-surface-controls select{display:block;margin-top:6px;border:1px solid #cbd5e1;border-radius:10px;padding:10px;max-width:100%;background:#fff}.wg-overlay-control{display:flex;align-items:center;gap:8px}.wg-dsm-info{padding:14px;background:#f0f9ff;border:1px solid #bae6fd;border-radius:12px;margin-bottom:16px;font-size:12px;color:#164e63}.wg-dsm-info p{margin:6px 0}.wg-dsm-info img{max-width:100%;height:auto;background:#fff;border-radius:6px}.wg-dsm-info a{text-decoration:underline}.wg-basemap-status{font-size:12px;color:#9f1239}.wg-container [hidden]{display:none!important}
.wg-facilities{border:1px solid #e2e8f0;padding:16px;border-radius:12px;margin-bottom:16px;font-size:12px}.wg-facility-filters{display:flex;flex-wrap:wrap;align-items:center;gap:14px;margin:12px 0}.wg-facility-filters label{display:flex;align-items:center;gap:6px;font-weight:700}.wg-facilities p{margin:8px 0;color:#475569}.wg-facilities a{text-decoration:underline}.wg-facility-list{display:flex;gap:6px;flex-wrap:wrap;max-height:130px;overflow:auto;margin-top:10px}.wg-facility-list button{background:#f8fafc;border:1px solid #e2e8f0;border-left:4px solid;padding:7px 10px;border-radius:6px;text-align:left}.wg-button:disabled{opacity:.5;cursor:default}
</style>
