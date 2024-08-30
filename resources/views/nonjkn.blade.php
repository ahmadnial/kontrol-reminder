@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col">
                <div class="card">
                    <div class="card-header">{{ __('Dashboard') }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <table id="example2" class="table table-hover" style="width: 100%; font-size: 13px;">

                            <thead style="background-color: rgb(236, 174, 97)">
                                <tr>
                                    <th>Nama</th>
                                    <th>No RM</th>
                                    <th>No Surat</th>
                                    <th>Jaminan</th>
                                    <th class="">Tgl Kontrol</th>
                                    <th>Layanan</th>
                                    <th>Nama Medis</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                        <div class="float-right">
                            <button type="button" class="btn btn-sm btn-primary" onclick="sendAllOurLove()"><i
                                    class="fa fa-plane"></i>&nbsp;&nbsp;Send
                                Love to All Awsem Vibes</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@push('javscript')
    <script>
        getDataPasienNonJkn();

        function getDataPasienNonJkn() {
            // const dataBulan = $('#monthSales').val();
            $.ajax({
                success: function() {
                    $('#example2').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        paging: false,
                        "bDestroy": true,
                        // "order": [
                        //     [4, "asc"]
                        // ],

                        ajax: {
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: "{{ url('getDataPasienNonJkn') }}",
                            type: 'GET',
                            // data: {
                            //     dataBulan: dataBulan
                            // }
                        },
                        columns: [{
                                data: 'fs_nm_pasien',
                                name: 'fs_nm_pasien',
                            },
                            {
                                data: 'fs_mr',
                                name: 'fs_mr'
                            },
                            {
                                data: 'fs_no_skdp',
                                name: 'fs_no_skdp',
                            },
                            {
                                data: 'fs_nm_tipe_jaminan',
                                name: 'fs_nm_tipe_jaminan',
                            },
                            {
                                data: 'fd_tgl_kontrol',
                                name: 'fd_tgl_kontrol',
                                render: function(data, type, row) {
                                    return moment(data).format('D MMMM YYYY');
                                }
                            },
                            {
                                data: 'fs_nm_layanan',
                                name: 'fs_nm_layanan',
                            },
                            {
                                data: 'fs_nm_dpjp',
                                name: 'fs_nm_dpjp'
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

        function sendNotificationNonJkn(xx) {
            var no_sukon = $(xx).data('no_sukon');
            var fs_mr = $(xx).data('fs_mr');
            var fs_nm_pasien = $(xx).data('fs_nm_pasien');
            var fs_alm_pasien = $(xx).data('fs_alm_pasien');
            var fd_tgl_kontrol = $(xx).data('fd_tgl_kontrol');
            var fs_nm_dpjp = $(xx).data('fs_nm_dpjp');
            var fs_nm_layanan = $(xx).data('fs_nm_layanan');
            var fs_tlp_pasien = $(xx).data('fs_tlp_pasien');
            // alert(alamat)
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ url('BrodcastMessageNonJkn') }}",
                type: "POST",
                data: {
                    no_sukon: no_sukon,
                    fs_mr: fs_mr,
                    fs_nm_pasien: fs_nm_pasien,
                    fs_alm_pasien: fs_alm_pasien,
                    fd_tgl_kontrol: fd_tgl_kontrol,
                    fs_nm_dpjp: fs_nm_dpjp,
                    fs_nm_layanan: fs_nm_layanan,
                    fs_tlp_pasien: fs_tlp_pasien,
                },
                success: function(response) {
                    console.log(response);
                }
            });
        }
    </script>
@endpush
@endsection

