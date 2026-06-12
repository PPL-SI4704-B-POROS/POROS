<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Siswa;
use App\Models\Resep;
use App\Models\ProduksiHarian;

class StokGudang extends Model
{
    use HasFactory;

    protected $table = 'stok_gudang';

    protected $fillable = [
        'bahan_baku_id',
        'supplier_id',
        'quantity',
        'satuan',
    ];

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_baku_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function histories()
    {
        return $this->hasMany(StockHistory::class, 'stok_gudang_id')
                    ->orderByDesc('incoming_date');
    }

    /**
     * Hitung kebutuhan bahan ini per porsi dari data Resep.
     */
    public function getGramasiPerPorsiAttribute(): float
    {
        if (is_null($this->bahan_baku_id)) {
            return 0.0;
        }

        $total = Resep::where('bahan_id', $this->bahan_baku_id)
            ->sum('gramasi_per_porsi');

        return (float) $total;
    }

    /**
     * Hitung kebutuhan bahan ini untuk seluruh siswa aktif (dalam gram).
     */
    public function getKebutuhanSatuSiklusAttribute(): float
    {
        $totalSiswa = Siswa::where('status', 'Active')->count();
        return $this->gramasi_per_porsi * $totalSiswa;
    }

    /**
     * Coverage: berapa kali stok saat ini dapat mencukupi kebutuhan 1 siklus.
     */
    public function getCoverageAttribute(): float
    {
        $totalSiswa = Siswa::where('status', 'Active')->count();

        if ($totalSiswa === 0) {
            return 0.0;
        }

        $gramasi = $this->gramasi_per_porsi;

        if ($gramasi > 0) {
            $quantityGram = $this->toGram($this->quantity, $this->satuan);
            $kebutuhan    = $gramasi * $totalSiswa;
            return round($quantityGram / $kebutuhan, 4);
        }

        return round($this->quantity / $totalSiswa, 4);
    }

    /**
     * 3. UBAH STOCK LEVEL LAMA
     * Mengarah langsung ke stock_indicator agar statistik otomatis mengikuti logika baru.
     */
    public function getStockLevelAttribute(): string
    {
        return $this->stock_indicator;
    }

    /**
     * 2. UBAH STATUS TEXT (Ganti format dadi "Perlu Restock untuk [tanggal]")
     */
    public function getStatusTextAttribute(): string
    {
        $stokTersisa = $this->toGram($this->quantity, $this->satuan);

        $jadwal = ProduksiHarian::with('menu.reseps')
            ->whereDate('tanggal_produksi', '>=', now()->toDateString())
            ->whereNotIn('status_produksi', ['Memasak', 'Selesai'])
            ->orderBy('tanggal_produksi')
            ->take(7)
            ->get();

        foreach ($jadwal as $hari) {
            foreach ($hari->menu->reseps as $resep) {
                if ($resep->bahan_id != $this->bahan_baku_id) {
                    continue;
                }

                $kebutuhan = $resep->gramasi_per_porsi * $hari->total_target_porsi;
                $stokTersisa -= $kebutuhan;

                if ($stokTersisa < 0) {
                    $tanggal = \Carbon\Carbon::parse(
                        $hari->tanggal_produksi
                    )->translatedFormat('d M Y');

                    // Diubah dadi luwih informatif kanggo Kapala Dapur
                    return "Perlu Restock untuk {$tanggal}";
                }
            }
        }

        return 'Aman 7 Hari';
    }

    /**
     * 1. TAMBAH ACCESSOR BARU (Stock Indicator adhedhasar selisih hari)
     */
    public function getStockIndicatorAttribute(): string
    {
        if ($this->status_text === 'Aman 7 Hari') {
            return 'good';
        }

        if (!str_contains($this->status_text, 'Perlu Restock')) {
            return 'critical';
        }

        // Regex disesuaikan kanggo nangkep format tanggal (contoh: 13 Jun 2026 utawa 13 Juni 2026)
        preg_match('/(\d{1,2}\s\w+\s\d{4})/', $this->status_text, $match);

        if (!isset($match[1])) {
            return 'critical';
        }

        // Parse tanggal nggunakake format lokal Indonesia (Locale Aware)
        try {
            $tanggalRestock = \Carbon\Carbon::parse($match[1]);
        } catch (\Exception $e) {
            return 'critical';
        }

        $selisihHari = now()->startOfDay()->diffInDays(
            $tanggalRestock->startOfDay(),
            false
        );

        // Logika Status Warna adhedhasar sisa hari
        if ($selisihHari <= 2) {
            return 'critical'; // Abang 🔴
        }

        if ($selisihHari <= 4) {
            return 'low'; // Kuning 🟡
        }

        return 'good'; // Ijo 🟢
    }

    /**
     * Helper: konversi quantity ke gram berdasarkan satuan stok gudang.
     */
    private function toGram(float $quantity, string $satuan): float
    {
        return match (strtolower(trim($satuan))) {
            'kg'    => $quantity * 1000,
            'liter' => $quantity * 1000,
            default => $quantity,
        };
    }
}