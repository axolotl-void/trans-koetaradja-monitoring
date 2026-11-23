<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Monitoring Trans Koetaradja')</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/variables.css') }}?v={{ filemtime(public_path('assets/css/variables.css')) }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}?v={{ filemtime(public_path('assets/css/app.css')) }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @stack('styles')
</head>

<body>

    <div class="app-shell">

        @include('partials.sidebar')

        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <div class="main-area">

            @include('partials.topbar')

            <main class="content">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        @foreach($errors->all() as $error)
                            {{ $error }}<br>
                        @endforeach
                    </div>
                @endif

                @yield('content')
            </main>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>

    <script>
        // toggle sidebar untuk layar kecil
        (function () {
            var btn = document.querySelector('.btn-menu');
            var sidebar = document.querySelector('.sidebar');
            var backdrop = document.getElementById('sidebarBackdrop');

            if (!btn || !sidebar) {
                return;
            }

            function close() {
                sidebar.classList.remove('open');
                if (backdrop) {
                    backdrop.classList.remove('open');
                }
            }

            btn.addEventListener('click', function () {
                var open = sidebar.classList.toggle('open');
                if (backdrop) {
                    backdrop.classList.toggle('open', open);
                }
            });

            if (backdrop) {
                backdrop.addEventListener('click', close);
            }

            var closeBtn = document.querySelector('.sidebar-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', close);
            }

            // Escape menutup drawer
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    close();
                }
            });
        })();
    </script>

    <script src="{{ asset('assets/js/form.js') }}?v={{ filemtime(public_path('assets/js/form.js')) }}" defer></script>
    <script src="{{ asset('assets/js/topbar.js') }}?v={{ filemtime(public_path('assets/js/topbar.js')) }}" defer></script>

    @stack('scripts')

</body>
</html>
