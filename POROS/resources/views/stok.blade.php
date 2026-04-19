<!DOCTYPE html>
<html>
<head>
    <title>Input Stok</title>
</head>
<body>

    <h2>Form Input Stok</h2>

    @if(session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <form action="/stok" method="POST">
        @csrf

        <input type="text" name="nama_bahan" placeholder="Nama Bahan Baku"><br><br>

        <input type="number" step="0.01" name="jumlah_masuk" placeholder="Jumlah"><br><br>

        <select name="satuan">
            <option value="kg">Kilogram (kg)</option>
            <option value="liter">Liter</option>
        </select><br><br>

        <input type="date" name="tanggal_terima"><br><br>

        <input type="text" name="keterangan" placeholder="Keterangan (opsional)"><br><br>

        <button type="submit">Simpan Stok Masuk</button>
    </form>

</body>
</html>