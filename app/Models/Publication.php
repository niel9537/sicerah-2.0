<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Publication extends Model
{
    protected $fillable = ['judul', 'kategori', 'ringkasan', 'konten', 'cover_path', 'is_published', 'published_at'];
    protected $casts = ['is_published' => 'boolean', 'published_at' => 'datetime'];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function scopeForCarousel(Builder $query): Builder
    {
        return $query->published()->where('published_at', '>=', now()->subDays(7))
            ->orderByDesc('published_at')->orderByDesc('id')->limit(3);
    }

    public function getCoverUrlAttribute(): string
    {
        return $this->cover_path ? Storage::disk('public')->url($this->cover_path) : asset('images/bappelitbangda-hero.png');
    }
}
