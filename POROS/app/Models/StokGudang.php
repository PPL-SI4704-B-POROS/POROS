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
     * Menjumlahkan semua gramasi_per_porsi bahan ini di seluruh menu aktif.
     * Satuan: gram/porsi (mengikuti satuan gramasi_per_porsi di tabel reseps).
     *
     * Mengembalikan 0 jika bahan ini tidak terdaftar di resep manapun.
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
     * = gramasi_per_porsi × jumlah_siswa_aktif
     */
    public function getKebutuhanSatuSiklusAttribute(): float
    {
        $totalSiswa = Siswa::where('status', 'Active')->count();
        return $this->gramasi_per_porsi * $totalSiswa;
    }

    /**
     * Coverage: berapa kali stok saat ini dapat mencukupi kebutuhan 1 siklus.
     *
     * - Jika bahan ada di Resep  → coverage = quantity_gram / kebutuhan_satu_siklus
     * - Jika bahan TIDAK ada di Resep (gramasi = 0) → fallback:
     * coverage = quantity / jumlah_siswa  (interpretasi: unit-per-siswa)
     *
     * Mengembalikan 0 jika siswa = 0.
     */
    public function getCoverageAttribute(): float
    {
        $totalSiswa = Siswa::where('status', 'Active')->count();

        if ($totalSiswa === 0) {
            return 0.0;
        }

        $gramasi = $this->gramasi_per_porsi;

        if ($gramasi > 0) {
            // Konversi quantity ke gram sesuai satuan stok gudang
            $quantityGram = $this->toGram($this->quantity, $this->satuan);
            $kebutuhan    = $gramasi * $totalSiswa;
            return round($quantityGram / $kebutuhan, 4);
        }

        // Fallback: tidak ada data resep, pakai rasio unit/siswa
        return round($this->quantity / $totalSiswa, 4);
    }

    /**
     * OWAHAN UTAMA SAKING GAMBAR PALING ANYAR:
     * Logika lawas dibusak kabeh, diganti mriksa isi variabel status_text
     */
    public function getStockLevelAttribute(): string
    {
        if (str_contains($this->status_text, 'Aman')) {
            return 'good';
        }

        if (str_contains($this->status_text, 'Cukup sampai')) {
            return 'low';
        }

        return 'critical';
    }

    /**
     * Menghitung kecukupan stok secara dinamis berdasarkan jadwal menu harian
     */
    public function getStatusTextAttribute(): string
    {
        $stokTersisa = $this->toGram($this->quantity, $this->satuan);

        $jadwal = ProduksiHarian::with('menu.reseps')
            ->whereDate('tanggal_produksi', '>=', now()->toDateString())
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

                    return "Cukup sampai {$tanggal}";
                }
            }
        }

        return 'Aman 7 Hari';
    }

    /**
     * Helper: konversi quantity ke gram berdasarkan satuan stok gudang.
     * Mendukung: gram, kg, ml, liter.
     * Satuan lain dikembalikan apa adanya (diasumsikan sudah dalam unit dasar).
     */
    private function toGram(float $quantity, string $satuan): float
    {
        return match (strtolower(trim($satuan))) {
            'kg'    => $quantity * 1000,
            'liter' => $quantity * 1000,
            default => $quantity, // gram, ml, atau satuan lain
        };
    }
}