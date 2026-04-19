@extends('layouts.app')

@section('title', 'Halaman Dalam Pengembangan')

@section('content')
<div class="dashboard-layout">
    @include('partials.sidebar')

    <main class="main-content">
        @include('partials.header')

        <div class="card" style="text-align: center; padding: 5rem 2rem;">
            <div style="font-size: 4rem; margin-bottom: 2rem;">🚧</div>
            <h1 style="font-size: 2rem; font-weight: 800; color: #0c1e35; margin-bottom: 1rem;">Halaman Sedang Dikembangkan</h1>
            <p style="color: var(--text-muted); max-width: 600px; margin: 0 auto 2rem;">
                Fitur ini sedang dalam tahap pengembangan oleh tim POROS. Kami bekerja keras untuk memberikan pengalaman terbaik bagi Anda.
            </p>
            <button onclick="window.history.back()" class="btn btn-primary" style="width: auto; padding: 0.75rem 2rem;">
                Kembali ke Halaman Sebelumnya
            </button>
        </div>
    </main>
</div>
@endsection
