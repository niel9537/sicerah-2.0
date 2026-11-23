<?php

namespace App\Traits;

use App\Services\IndicatorValueParser;
use Illuminate\Database\Eloquent\Model;

/**
 * Bantuan baca angka dari kolom yang sebenarnya teks bebas (varchar) — dipakai untuk
 * indikator_target.target & indikator_realisasi.realisasi, yang isinya kadang bukan
 * angka murni ("Baik", "N/A", "-") atau desimalnya pakai koma ala Indonesia ("53,51")
 * alih-alih titik ("53.51").
 *
 * PENTING soal error "uncast decimal": kalau model IndikatorTarget/IndikatorRealisasi
 * (tidak ikut ter-upload jadi tidak terlihat dari sini) kebetulan men-cast kolom ini ke
 * decimal/float di $casts, maka sekadar MENGAKSES $model->target atau $model->realisasi
 * SUDAH memicu proses casting itu — dan kalau isinya teks, casting itu sendiri yang bisa
 * melempar error, sebelum sempat dicek is_numeric() sama sekali. Makanya semua method di
 * sini WAJIB dipanggil lewat getRawOriginal(), bukan lewat properti langsung, karena
 * getRawOriginal() mengambil nilai mentah dari database dan melewati casting apa pun.
 */
trait ParsesAngkaFleksibel
{
    /** Ambil nilai mentah kolom $kolom dari $model (lewat getRawOriginal, aman dari casting), lalu parse jadi angka. */
    protected function angkaDariModel(?Model $model, string $kolom): ?float
    {
        if (! $model) {
            return null;
        }

        return $this->angkaAtauNull($model->getRawOriginal($kolom));
    }

    /** Ambil nilai mentah kolom $kolom dari $model TANPA di-parse — dipakai untuk ditampilkan apa adanya (mis. di kotak input). */
    protected function rawDariModel(?Model $model, string $kolom): mixed
    {
        return $model?->getRawOriginal($kolom);
    }

    /** Parse nilai bebas (string/angka/null apa saja) jadi float, atau null kalau kosong/bukan angka. */
    protected function angkaAtauNull(mixed $nilai): ?float
    {
        return app(IndicatorValueParser::class)->numericValue($nilai);
    }

    /** Nilai lengkap untuk tampilan grafik, termasuk rentang dan teks asli. */
    protected function nilaiTerstruktur(mixed $nilai): array
    {
        return app(IndicatorValueParser::class)->parse($nilai);
    }

    /**
     * Normalisasi format desimal: "53,51" → "53.51" ; "1.234,56" → "1234.56" ;
     * "1,234.56" → "1234.56" ; "65.48" (sudah baku) tetap "65.48". Teks yang memang
     * bukan angka ("Baik", "N/A") dibiarkan apa adanya — biar tetap gagal is_numeric()
     * di angkaAtauNull(), sesuai maksudnya (bukan angka ya tetap bukan angka).
     */
    protected function normalisasiAngka(string $teks): string
    {
        $parsed = app(IndicatorValueParser::class)->parse($teks);

        return $parsed['graph'] === null ? $teks : (string) $parsed['graph'];
    }
}
