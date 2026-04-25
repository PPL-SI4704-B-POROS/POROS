<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Resep;
use App\Models\BahanBaku;
use App\Models\ProduksiHarian;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $menus = Menu::with('reseps.bahanBaku')->get();
        $bahanBakus = BahanBaku::orderBy('nama_bahan', 'asc')->get();
        
        // Support week navigation via query param
        $weekOffset = (int) $request->query('week', 0);
        $startOfWeek = Carbon::now()->startOfWeek()->addWeeks($weekOffset);
        $endOfWeek = $startOfWeek->copy()->endOfWeek();
        
        $schedules = ProduksiHarian::with('menu.reseps.bahanBaku')
            ->whereBetween('tanggal_produksi', [$startOfWeek, $endOfWeek])
            ->get()
            ->groupBy('tanggal_produksi');

        return view('dashboards.meal-planning', compact('menus', 'bahanBakus', 'schedules', 'startOfWeek', 'weekOffset'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_menu' => 'required|string|max:255',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.bahan_id' => 'required|exists:bahan_bakus,id',
            'ingredients.*.gramasi' => 'required|numeric|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $totalKalori = 0;
            $totalProtein = 0;
            $totalKarbohidrat = 0;
            $totalLemak = 0;

            foreach ($request->ingredients as $ingredient) {
                $bahan = BahanBaku::findOrFail($ingredient['bahan_id']);
                $gramasi = $ingredient['gramasi'];
                $multiplier = $gramasi / 100;

                $totalKalori     += $bahan->energi_per_100g * $multiplier;
                $totalProtein    += $bahan->protein_per_100g * $multiplier;
                $totalKarbohidrat += $bahan->karbohidrat_per_100g * $multiplier;
                $totalLemak      += $bahan->lemak_per_100g * $multiplier;
            }

            $menu = Menu::create([
                'nama_menu'        => $request->nama_menu,
                'total_kalori'     => round($totalKalori, 2),
                'total_protein'    => round($totalProtein, 2),
                'total_karbohidrat' => round($totalKarbohidrat, 2),
                'total_lemak'      => round($totalLemak, 2),
                'deskripsi_gizi'   => round($totalKalori) . ' kkal',
            ]);

            foreach ($request->ingredients as $ingredient) {
                Resep::create([
                    'menu_id'         => $menu->id,
                    'bahan_id'        => $ingredient['bahan_id'],
                    'gramasi_per_porsi' => $ingredient['gramasi'],
                ]);
            }
        });

        return redirect()->back()->with('success', 'Menu dan Resep berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Menu $menu)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Menu $menu)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'nama_menu' => 'required|string|max:255',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.bahan_id' => 'required|exists:bahan_bakus,id',
            'ingredients.*.gramasi' => 'required|numeric|min:1',
        ]);

        DB::transaction(function () use ($request, $menu) {
            $totalKalori = 0;
            $totalProtein = 0;
            $totalKarbohidrat = 0;
            $totalLemak = 0;

            foreach ($request->ingredients as $ingredient) {
                $bahan = BahanBaku::findOrFail($ingredient['bahan_id']);
                $gramasi = $ingredient['gramasi'];
                $multiplier = $gramasi / 100;

                $totalKalori     += $bahan->energi_per_100g * $multiplier;
                $totalProtein    += $bahan->protein_per_100g * $multiplier;
                $totalKarbohidrat += $bahan->karbohidrat_per_100g * $multiplier;
                $totalLemak      += $bahan->lemak_per_100g * $multiplier;
            }

            $menu->update([
                'nama_menu'        => $request->nama_menu,
                'total_kalori'     => round($totalKalori, 2),
                'total_protein'    => round($totalProtein, 2),
                'total_karbohidrat' => round($totalKarbohidrat, 2),
                'total_lemak'      => round($totalLemak, 2),
                'deskripsi_gizi'   => round($totalKalori) . ' kkal',
            ]);

            // Replace old reseps with new ones
            $menu->reseps()->forceDelete();

            foreach ($request->ingredients as $ingredient) {
                Resep::create([
                    'menu_id'         => $menu->id,
                    'bahan_id'        => $ingredient['bahan_id'],
                    'gramasi_per_porsi' => $ingredient['gramasi'],
                ]);
            }
        });

        return redirect()->back()->with('success', 'Menu berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu)
    {
        DB::transaction(function () use ($menu) {
            $menu->produksiHarians()->delete();
            $menu->reseps()->delete();
            $menu->delete();
        });

        return redirect()->back()->with('success', 'Menu berhasil dihapus.');
    }
}
