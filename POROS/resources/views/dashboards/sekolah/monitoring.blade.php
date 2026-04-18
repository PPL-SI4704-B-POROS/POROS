@extends('layouts.app')

@section('title', 'School Monitoring')

@section('styles')
<style>
    .card { background: white; border-radius: 1rem; padding: 2rem; border: 1px solid var(--border-color); text-align: center; }
</style>
@endsection

@section('content')
<div class="dashboard-layout">
    @include('partials.sidebar')

    <main class="main-content">
        @include('partials.header')

        <div class="card">
            <h1 style="color: #0c1e35;">School Monitoring</h1>
            <p style="color: var(--text-muted); margin-top: 1rem;">Halaman ini sedang dalam pengembangan.</p>
        </div>
    </main>
</div>
@endsection
