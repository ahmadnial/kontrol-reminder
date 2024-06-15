<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use Illuminate\Http\Request;

class brodcastController extends Controller
{
    public function postBrodcastMessage(Request $request)
    {

        // foreach ($listAll as $itemX) {
        //     $noSukon = $itemX->noSuratKontrol;
        //     $noKa = $itemX->NoKartu;
        //     $fs_mr = $itemX->fs_mr;
        //     $nama = $itemX->Nama;
        //     $alamat = $itemX->fs_alm_pasien;
        //     $tglRencanaKontrol = $itemX->TglRencanaKontrol;
        //     $namaDokter = $itemX->NamaDokter;
        //     $fs_tlp_pasien = $itemX->fs_tlp_pasien;
        //     $tglExpired = $itemX->TglExpired;
        //     $Faskes = $itemX->Faskes;
        //     $jk = $itemX->fs_jns_kelamin;

        $curl = curl_init();
        $token = 'qbY!Uy3#L1H1A29Z8h+E';
        $message = 'SURAT RENCANA KONTROL
No. : ' . $request->no_sukon . '
BPJS : ' . $request->no_ka . '
Atas Nama : ' . $request->nama . '
RM : ' . substr($request->fs_mr, 9) . '  JK : ' . $request->jk . '
' . $request->alamat . '

Silakan untuk kontrol kembali ke dokter 
' . $request->nama_dokter . '
di RUMAH SAKIT NUR ROHMAH
pada Tanggal ' . $request->tgl_rencana_kontrol . '
Rujukan anda berakhir : ' . $request->tgl_expired . '
Jika anda kontrol setelah tanggal ' . $request->tgl_expired . ', Silahkan untuk mencari Rujukan ulang di PPK ' . $request->faskes . '

Terima kasih telah mempercayakan kesehatan anda pada kami, Semoga Lekas Sembuh :)';
        // }

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => '085974077234',
                'message' => $message,
                'delay' => '10',
                // 'schedule' => '1718003104',
                'countryCode' => '62', //optional
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: qbY!Uy3#L1H1A29Z8h+E'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // echo $response;

        // return back();
        if ($response) {
            return response()->json($response);
        }
    }
}
