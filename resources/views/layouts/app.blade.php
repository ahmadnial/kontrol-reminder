<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'laravel') }}</title>

    <!-- Fonts -->
    {{-- <link rel="dns-prefetch" href="//fonts.bunny.net"> --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    {{-- <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet"> --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        body {
            font-family: serif;
        }

        #id {
            font-family: serif;
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/login') }}">
                    {{ config('app.name', 'laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                {{-- <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li> --}}
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
             <div class="container-fluid mb-2">
                <a class="btn btn-sm btn-warning" href="{{ url('home') }}">JKN</a>
                <a class="btn btn-sm btn-warning" href="{{ url('non-jkn') }}">Non-JKN</a>
            </div>
            @yield('content')
        </main>
    </div>

    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    {{-- <script src="https://cdn.datatables.net/plug-ins/2.0.8/dataRender/datetime.js"></script> --}}
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.26.0/moment.min.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.10.21/dataRender/datetime.js"></script>
    <script>
        window.onload = function() {

        };

        function sendAllOurLove() {
            var button = document.getElementsByClassName('Sendmessage');
            setTimeout(() => {
                $('.sendmessage').click();
            }, 1000);
        }

        getMonthSale()

        function getMonthSale() {
            // const dataBulan = $('#monthSales').val();
            $.ajax({
                success: function() {
                    $('#example1').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        paging: false,
                        "bDestroy": true,
                        "order": [
                            [4, "asc"]
                        ],

                        ajax: {
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: "{{ url('getDataPasien') }}",
                            type: 'GET',
                            // data: {
                            //     dataBulan: dataBulan
                            // }
                        },
                        columns: [{
                                data: 'Nama',
                                name: 'Nama',
                            },
                            {
                                data: 'fs_mr',
                                name: 'fs_mr'
                            },
                            {
                                data: 'noSuratKontrol',
                                name: 'noSuratKontrol',
                            },
                            {
                                data: 'NoKartu',
                                name: 'NoKartu',
                            },
                            {
                                data: 'TglRencanaKontrol',
                                name: 'TglRencanaKontrol',
                                render: function(data, type, row) {
                                    return moment(data).format('D MMMM YYYY');
                                }
                            },
                            {
                                data: 'NamaDokter',
                                name: 'NamaDokter'
                            },
                            {
                                data: 'FS_NM_LAYANAN',
                                name: 'FS_NM_LAYANAN'
                            },
                            {
                                data: 'TglExpired',
                                name: 'TglExpired',
                                render: function(data, type, row) {
                                    return moment(data).format('D MMMM YYYY');
                                }
                            },
                            {
                                data: 'Faskes',
                                name: 'Faskes',
                            },
                            {
                                data: 'action',
                                name: 'action'
                            },
                        ],

                    })
                }
            })
        };

        function sendNotification(xx) {
            var no_sukon = $(xx).data('no_sukon');
            var no_sep = $(xx).data('no_sep');
            var no_rujukan = $(xx).data('no_rujukan');
            var no_ka = $(xx).data('no_ka');
            var fs_mr = $(xx).data('fs_mr');
            var nama = $(xx).data('nama');
            var alamat = $(xx).data('alamat');
            var tgl_rencana_kontrol = $(xx).data('tgl_rencana_kontrol');
            var nama_dokter = $(xx).data('nama_dokter');
            var layanan = $(xx).data('layanan');
            var fs_tlp_pasien = $(xx).data('fs_tlp_pasien');
            var tgl_expired = $(xx).data('tgl_expired');
            var faskes = $(xx).data('faskes');
            var jk = $(xx).data('jk');

            // alert(alamat)
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ url('postBrodcastMessage') }}",
                type: "POST",
                data: {
                    no_sukon: no_sukon,
                    no_sep: no_sep,
                    no_rujukan: no_rujukan,
                    no_ka: no_ka,
                    fs_mr: fs_mr,
                    nama: nama,
                    alamat: alamat,
                    tgl_rencana_kontrol: tgl_rencana_kontrol,
                    nama_dokter: nama_dokter,
                    layanan: layanan,
                    fs_tlp_pasien: fs_tlp_pasien,
                    tgl_expired: tgl_expired,
                    faskes: faskes,
                    jk: jk,
                },
                success: function(response) {
                    console.log(response);
                }
            });
        }
    </script>
    @stack('javscript')
</body>

</html>
