namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StokGudang;

class StokController extends Controller
{
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama_bahan' => 'required',
            'jumlah_masuk' => 'required|numeric',
            'satuan' => 'required',
            'tanggal_terima' => 'required|date',
        ]);

        // Simpan ke Database secara Digital
        StokGudang::create($request->all());

        return redirect()->back()->with('success', 'Stok berhasil dicatat secara digital!');
    }
}