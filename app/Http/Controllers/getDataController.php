<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class getDataController extends Controller
{
    public function getDataPasien()
    {
        $dateNow = Carbon::now()->format('Y-m-d');
        $dateNowFull = Carbon::now();
        $maxDate = $dateNowFull->addDays(3)->format('Y-m-d');
        // dd($maxDate);
        $listAll = DB::select(" SELECT a.noSuratKontrol,a.TglTrs,a.NoSep,a.NoKartu,a.norujukan,a.Nama,a.TglRencanaKontrol,a.NamaDokter, a.TglExpired, a.Faskes,b.fs_mr, b.fs_alm_pasien, b.fs_jns_kelamin, b.fs_tlp_pasien, c.FS_NM_LAYANAN from VCLAIM_SKDP a 
                        left join tc_mr b on a.noMr = b.FS_MR
                        left join TA_LAYANAN c on a.PoliKontrol = c.FS_KD_LAYANAN
                        where a.tglvoid = '3000-01-01' and a.NoSepKontrol='' and a.isspri = 0 and a.isskdphidok = 0 and a.tglRencanaKontrol = '$maxDate' ");
        // return response()->json($listAll);

        return DataTables::of($listAll)
            ->addColumn('action', function ($row) {
                $today = Carbon::today()->toDateString();
                $actionBtn = '
                <button class="sendmessage btn btn-sm btn-success" data-toggle="modal" data-target="#EditObat"
                onclick="sendNotification(this)"
                data-no_sukon="' . $row->noSuratKontrol . '"
                data-no_sep="' . $row->NoSep . '"
                data-no_rujukan="' . $row->norujukan . '"
                data-no_ka="' . $row->NoKartu . '"
                data-fs_mr="' . $row->fs_mr . '"
                data-nama="' . $row->Nama . '"
                data-alamat="' . $row->fs_alm_pasien . '"
                data-tgl_rencana_kontrol="' . $row->TglRencanaKontrol . '"
                data-nama_dokter="' . $row->NamaDokter . '"
                data-fs_tlp_pasien="' . $row->fs_tlp_pasien . '"
                data-tgl_expired="' . $row->TglExpired . '"
                data-faskes="' . $row->Faskes . '"
                data-jk="' . $row->fs_jns_kelamin . '"
                data-layanan="' . $row->FS_NM_LAYANAN . '"
                ><i class="fa fa-paper-plane">&nbsp;</i></button>
                ';

                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getDataPasienNonJkn()
    {
        $dateNow = Carbon::now()->format('Y-m-d');
        $dateNowFull = Carbon::now();
        $maxDate = $dateNowFull->addDays(3)->format('Y-m-d');
        // dd($maxDate);
        $listAll = DB::select(" SELECT b.fs_nm_pasien, b.fs_alm_pasien, e.fs_nm_tipe_jaminan, a.fs_mr , a.fs_kd_reg, a.fs_no_skdp, c.fs_nm_layanan, a.fd_tgl_kontrol, a.fs_nm_dpjp, b.fs_tlp_pasien
                                from tz_skdp_histori a
                                left join tc_mr b on a.fs_mr = b.fs_mr
                                left join TA_LAYANAN c on a.fs_kd_layanan = c.fs_kd_layanan
                                left join TA_REGISTRASI d on a.fs_kd_reg=d.fs_kd_reg
                                left join TA_TIPE_JAMINAN e on d.fs_kd_tipe_jaminan = e.fs_kd_tipe_jaminan
                                where a.fb_aktif='1'
                                ");
                                // and a.fd_tgl_kontrol='$maxDate'
        // return response()->json($listAll);

        return DataTables::of($listAll)
            ->addColumn('action', function ($row) {
                $today = Carbon::today()->toDateString();
                $actionBtn = '
                <button class="sendmessage btn btn-sm btn-success" data-toggle="modal" data-target="#EditObat"
                onclick="sendNotificationNonJkn(this)"
                data-no_sukon="' . $row->fs_no_skdp . '"
                data-fs_mr="' . $row->fs_mr . '"
                data-fs_nm_pasien="' . $row->fs_nm_pasien . '"
                data-fs_alm_pasien="' . $row->fs_alm_pasien . '"
                data-fd_tgl_kontrol="' . $row->fd_tgl_kontrol . '"
                data-fs_nm_dpjp="' . $row->fs_nm_dpjp . '"
                data-fs_nm_layanan="' . $row->fs_nm_layanan . '"
                data-fs_tlp_pasien="' . $row->fs_tlp_pasien . '"
                ><i class="fa fa-paper-plane">&nbsp;</i></button>
                ';

                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
