<?php

namespace App\Http\Controllers;

use App\Services\RpjmdContent;
use Illuminate\Http\Request;

class RpjmdContentController extends Controller
{
    public function show(RpjmdContent $content)
    {
        return view('rpjmd.public', ['rpjmd' => $content->get()]);
    }

    public function edit(RpjmdContent $content)
    {
        return view('rpjmd.edit', ['rpjmd' => $content->get()]);
    }

    public function update(Request $request, RpjmdContent $content)
    {
        $data = $request->validate([
            'visi' => ['required', 'string', 'max:1000'],
            'pairs' => ['required', 'array', 'size:5'],
            'pairs.*' => ['required', 'array:misi,tujuan'],
            'pairs.*.misi' => ['required', 'string', 'max:2000'],
            'pairs.*.tujuan' => ['required', 'string', 'max:2000'],
        ]);
        $data['pairs'] = array_values($data['pairs']);
        $content->save($data);
        return redirect()->route('cms.rpjmd.edit')->with('success', 'Visi, 5 misi dan 5 tujuan RPJMD berhasil diperbarui.');
    }
}
