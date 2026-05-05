<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POROS - @yield('title')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    @if(Route::currentRouteName() == 'login')
        <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @endif
    
    @yield('styles')
</head>
<body>
    <!-- Toast Container (Corner) -->
    <div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px;"></div>

    @yield('content')

    <script src="{{ asset('js/dashboard.js') }}"></script>
    
    <!-- Notification Handler -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                showToast("{{ session('success') }}", 'success');
            @endif

            @if(session('error'))
                showModal("{{ session('error') }}", 'error', 'Peringatan', 'Silakan periksa kembali data yang Anda masukkan.');
            @endif

            @if($errors->any())
                @php
                    $isLogin = Route::currentRouteName() == 'login' || Route::currentRouteName() == 'login.post';
                    $title = $isLogin ? 'Login Gagal' : 'Validasi Gagal';
                    $hint = $isLogin ? 'Pastikan email dan password sudah benar. Hubungi admin jika lupa password.' : 'Pastikan semua baris formulir terisi dengan benar.';
                    $allErrors = implode('<br>', $errors->all());
                @endphp
                showModal("{{ $allErrors }}", 'error', "{{ $title }}", "{{ $hint }}");
            @endif
        });
    </script>
    @yield('scripts')
</body>
</html>
