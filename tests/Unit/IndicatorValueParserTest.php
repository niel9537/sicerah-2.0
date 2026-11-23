<?php

namespace Tests\Unit;

use App\Services\IndicatorValueParser;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class IndicatorValueParserTest extends TestCase
{
    #[DataProvider('numericValues')]
    public function test_it_parses_numeric_formats(string $raw, string $type, float $graph, float $min, float $max): void
    {
        $parsed = app(IndicatorValueParser::class)->parse($raw);

        $this->assertSame($type, $parsed['type']);
        $this->assertEqualsWithDelta($graph, $parsed['graph'], 0.000001);
        $this->assertEqualsWithDelta($min, $parsed['min'], 0.000001);
        $this->assertEqualsWithDelta($max, $parsed['max'], 0.000001);
        $this->assertSame($raw, $parsed['raw']);
    }

    public static function numericValues(): array
    {
        return [
            'decimal comma' => ['53,51', 'number', 53.51, 53.51, 53.51],
            'decimal point' => ['53.51', 'number', 53.51, 53.51, 53.51],
            'indonesian thousands' => ['89.025,05', 'number', 89025.05, 89025.05, 89025.05],
            'international thousands' => ['89,025.05', 'number', 89025.05, 89025.05, 89025.05],
            'footnote suffix' => ['53.51**', 'number', 53.51, 53.51, 53.51],
            'letter grade suffix' => ['77,35 (B)', 'number', 77.35, 77.35, 77.35],
            'excel text suffix' => ['89+M119', 'number', 89.0, 89.0, 89.0],
            'hyphen range' => ['72,49-72,67', 'range', 72.58, 72.49, 72.67],
            'en dash range' => ['81,53–81,60', 'range', 81.565, 81.53, 81.60],
            'descending range' => ['26,00–25,00', 'range', 25.5, 25.0, 26.0],
        ];
    }

    public function test_it_preserves_text_and_missing_markers(): void
    {
        $this->assertSame('text', app(IndicatorValueParser::class)->parse('WTP')['type']);
        $this->assertSame('empty', app(IndicatorValueParser::class)->parse('-')['type']);
        $this->assertNull(app(IndicatorValueParser::class)->parse('N/A')['graph']);
    }
}
