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

                        <table id="example1" class="table table-hover" style="width: 100%; font-size: 13px;">

                            <thead style="background-color: rgb(236, 174, 97)">
                                <tr>
                                    <th>Nama</th>
                                    <th>No RM</th>
                                    <th>No SKDP</th>
                                    <th>No BPJS</th>
                                    <th class="">Tgl Kontrol</th>
                                    <th>Tujuan</th>
                                    <th>Layanan</th>
                                    <th class="">Expired</th>
                                    <th>PPK</th>
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
@endsection
