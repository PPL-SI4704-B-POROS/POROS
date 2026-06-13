<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Siswa;
use App\Models\Resep;
use App\Models\ProduksiHarian;
use Carbon\Carbon;

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
            $quantityGram = $this->toGram((float) $this->quantity, $this->satuan);
            $kebutuhan    = $gramasi * $totalSiswa;
            return round($quantityGram / $kebutuhan, 4);
        }

        return round((float) $this->quantity / $totalSiswa, 4);
    }

    /**
     * UBAH STOCK LEVEL LAMA
     * Mengarah langsung ke stock_indicator agar statistik otomatis mengikuti logika baru milikmu.
     */
    public function getStockLevelAttribute(): string
    {
        return $this->stock_indicator;
    }

    /**
     * INDIKATOR STATUS UTAMA (Format dasar: "Perlu Restock untuk YYYY-MM-DD")
     * Logika inti buatanmu untuk memprediksi kecukupan bahan berdasarkan jadwal produksi riil.
     */
    public function getStatusTextAttribute(): string
    {
        $stokTersisa = $this->toGram((float) $this->quantity, $this->satuan);

        // Mengambil jadwal produksi 7 hari ke depan yang belum selesai dimasak
        $jadwal = ProduksiHarian::with('menu.reseps')
            ->whereDate('tanggal_produksi', '>=', now()->toDateString())
            ->whereNotIn('status_produksi', ['Memasak', 'Selesai'])
            ->orderBy('tanggal_produksi')
            ->take(7)
            ->get();

        foreach ($jadwal as $hari) {
            if (!$hari->menu) {
                continue;
            }

            foreach ($hari->menu->reseps as $resep) {
                if ($resep->bahan_id != $this->bahan_baku_id) {
                    continue;
                }

                $kebutuhan = $resep->gramasi_per_porsi * $hari->total_target_porsi;
                $stokTersisa -= $kebutuhan;

                if ($stokTersisa < 0) {
                    // Menggunakan format Y-m-d standar agar aman di-parse oleh regex internal
                    $tanggalSaja = Carbon::parse($hari->tanggal_produksi)->toDateString();
                    return "Perlu Restock untuk {$tanggalSaja}";
                }
            }
        }

        return 'Aman 7 Hari';
    }

    /**
     * INDIKATOR WARNA STOK (Menghitung selisih hari dari hasil rumus buatanmu)
     */
    public function getStockIndicatorAttribute(): string
    {
        $statusText = $this->status_text;

        if ($statusText === 'Aman 7 Hari') {
            return 'good';
        }

        if (!str_contains($statusText, 'Perlu Restock')) {
            return 'critical';
        }

        // Regex menangkap format YYYY-MM-DD hasil kalkulasi status_text
        preg_match('/(\d{4}-\d{2}-\d{2})/', $statusText, $match);

        if (!isset($match[1])) {
            return 'critical';
        }

        try {
            $tanggalRestock = Carbon::createFromFormat('Y-m-d', $match[1])->startOfDay();
            $hariIni = now()->startOfDay();
            
            $selisihHari = $hariIni->diffInDays($tanggalRestock, false);
        } catch (\Exception $e) {
            return 'critical';
        }

        // Logika warna adaptif buatanmu berdasarkan sisa hari produksi
        if ($selisihHari <= 2) {
            return 'critical'; // Merah 🔴
        }

        if ($selisihHari <= 4) {
            return 'low'; // Kuning 🟡
        }

        return 'good'; // Hijau 🟢
    }

    /**
     * Helper opsional: Digunakan oleh file Blade untuk menampilkan teks tanggal Indonesia yang cantik (Locale Aware)
     */
    public function getStatusTextFormattedAttribute(): string
    {
        $statusText = $this->status_text;

        if (str_contains($statusText, 'Perlu Restock')) {
            preg_match('/(\d{4}-\d{2}-\d{2})/', $statusText, $match);
            if (isset($match[1])) {
                $tanggalIndo = Carbon::parse($match[1])->translatedFormat('d M Y');
                return "Perlu Restock untuk {$tanggalIndo}";
            }
        }

        return $statusText;
    }

    /**
     * Helper: konversi quantity ke gram berdasarkan satuan stok gudang.
     */
    private function toGram(float $quantity, ?string $satuan): float
    {
        if (is_null($satuan)) {
            return $quantity;
        }

        return match (strtolower(trim($satuan))) {
            'kg', 'kilogram' => $quantity * 1000,
            'l', 'liter'     => $quantity * 1000,
            default          => $quantity,
        };
    }
}