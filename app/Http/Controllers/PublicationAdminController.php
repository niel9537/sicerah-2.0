<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicationAdminController extends Controller
{
    public function index()
    {
        $publications = Publication::orderByDesc('created_at')->orderByDesc('id')->paginate(15);
        return view('publications.admin.index', compact('publications'));
    }

    public function create()
    {
        return view('publications.admin.form', ['publication' => new Publication(['kategori' => 'Berita'])]);
    }

    public function store(Request $request)
    {
        Publication::create($this->validatedData($request));
        return redirect()->route('cms.publications.index')->with('success', 'Publikasi berhasil disimpan.');
    }

    public function edit(Publication $publication)
    {
        return view('publications.admin.form', compact('publication'));
    }

    public function update(Request $request, Publication $publication)
    {
        $oldCover = $publication->cover_path;
        $data = $this->validatedData($request);
        $publication->update($data);
        if (isset($data['cover_path']) && $oldCover) Storage::disk('public')->delete($oldCover);
        return redirect()->route('cms.publications.index')->with('success', 'Publikasi berhasil diperbarui.');
    }

    public function destroy(Publication $publication)
    {
        $cover = $publication->cover_path;
        $publication->delete();
        if ($cover) Storage::disk('public')->delete($cover);
        return redirect()->route('cms.publications.index')->with('success', 'Publikasi berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:80'],
            'ringkasan' => ['required', 'string', 'max:600'],
            'konten' => ['required', 'string', 'max:100000'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120', 'dimensions:max_width=8000,max_height=8000'],
            'is_published' => ['required', 'boolean'],
            'published_at' => ['nullable', 'required_if:is_published,1', 'date'],
        ]);
        unset($data['cover']);
        if ($request->hasFile('cover')) {
            $path = $request->file('cover')->store('publications', 'public');
            abort_unless($path, 500, 'Gambar gagal disimpan. Silakan coba lagi.');
            $data['cover_path'] = $path;
        }
        return $data;
    }
}
