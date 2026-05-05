<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Siswa & Data Kesehatan</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], textarea, select { width: 100%; padding: 8px; box-sizing: border-box; }
        .alert-success { color: green; margin-bottom: 15px; font-weight: bold; }
    </style>
</head>
<body>

    <h2>Form Pendaftaran Data Kesehatan Siswa</h2>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('students.create') }}" method="POST">
        @csrf <div class="form-group">
            <label for="nisn">NISN</label>
            <input type="text" name="nisn" id="nisn" required>
        </div>

        <div class="form-group">
            <label for="name">Nama Lengkap</label>
            <input type="text" name="name" id="name" required>
        </div>

        <div class="form-group">
            <label for="gender">Jenis Kelamin</label>
            <select name="gender" id="gender" required>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
            </select>
        </div>

        <div class="form-group">
            <label for="class">Kelas</label>
            <input type="text" name="class" id="class" required>
        </div>

        <div class="form-group">
            <label for="allergies">Daftar Alergi (Kosongkan jika tidak ada)</label>
            <textarea name="allergies" id="allergies" rows="3" placeholder="Contoh: Kacang, Debu, Susu Sapi"></textarea>
        </div>

        <div class="form-group">
            <label for="basic_health_notes">Catatan Kesehatan Dasar</label>
            <textarea name="basic_health_notes" id="basic_health_notes" rows="3" placeholder="Contoh: Memiliki asma ringan"></textarea>
        </div>

        <button type="submit" style="padding: 10px 20px; background-color: #007BFF; color: white; border: none; cursor: pointer;">
            Simpan Data Siswa
        </button>
    </form>

</body>
</html>