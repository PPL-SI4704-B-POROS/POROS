<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InventoryMonitorController extends Controller
{
    public function index()
    {
        $bahanBakus = BahanBaku::with('supplier')->get();
        $suppliers = Supplier::all();

        return view('dashboards.superadmin.inventory-monitor', compact('bahanBakus', 'suppliers'));
    }
}
