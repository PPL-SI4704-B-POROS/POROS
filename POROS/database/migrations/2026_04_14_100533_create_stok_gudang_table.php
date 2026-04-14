public function up(): void
{
    Schema::create('stok_gudang', function (Blueprint $table) {
        $table->id();
        $table->string('nama_bahan'); // Contoh: Ayam, Beras, Cabai
        $table->integer('jumlah_masuk'); // Berapa banyak yang dibeli
        $table->string('satuan'); // kg, liter, atau krat
        $table->date('tanggal_terima'); // Tanggal barang datang ke gudang
        $table->string('keterangan')->nullable(); // Contoh: Dari Supplier A, Kualitas bagus
        $table->timestamps();
    });
}