<h2>Buat Pengumuman Baru</h2>

<form action="{{ route('announcements.store') }}" method="POST">
    @csrf

    <label>Judul</label><br>
    <input type="text" name="title"><br><br>

    <label>Isi Pengumuman</label><br>
    <textarea name="content" rows="5"></textarea><br><br>

    <button type="submit">Simpan</button>
</form>