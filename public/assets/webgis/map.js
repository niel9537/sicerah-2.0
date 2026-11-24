(() => {
'use strict';
const $ = id => document.getElementById('wg-' + id);
if (!$('map')) return;
if (!window.L) { $('status').textContent = 'Leaflet gagal dimuat. Periksa koneksi ke unpkg.com lalu muat ulang halaman.'; return; }
const map = L.map($('map'),{preferCanvas:true}).setView([1.0,97.45],10);
const attribution='ASTER GDEM &copy; NASA/METI | <a href="https://www.earthdata.nasa.gov/data/tools/gibs">NASA GIBS</a>';
const gibs='https://gibs.earthdata.nasa.gov/wmts/epsg3857/best/';
const basemaps={
 satellite:L.tileLayer('https://services.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',{maxNativeZoom:19,maxZoom:19,attribution:'Tiles &copy; Esri, Maxar, Earthstar Geographics, and the GIS User Community'}),
 dsm:L.tileLayer(gibs+'ASTER_GDEM_Color_Index/default/GoogleMapsCompatible_Level12/{z}/{y}/{x}.png',{maxNativeZoom:12,maxZoom:19,attribution}),
 relief:L.tileLayer(gibs+'ASTER_GDEM_Color_Shaded_Relief/default/GoogleMapsCompatible_Level12/{z}/{y}/{x}.jpeg',{maxNativeZoom:12,maxZoom:19,attribution}),
 street:L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19,attribution:'&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'})
};
let activeBasemap=basemaps.street;
activeBasemap.addTo(map);
Object.values(basemaps).forEach(base=>base.on('tileerror',()=>{
 if(base===activeBasemap) $('basemap-status').textContent='Peta dasar gagal dimuat. Pilih peta lain atau periksa koneksi internet.';
}));
$('basemap').addEventListener('change',()=>{
 map.removeLayer(activeBasemap);activeBasemap=basemaps[$('basemap').value];
 $('basemap-status').textContent='';activeBasemap.addTo(map);
 $('dsm-info').hidden=!['dsm','relief'].includes($('basemap').value);
 $('satellite-info').hidden=$('basemap').value!=='satellite';
 $('dsm-legend').hidden=$('basemap').value!=='dsm';
});
L.control.scale({imperial:false}).addTo(map);
$('detail').addEventListener('click',()=>{
 map.setView(map.getCenter(),18);
});
map.createPane('facilities');map.getPane('facilities').style.zIndex=450;
let payload, layer, controller, sequence=0;
function option(value, text) { const o=document.createElement('option');o.value=value;o.textContent=text;return o; }
function villages() {
 const selected=$('desa').value; $('desa').replaceChildren(option('','Semua desa'));
 const codes=new Set(payload.geojson.features.map(f=>f.properties.kode_desa));
 payload.desa.filter(d=>codes.has(d.kode_desa)&&(!$('kec').value||d.kode_kecamatan===$('kec').value)).sort((a,b)=>a.nama_desa.localeCompare(b.nama_desa)).forEach(d=>$('desa').append(option(d.kode_desa,`${d.nama_desa} — ${d.nama_kecamatan}`)));
 if([...$('desa').options].some(o=>o.value===selected)) $('desa').value=selected;
}
function render() {
 if(!payload) return;
 const master=Object.fromEntries(payload.desa.map(d=>[d.kode_desa,d]));
 const features=payload.geojson.features.filter(f=>{const code=f.properties.kode_desa,d=master[code]; return d&&(!$('kec').value||d.kode_kecamatan===$('kec').value)&&(!$('desa').value||code===$('desa').value);});
 function number(code) { const v=payload.values[code];return v===null||v===undefined?null:Number(v); }
 function color(v){if(v===null)return '#cbd5e1';let i=payload.theme.breaks.findIndex(b=>v<=b);return payload.theme.colors[i<0?payload.theme.colors.length-1:i];}
 if(layer) map.removeLayer(layer);
 layer=L.geoJSON({type:'FeatureCollection',features},{style:f=>({fillColor:color(number(f.properties.kode_desa)),fillOpacity:$('thematic').checked?.65:0,color:'#64748b',weight:1.3}),onEachFeature:(f,l)=>{
  const code=f.properties.kode_desa,d=master[code],v=number(code),box=document.createElement('div');
  const title=document.createElement('strong');title.textContent=d.nama_desa;box.append(title);
  [d.nama_kecamatan,code,`${payload.theme.name} • ${payload.year}`,v===null?'Belum ada data':`${v.toLocaleString('id-ID',{maximumFractionDigits:4})} ${payload.theme.unit}`].forEach(text=>{const p=document.createElement('div');p.textContent=text;box.append(p);});
  const education=payload.education?.[d.kode_kecamatan];
  const heading=document.createElement('strong');heading.textContent='Pendidikan Kecamatan '+d.nama_kecamatan;box.append(heading);
  if(education) {
   const percent=education.schools ? (100*education.synced/education.schools).toLocaleString('id-ID',{maximumFractionDigits:1})+'%' : 'Tidak tersedia';
   [`Sekolah: ${education.schools}`,`Peserta didik (PD): ${education.students}`,`Guru dan tenaga kependidikan (GTK): ${education.teachers}`,`Sudah sinkronisasi: ${education.synced}`,`Belum sinkronisasi: ${education.unsynced}`,`Sinkronisasi: ${percent}`,`Sumber: ${education.source}`,`Diperbarui: ${education.updated_at} (waktu aplikasi)`].forEach(text=>{const line=document.createElement('div');line.textContent=text;box.append(line);});
  } else { const line=document.createElement('div');line.textContent='Belum ada data pendidikan.';box.append(line); }
  l.bindPopup(box); const tooltip=document.createElement('span');tooltip.textContent=d.nama_desa;l.bindTooltip(tooltip);
  l.on('mouseover',()=>l.setStyle({weight:3,color:'#124e55'}));l.on('mouseout',()=>layer.resetStyle(l));
 }}).addTo(map);
 if(layer.getBounds().isValid()) map.fitBounds(layer.getBounds(),{padding:[20,20],maxZoom:14});
 const filled=features.filter(f=>number(f.properties.kode_desa)!==null).length;
 $('status').textContent=`${payload.theme.name} • ${payload.year} • ${filled} dari ${features.length} ${payload.education_mode?'kecamatan':'desa'} memiliki data`;
 $('legend').replaceChildren();$('legend').hidden=!$('thematic').checked;
 [...payload.theme.colors,'#cbd5e1'].forEach((c,i)=>{const item=document.createElement('span'),chip=document.createElement('span');chip.className='wg-chip';chip.style.background=c;item.append(chip);const b=payload.theme.breaks;item.append(document.createTextNode(i===payload.theme.colors.length?'Belum ada data':i===0?`≤ ${b[0]}`:i===b.length?`> ${b[i-1]}`:`> ${b[i-1]} – ${b[i]}`));$('legend').append(item);});
 $('source').textContent=`Satuan: ${payload.theme.unit}. Sumber: ${payload.theme.source||'Belum diisi'}. ${payload.theme.description||''}`;
}
async function load(reset=false) {
 const current=++sequence;
 if(controller)controller.abort(); controller=new AbortController();const active=controller;
 const timeout=setTimeout(()=>active.abort(),20000);
 $('status').textContent='Memuat data…';$('retry').hidden=true;
 $('year').disabled=true;$('kec').disabled=true;$('desa').disabled=true;
 if(layer){map.removeLayer(layer);layer=null;}payload=null;$('legend').replaceChildren();$('legend').hidden=!$('thematic').checked;$('source').textContent='';
 try {
  const url=new URL($('theme').value);if(!reset&&$('year').value&& !payload?.education_mode && /^\d{4}$/.test($('year').value))url.searchParams.set('year',$('year').value);
  const res=await fetch(url,{signal:active.signal,headers:{Accept:'application/json'},credentials:'same-origin'});
  if(!res.ok)throw new Error(`Server mengembalikan HTTP ${res.status}`);
  const data=await res.json();if(current!==sequence)return;payload=data;
  $('year').replaceChildren();const years=payload.years.length?payload.years:[payload.year];years.forEach(y=>$('year').append(option(y,y)));$('year').value=String(payload.year);
  const kec=$('kec').value;$('kec').replaceChildren(option('','Semua kecamatan'));const codes=new Set(payload.geojson.features.map(f=>f.properties.kode_desa));const districts=new Map(payload.desa.filter(d=>codes.has(d.kode_desa)).map(d=>[d.kode_kecamatan,d.nama_kecamatan]));[...districts].sort((a,b)=>a[1].localeCompare(b[1])).forEach(([code,name])=>$('kec').append(option(code,name)));if([...$('kec').options].some(o=>o.value===kec))$('kec').value=kec;
  $('desa').closest('label').hidden=!!payload.education_mode;villages();render();
 } catch(e) { if(current!==sequence)return;$('status').textContent=(e.name==='AbortError'?'Permintaan melewati 20 detik.':e.message)+'. Klik Coba lagi.';$('retry').hidden=false; }
 finally {clearTimeout(timeout);if(current===sequence){$('year').disabled=!payload||!!payload.education_mode;$('kec').disabled=!payload;$('desa').disabled=!payload||!!payload.education_mode;}}
}
$('thematic').addEventListener('change',()=>{if(layer) layer.setStyle({fillOpacity:$('thematic').checked?.65:0});$('legend').hidden=!$('thematic').checked;});
$('theme').addEventListener('change',()=>{ $('kec').value='';$('desa').value='';load(true); });
$('year').addEventListener('change',()=>load());$('kec').addEventListener('change',()=>{$('desa').value='';villages();render();});$('desa').addEventListener('change',render);$('retry').addEventListener('click',()=>load(true));load(true);

const facilityColors={government:'#7c3aed',school:'#2563eb',health:'#dc2626',unclassified:'#64748b'};
const facilityLabels={government:'Pemerintah',school:'Sekolah',health:'Kesehatan',unclassified:'Bangunan - fungsi belum diketahui'};
let facilityData,facilityLayer,facilityBusy=false;
function renderFacilities(){
 if(!facilityData)return;
 if(facilityLayer)map.removeLayer(facilityLayer);
 const selected=new Set([...$('facilities').querySelectorAll('input:checked')].map(i=>i.value));
 const features=facilityData.features.filter(f=>selected.has(f.properties.category));
 facilityLayer=L.geoJSON({type:'FeatureCollection',features},{pane:'facilities',
  style:f=>({color:facilityColors[f.properties.category],weight:2,fillOpacity:.35}),
  pointToLayer:(f,latlng)=>L.circleMarker(latlng,{pane:'facilities',radius:7,color:'#fff',weight:2,fillColor:facilityColors[f.properties.category],fillOpacity:1}),
  onEachFeature:(f,l)=>{
   const p=f.properties;
   l.bindPopup(()=>{
    const box=document.createElement('div'),title=document.createElement('strong');title.textContent=p.name;box.append(title);
    [facilityLabels[p.category],p.geometry_label].forEach(text=>{const line=document.createElement('div');line.textContent=text;box.append(line);});
    const link=document.createElement('a');link.href='https://docs.overturemaps.org/attribution/';link.target='_blank';link.rel='noopener noreferrer';link.textContent='Sumber Overture Maps';box.append(link);return box;
   });
   if(p.category!=='unclassified')l.bindTooltip(()=>{const text=document.createElement('span');text.textContent=p.name;return text;});
  }
 }).addTo(map);
 $('facility-list').replaceChildren();
 facilityLayer.eachLayer(l=>{if(l.feature.properties.category==='unclassified')return;const button=document.createElement('button');button.type='button';button.textContent=facilityLabels[l.feature.properties.category]+' - '+l.feature.properties.name;button.style.borderLeftColor=facilityColors[l.feature.properties.category];button.addEventListener('click',()=>{const bounds=l.getBounds?.();if(bounds)map.fitBounds(bounds,{maxZoom:18,padding:[30,30]});else map.setView(l.getLatLng(),18);l.openPopup();});$('facility-list').append(button);});
 Object.keys(facilityColors).forEach(c=>$('count-'+c).textContent=facilityData.features.filter(f=>f.properties.category===c).length);
 $('facility-fit').disabled=!features.length;
 $('facility-status').textContent=features.length?`${features.length} objek bangunan dan fasilitas ditampilkan. Diperbarui: ${facilityData.updated_at}.`:'Belum ada fasilitas pada kategori yang dipilih atau data sumber belum tersedia.';
}
async function loadFacilities(){
 if(facilityBusy)return;facilityBusy=true;$('facility-retry').hidden=true;$('facility-status').textContent='Memuat fasilitas publik...';
 const abort=new AbortController(),timer=setTimeout(()=>abort.abort(),45000);
 try{const response=await fetch($('facilities').dataset.url,{signal:abort.signal,headers:{Accept:'application/json'}});if(!response.ok)throw new Error('unavailable');facilityData=await response.json();renderFacilities();}
 catch(e){$('facility-status').textContent='Data fasilitas belum dapat dimuat. Peta dasar tetap dapat digunakan.';$('facility-retry').hidden=false;}
 finally{clearTimeout(timer);facilityBusy=false;}
}
$('facilities').querySelectorAll('input').forEach(i=>i.addEventListener('change',renderFacilities));
$('facility-fit').addEventListener('click',()=>{if(facilityLayer?.getBounds().isValid())map.fitBounds(facilityLayer.getBounds(),{padding:[30,30],maxZoom:16});});
$('facility-retry').addEventListener('click',loadFacilities);loadFacilities();
})();