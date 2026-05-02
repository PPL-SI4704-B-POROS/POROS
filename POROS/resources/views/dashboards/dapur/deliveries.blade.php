@extends('layouts.app')

@section('title', 'Logistics & Deliveries')

@section('content')

<div class="dashboard-layout">

```
@include('partials.sidebar')

<main class="main-content">

    @include('partials.header')



    <!-- PAGE HEADER -->
    <div style="
        display:flex;
        justify-content:space-between;
        align-items:center;
        margin-bottom:2rem;
    ">

        <div>

            <h1 style="
                font-size:2rem;
                font-weight:800;
                color:#0c1e35;
            ">
                Logistics & Deliveries
            </h1>

            <p style="
                color:#6b7280;
                margin-top:0.5rem;
            ">
                Track and manage ingredient stock deliveries
            </p>

        </div>

        <button
            onclick="openModal()"
            style="
                background:#ff6b00;
                color:white;
                border:none;
                padding:1rem 1.5rem;
                border-radius:14px;
                font-weight:700;
                cursor:pointer;
            "
        >
            + Add Incoming Stock
        </button>

    </div>





    <!-- SUCCESS -->
    @if(session('success'))

        <div style="
            background:#dcfce7;
            color:#166534;
            padding:1rem;
            border-radius:12px;
            margin-bottom:1rem;
            font-weight:600;
        ">
            {{ session('success') }}
        </div>

    @endif





    <!-- ERROR -->
    @if($errors->any())

        <div style="
            background:#fee2e2;
            color:#991b1b;
            padding:1rem;
            border-radius:12px;
            margin-bottom:1rem;
        ">

            <ul style="margin-left:1rem;">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif





    <!-- SUMMARY -->
    <div style="
        display:grid;
        grid-template-columns:repeat(4,1fr);
        gap:1rem;
        margin-bottom:2rem;
    ">

        <div class="card">

            <h3 style="
                color:#6b7280;
                margin-bottom:1rem;
            ">
                Total Stock
            </h3>

            <h1 style="
                font-size:2rem;
                color:#0c1e35;
            ">
                {{ $stocks->count() }}
            </h1>

        </div>



        <div class="card">

            <h3 style="
                color:#6b7280;
                margin-bottom:1rem;
            ">
                Good Stock
            </h3>

            <h1 style="
                font-size:2rem;
                color:#22c55e;
            ">
                {{ $stocks->where('jumlah_masuk', '>=', 100)->count() }}
            </h1>

        </div>



        <div class="card">

            <h3 style="
                color:#6b7280;
                margin-bottom:1rem;
            ">
                Low Stock
            </h3>

            <h1 style="
                font-size:2rem;
                color:#f59e0b;
            ">
                {{ $stocks->whereBetween('jumlah_masuk', [50,99])->count() }}
            </h1>

        </div>



        <div class="card">

            <h3 style="
                color:#6b7280;
                margin-bottom:1rem;
            ">
                Critical Stock
            </h3>

            <h1 style="
                font-size:2rem;
                color:#ef4444;
            ">
                {{ $stocks->where('jumlah_masuk', '<', 50)->count() }}
            </h1>

        </div>

    </div>





    <!-- TABLE -->
    <div class="card" style="
        background:white;
        border-radius:20px;
        padding:2rem;
    ">

        <div style="
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:2rem;
        ">

            <h2 style="
                font-size:1.5rem;
                font-weight:700;
                color:#0c1e35;
            ">
                Inventory Stock
            </h2>

        </div>





        <table style="
            width:100%;
            border-collapse:collapse;
        ">

            <thead>

                <tr style="
                    border-bottom:1px solid #e5e7eb;
                    text-align:left;
                ">

                    <th style="padding:1rem;">Status</th>
                    <th style="padding:1rem;">Item</th>
                    <th style="padding:1rem;">Category</th>
                    <th style="padding:1rem;">Quantity</th>
                    <th style="padding:1rem;">Supplier</th>
                    <th style="padding:1rem;">Batch ID</th>
                    <th style="padding:1rem;">Incoming Date</th>
                    <th style="padding:1rem;">Expired Date</th>
                    <th style="padding:1rem;">Stock Level</th>

                </tr>

            </thead>





            <tbody>

                @forelse($stocks as $stock)

                    <tr
                        onclick="openEditModal(
                            '{{ $stock->id }}',
                            '{{ $stock->nama_bahan }}',
                            '{{ $stock->jumlah_masuk }}',
                            '{{ $stock->satuan }}',
                            '{{ $stock->supplier_id }}',
                            '{{ $stock->batch_id }}',
                            '{{ $stock->tanggal_terima }}',
                            '{{ $stock->expired_date }}'
                        )"
                        style="
                            border-bottom:1px solid #f3f4f6;
                            cursor:pointer;
                        "
                    >

                        <!-- STATUS -->
                        <td style="padding:1rem;">

                            @if($stock->jumlah_masuk >= 100)

                                <div style="
                                    width:12px;
                                    height:12px;
                                    border-radius:999px;
                                    background:#22c55e;
                                "></div>

                            @elseif($stock->jumlah_masuk >= 50)

                                <div style="
                                    width:12px;
                                    height:12px;
                                    border-radius:999px;
                                    background:#eab308;
                                "></div>

                            @else

                                <div style="
                                    width:12px;
                                    height:12px;
                                    border-radius:999px;
                                    background:#ef4444;
                                "></div>

                            @endif

                        </td>





                        <!-- ITEM -->
                        <td style="padding:1rem;">

                            <div style="
                                font-weight:700;
                                color:#0c1e35;
                            ">
                                {{ $stock->nama_bahan }}
                            </div>

                            <div style="
                                font-size:0.85rem;
                                color:#64748b;
                                margin-top:0.3rem;
                            ">
                                Added:
                                {{ \Carbon\Carbon::parse($stock->created_at)->format('d M Y H:i') }}
                            </div>

                        </td>





                        <!-- CATEGORY -->
                        <td style="padding:1rem;">

                            <span style="
                                background:#dbeafe;
                                color:#1d4ed8;
                                padding:0.35rem 0.7rem;
                                border-radius:8px;
                                font-size:0.8rem;
                            ">

                                @if(str_contains(strtolower($stock->nama_bahan), 'ayam') || str_contains(strtolower($stock->nama_bahan), 'telur'))

                                    Protein

                                @elseif(str_contains(strtolower($stock->nama_bahan), 'beras'))

                                    Karbohidrat

                                @else

                                    Bahan Pokok

                                @endif

                            </span>

                        </td>





                        <!-- QUANTITY -->
                        <td style="
                            padding:1rem;
                            font-weight:700;
                        ">
                            {{ $stock->jumlah_masuk }} {{ $stock->satuan }}
                        </td>





                        <!-- SUPPLIER -->
                        <td style="padding:1rem;">

                            {{ $stock->supplier->nama_supplier ?? 'Supplier Tidak Ada' }}

                        </td>





                        <!-- BATCH -->
                        <td style="padding:1rem;">

                            {{ $stock->batch_id ?? '-' }}

                        </td>





                        <!-- INCOMING DATE -->
                        <td style="padding:1rem;">

                            {{ \Carbon\Carbon::parse($stock->tanggal_terima)->format('d M Y') }}

                        </td>





                        <!-- EXPIRED DATE -->
                        <td style="padding:1rem;">

                            @if($stock->expired_date)

                                {{ \Carbon\Carbon::parse($stock->expired_date)->format('d M Y') }}

                            @else

                                -

                            @endif

                        </td>





                        <!-- STOCK LEVEL -->
                        <td style="padding:1rem;">

                            @if($stock->jumlah_masuk >= 100)

                                <span style="
                                    background:#dcfce7;
                                    color:#166534;
                                    padding:0.4rem 0.8rem;
                                    border-radius:999px;
                                    font-size:0.85rem;
                                    font-weight:600;
                                ">
                                    Good Stock
                                </span>

                            @elseif($stock->jumlah_masuk >= 50)

                                <span style="
                                    background:#fef3c7;
                                    color:#92400e;
                                    padding:0.4rem 0.8rem;
                                    border-radius:999px;
                                    font-size:0.85rem;
                                    font-weight:600;
                                ">
                                    Low Stock
                                </span>

                            @else

                                <span style="
                                    background:#fee2e2;
                                    color:#991b1b;
                                    padding:0.4rem 0.8rem;
                                    border-radius:999px;
                                    font-size:0.85rem;
                                    font-weight:600;
                                ">
                                    Critical
                                </span>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="9" style="
                            padding:2rem;
                            text-align:center;
                            color:#9ca3af;
                        ">
                            No stock data available
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</main>
```

</div>

<!-- ADD MODAL -->

<div id="stockModal"
    style="
        display:none;
        position:fixed;
        inset:0;
        background:rgba(0,0,0,0.6);
        justify-content:center;
        align-items:center;
        z-index:999;
    "
>

```
<div style="
    background:white;
    width:450px;
    border-radius:24px;
    padding:2rem;
">

    <h2 style="
        font-size:2rem;
        font-weight:800;
        color:#0c1e35;
        margin-bottom:2rem;
    ">
        Add Incoming Stock
    </h2>

    <form action="{{ route('stocks.store') }}" method="POST">

        @csrf

        <div style="display:flex; flex-direction:column; gap:1rem;">

            <select
                name="nama_bahan"
                required
                style="
                    width:100%;
                    padding:1rem;
                    border:1px solid #d1d5db;
                    border-radius:12px;
                "
            >

                <option value="">Select Ingredient</option>

                <option value="Ayam Broiler">Ayam Broiler</option>
                <option value="Beras Premium">Beras Premium</option>
                <option value="Telur Ayam">Telur Ayam</option>
                <option value="Minyak Goreng">Minyak Goreng</option>

            </select>

            <input
                type="number"
                name="jumlah_masuk"
                required
                placeholder="Quantity"
                style="
                    width:100%;
                    padding:1rem;
                    border:1px solid #d1d5db;
                    border-radius:12px;
                "
            >

            <select
                name="satuan"
                required
                style="
                    width:100%;
                    padding:1rem;
                    border:1px solid #d1d5db;
                    border-radius:12px;
                "
            >

                <option value="">Select Unit</option>

                <option value="kg">kg</option>
                <option value="pcs">pcs</option>
                <option value="liter">liter</option>

            </select>

            <select
                name="supplier_id"
                required
                style="
                    width:100%;
                    padding:1rem;
                    border:1px solid #d1d5db;
                    border-radius:12px;
                "
            >

                <option value="">Select Supplier</option>

                @foreach($suppliers as $supplier)

                    <option value="{{ $supplier->id }}">
                        {{ $supplier->nama_supplier }}
                    </option>

                @endforeach

            </select>

            <input
                type="text"
                name="batch_id"
                required
                placeholder="Batch ID"
                style="
                    width:100%;
                    padding:1rem;
                    border:1px solid #d1d5db;
                    border-radius:12px;
                "
            >

            <input
                type="date"
                name="tanggal_terima"
                required
                style="
                    width:100%;
                    padding:1rem;
                    border:1px solid #d1d5db;
                    border-radius:12px;
                "
            >

            <input
                type="date"
                name="expired_date"
                required
                style="
                    width:100%;
                    padding:1rem;
                    border:1px solid #d1d5db;
                    border-radius:12px;
                "
            >

        </div>

        <div style="
            display:flex;
            gap:1rem;
            margin-top:2rem;
        ">

            <button
                type="submit"
                style="
                    flex:1;
                    background:#ff6b00;
                    color:white;
                    border:none;
                    padding:1rem;
                    border-radius:14px;
                    font-weight:700;
                    cursor:pointer;
                "
            >
                Add Stock
            </button>

            <button
                type="button"
                onclick="closeModal()"
                style="
                    flex:1;
                    background:#e5e7eb;
                    color:#111827;
                    border:none;
                    padding:1rem;
                    border-radius:14px;
                    font-weight:700;
                    cursor:pointer;
                "
            >
                Cancel
            </button>

        </div>

    </form>

</div>
```

</div>

<!-- EDIT MODAL -->

<div id="editModal"
    style="
        display:none;
        position:fixed;
        inset:0;
        background:rgba(0,0,0,0.6);
        justify-content:center;
        align-items:center;
        z-index:999;
    "
>

```
<div style="
    background:white;
    width:450px;
    border-radius:24px;
    padding:2rem;
">

    <h2 style="
        font-size:2rem;
        font-weight:800;
        color:#0c1e35;
        margin-bottom:2rem;
    ">
        Edit Stock
    </h2>

    <form method="POST" id="editForm">

        @csrf
        @method('PUT')

        <div style="display:flex; flex-direction:column; gap:1rem;">

            <input type="text"
                name="nama_bahan"
                id="edit_nama_bahan"
                placeholder="Nama Bahan"
                style="
                    width:100%;
                    padding:1rem;
                    border:1px solid #d1d5db;
                    border-radius:12px;
                "
            >

            <input type="number"
                name="jumlah_masuk"
                id="edit_jumlah_masuk"
                placeholder="Quantity"
                style="
                    width:100%;
                    padding:1rem;
                    border:1px solid #d1d5db;
                    border-radius:12px;
                "
            >

            <select
                name="satuan"
                id="edit_satuan"
                style="
                    width:100%;
                    padding:1rem;
                    border:1px solid #d1d5db;
                    border-radius:12px;
                "
            >
                <option value="kg">kg</option>
                <option value="pcs">pcs</option>
                <option value="liter">liter</option>
            </select>

            <select
                name="supplier_id"
                id="edit_supplier"
                style="
                    width:100%;
                    padding:1rem;
                    border:1px solid #d1d5db;
                    border-radius:12px;
                "
            >

                @foreach($suppliers as $supplier)

                    <option value="{{ $supplier->id }}">
                        {{ $supplier->nama_supplier }}
                    </option>

                @endforeach

            </select>

            <input type="text"
                name="batch_id"
                id="edit_batch_id"
                placeholder="Batch ID"
                style="
                    width:100%;
                    padding:1rem;
                    border:1px solid #d1d5db;
                    border-radius:12px;
                "
            >

            <input type="date"
                name="tanggal_terima"
                id="edit_tanggal"
                style="
                    width:100%;
                    padding:1rem;
                    border:1px solid #d1d5db;
                    border-radius:12px;
                "
            >

            <input type="date"
                name="expired_date"
                id="edit_expired"
                style="
                    width:100%;
                    padding:1rem;
                    border:1px solid #d1d5db;
                    border-radius:12px;
                "
            >

        </div>

        <div style="
            display:flex;
            gap:1rem;
            margin-top:2rem;
        ">

            <button
                type="submit"
                style="
                    flex:1;
                    background:#ff6b00;
                    color:white;
                    border:none;
                    padding:1rem;
                    border-radius:14px;
                    font-weight:700;
                    cursor:pointer;
                "
            >
                Update
            </button>

    </form>

            <form method="POST" id="deleteForm">

                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    style="
                        background:#ef4444;
                        color:white;
                        border:none;
                        padding:1rem;
                        border-radius:14px;
                        font-weight:700;
                        cursor:pointer;
                    "
                >
                    Delete
                </button>

            </form>

            <button
                onclick="closeEditModal()"
                style="
                    background:#e5e7eb;
                    color:#111827;
                    border:none;
                    padding:1rem;
                    border-radius:14px;
                    font-weight:700;
                    cursor:pointer;
                "
            >
                Cancel
            </button>

        </div>

</div>
```

</div>

<script>

function openModal() {
    document.getElementById('stockModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('stockModal').style.display = 'none';
}

function openEditModal(
    id,
    nama,
    jumlah,
    satuan,
    supplier,
    batch,
    tanggal,
    expired
) {

    document.getElementById('editModal').style.display = 'flex';

    document.getElementById('edit_nama_bahan').value = nama;
    document.getElementById('edit_jumlah_masuk').value = jumlah;
    document.getElementById('edit_satuan').value = satuan;
    document.getElementById('edit_supplier').value = supplier;
    document.getElementById('edit_batch_id').value = batch;
    document.getElementById('edit_tanggal').value = tanggal;
    document.getElementById('edit_expired').value = expired;

    document.getElementById('editForm').action =
        `/dashboard/dapur/deliveries/${id}`;

    document.getElementById('deleteForm').action =
        `/dashboard/dapur/deliveries/${id}`;
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

</script>

@endsection
