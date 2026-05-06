<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kesehatan Siswa</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        .btn { padding: 8px 15px; background-color: #28a745; color: white; text-decoration: none; border-radius: 4px; }
        .btn:hover { background-color: #218838; }
        .badge { background-color: #dc3545; color: white; padding: 3px 8px; border-radius: 12px; font-size: 12px; }
    </style>
</head>
<body>

    <h2>Daftar Kesehatan Dasar Siswa</h2>

    <a href="{{ route('students.create') }}" class="btn">+ Tambah Data Siswa</a>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NISN</th>
                <th>Nama Lengkap</th>
                <th>L/P</th>
                <th>Kelas</th>
                <th>Alergi</th>
                <th>Catatan Kesehatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $index => $student)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $student->nisn }}</td>
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->gender }}</td>
                    <td>{{ $student->class }}</td>
                    <td>
                        @if($student->allergies)
                            <span class="badge">{{ $student->allergies }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $student->basic_health_notes ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">Belum ada data siswa terdaftar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>