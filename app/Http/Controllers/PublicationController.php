<?php

namespace App\Http\Controllers;

use App\Models\Publication;

class PublicationController extends Controller
{
    public function index()
    {
        $publications = Publication::published()->orderByDesc('published_at')->orderByDesc('id')->paginate(9);
        return view('publications.index', compact('publications'));
    }

    public function show(Publication $publication)
    {
        abort_unless(Publication::published()->whereKey($publication->id)->exists(), 404);
        $related = Publication::published()->whereKeyNot($publication->id)->orderByDesc('published_at')->limit(3)->get();
        return view('publications.show', compact('publication', 'related'));
    }
}
