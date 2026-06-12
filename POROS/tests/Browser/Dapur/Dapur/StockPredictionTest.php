<?php

use App\Models\StokGudang;

test('stok memiliki status text', function () {

    $stok = StokGudang::first();

    expect($stok)->not->toBeNull();
    expect($stok->status_text)->not->toBeEmpty();
});

test('status level valid', function () {

    $stok = StokGudang::first();

    // SAKDURUNGE: status_level (Typo property)
    // DISESUAIKAKE: stock_level (Manut isi screenshot)
    expect([
        'good',
        'low',
        'critical'
    ])->toContain($stok->stock_level);
});

test('stok kritis jika tidak cukup untuk besok', function () {

    $stok = StokGudang::first();

    if (str_contains(strtolower($stok->status_text), 'restock')) {
        expect($stok->stock_level)->toBe('critical');
    }

    $this->assertTrue(true);
});

test('status text tidak kosong', function () {

    $stok = StokGudang::first();

    expect(strlen($stok->status_text))->toBeGreaterThan(0);
});

test('quantity tidak negatif', function () {

    $stok = StokGudang::first();

    expect($stok->quantity)->toBeGreaterThanOrEqual(0);
});