<?php

namespace App\Http\Controllers;

use App\Services\RpjmdContent;
use App\Services\RpjmdHierarchy;
use Illuminate\Http\Request;

class RpjmdContentController extends Controller
{
    public function indicator(\App\Models\Indikator $indikator)
    {
        $indikator->load('targets');
        $sources = \App\Models\RpjmdIndicatorSource::where('indikator_id', $indikator->id)->orderBy('file')->orderBy('baris')->get();
        return view('rpjmd.indicator', compact('indikator', 'sources'));
    }

    public function show(RpjmdContent $content, RpjmdHierarchy $hierarchy)
    {
        $rpjmd = $content->get();
        return view('rpjmd.public', ['rpjmd' => $rpjmd, 'hierarchyRows' => $hierarchy->present($rpjmd)]);
    }

    public function edit(RpjmdContent $content, RpjmdHierarchy $hierarchy)
    {
        $rpjmd = $content->get();
        $indicators = $hierarchy->indicators();
        return view('rpjmd.edit', ['rpjmd' => $rpjmd, 'indicators' => $indicators, 'hierarchyRows' => $hierarchy->present($rpjmd, $indicators)]);
    }

    public function update(Request $request, RpjmdContent $content)
    {
        $data = $request->validate([
            'visi' => ['required', 'string', 'max:1000'],
            'pairs' => ['required', 'array', 'size:5'],
            'pairs.*' => ['required', 'array:misi,tujuan'],
            'pairs.*.misi' => ['required', 'string', 'max:2000'],
            'pairs.*.tujuan' => ['required', 'string', 'max:2000'],
            'links' => ['sometimes', 'array'],
            'links.*' => ['nullable', 'integer', 'exists:indikator,id'],
        ]);
        $data['pairs'] = array_values($data['pairs']);
        if (!array_key_exists('links', $data)) {
            $existing = $content->get();
            if (isset($existing['links'])) $data['links'] = $existing['links'];
        }
        $content->save($data);
        return redirect()->route('cms.rpjmd.edit')->with('success', 'Visi, 5 misi dan 5 tujuan RPJMD berhasil diperbarui.');
    }
}
