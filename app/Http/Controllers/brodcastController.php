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
        if ($request->no_sep == $request->no_rujukan) {
            $message = 'SURAT RENCANA KONTROL
No. : ' . $request->no_sukon . '
BPJS : ' . $request->no_ka . '
Atas Nama : ' . $request->nama . '
RM : ' . substr($request->fs_mr, 9) . '  JK : ' . $request->jk . '
' . $request->alamat . '

Silakan untuk kontrol kembali ke dokter 
' . $request->nama_dokter . '
di RUMAH SAKIT NUR ROHMAH
pada Tanggal ' . date("d-m-Y ", strtotime($request->tgl_rencana_kontrol)) . '

Terima kasih telah mempercayakan kesehatan anda pada kami, Semoga Lekas Sembuh :)

Panel Informasi ⬇️

1. Informasi Jadwal Praktek Dokter balas pesan ini dengan mengetikan "jadwal" (tanpa tanda kutip)

2. Konsultasi TELEPHONE RSNR (0274)394574

3. Emergency UGD ➡️ http://wa.me/6287733154169

4. KONSULTASI FARMASI ➡️ http://wa.me/088902938721

5.LAYANAN INFORMASI ➡️ http://wa.me/6283854014057

6.PENDAFTARAN ONLINE BPJS MELALUI MOBILE JKN 

7.Pendaftaran pasien umum bisa mengirim foto KTP dan nomor telf

Note : Mohon maaf kami tidak menerima Telepone seluler atau wa🙏🙏🙏
';
        } else {
            $message = 'SURAT RENCANA KONTROL
No. : ' . $request->no_sukon . '
BPJS : ' . $request->no_ka . '
Atas Nama : ' . $request->nama . '
RM : ' . substr($request->fs_mr, 9) . '  JK : ' . $request->jk . '
' . $request->alamat . '

Silakan untuk kontrol kembali ke dokter 
' . $request->nama_dokter . '
di RUMAH SAKIT NUR ROHMAH
pada Tanggal ' . date("d-m-Y ", strtotime($request->tgl_rencana_kontrol)) . '
Rujukan anda berakhir : ' . date("d-m-Y ", strtotime($request->tgl_expired)) . '
Jika anda kontrol setelah tanggal ' . date("d-m-Y ", strtotime($request->tgl_expired)) . ', Silahkan untuk mencari Rujukan ulang di PPK ' . $request->faskes . '

Terima kasih telah mempercayakan kesehatan anda pada kami, Semoga Lekas Sembuh :)

Panel Informasi ⬇️

1. Informasi Jadwal Praktek Dokter balas pesan ini dengan mengetikan "jadwal" (tanpa tanda kutip)

2. Konsultasi TELEPHONE RSNR (0274)394574

3. Emergency UGD ➡️ http://wa.me/6287733154169

4. KONSULTASI FARMASI ➡️ http://wa.me/088902938721

5.LAYANAN INFORMASI ➡️ http://wa.me/6283854014057

6.PENDAFTARAN ONLINE BPJS MELALUI MOBILE JKN 

7.Pendaftaran pasien umum bisa mengirim foto KTP dan nomor telf

Note : Mohon maaf kami tidak menerima Telepone seluler atau wa🙏🙏🙏
';
        }
        $curl = curl_init();
        $token = '-iwXrDQw9Yt9NZtZXX1d';

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
                'target' => $request->fs_tlp_pasien,
                'message' => $message,
                // 'buttonJSON' => '{"buttons":[{"id":"mybutton1","message":"hello fonnte"},{"id":"mybutton2","message":"fonnte pricing"},{"id":"mybutton3","message":"tutorial fonnte"}]}',
                'delay' => '10',
                // 'schedule' => '1718003104',
                'countryCode' => '62', //optional
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: -iwXrDQw9Yt9NZtZXX1d'
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
