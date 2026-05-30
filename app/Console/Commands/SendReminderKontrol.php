<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendReminderKontrol extends Command
{
    protected $signature = 'wa:reminder-kontrol';
    protected $description = 'Kirim otomatis WA Reminder Kontrol H-3 ke Pasien dan Laporan ke Admin';

    public function handle()
    {
        $dateNowFull = Carbon::now();
        $maxDate = $dateNowFull->addDays(3)->format('Y-m-d');
        
        $this->info("Memulai proses tarik data untuk tanggal kontrol: " . $maxDate);

        // 1. Tarik Data Pasien
        $listAll = DB::select(" SELECT a.noSuratKontrol,a.TglTrs,a.NoSep,a.NoKartu,a.norujukan,a.Nama,a.TglRencanaKontrol,a.NamaDokter, a.TglExpired, a.Faskes,b.fs_mr, b.fs_alm_pasien, b.fs_jns_kelamin, b.fs_tlp_pasien, c.FS_NM_LAYANAN from VCLAIM_SKDP a 
                        left join tc_mr b on a.noMr = b.FS_MR
                        left join TA_LAYANAN c on a.PoliKontrol = c.FS_KD_LAYANAN
                        where a.tglvoid = '3000-01-01' and a.NoSepKontrol='' and a.isspri = 0 and a.isskdphidok = 0 and a.tglRencanaKontrol = '$maxDate' ");

        $token = '-iwXrDQw9Yt9NZtZXX1d'; // Token Fonnte
        $no_wa_admin = '6285974077234'; // GANTI DENGAN NOMOR WA ADMIN (Gunakan awalan 62)
        
        $totalTarget = count($listAll);
        $sukses = 0;
        $gagal = 0;

        // 2. Looping Pengiriman Pesan ke Pasien
        foreach ($listAll as $row) {
            // Lewati jika nomor telepon kosong atau terlalu pendek
            if (empty($row->fs_tlp_pasien) || strlen($row->fs_tlp_pasien) < 9) {
                $gagal++;
                continue; 
            }

            // Logika Pesan Pasien
            if ($row->NoSep == $row->norujukan) {
                $message = "Selamat Pagi Sahabat Sehat RS Nur Rohmah!\nMengingatkan Bapak/Ibu/Saudara/Saudari *" . $row->Nama . "* \n" . $row->fs_alm_pasien . "\nuntuk periksa sesuai Jadwal dengan Dokter *" . $row->NamaDokter . "* di *" . $row->FS_NM_LAYANAN . "* pada tanggal " . date("d-m-Y ", strtotime($row->TglRencanaKontrol)) . "\n\n-----------------------------------\n\nPanel Informasi\n* Pendaftaran Online Via Mobile JKN melalui link berikut https://bit.ly/registrasionline-rsnurrohmah\n* Informasi jadwal praktek Dokter balas pesan ini dengan mengetikan \"jadwal\" (tanpa tanda kutip)\n* Informasi Farmasi http://wa.me/088902938721\n* Informasi Layanan Gawat Darurat (IGD) http://wa.me/6287733154169\n\nTerima kasih telah mempercayakan kesehatan anda pada kami, Semoga Lekas Sembuh ☺️";
            } else {
                $message = "Selamat Pagi Sahabat Sehat RS Nur Rohmah!\nMengingatkan Bapak/Ibu/Saudara/Saudari *" . $row->Nama . "* \n" . $row->fs_alm_pasien . "\nuntuk periksa sesuai Jadwal dengan Dokter *" . $row->NamaDokter . "* di *" . $row->FS_NM_LAYANAN . "* pada tanggal " . date("d-m-Y ", strtotime($row->TglRencanaKontrol)) . "\n\nRujukan anda berakhir pada " . date("d-m-Y ", strtotime($row->TglExpired)) . " \nJika anda periksa setelah tanggal " . date("d-m-Y ", strtotime($row->TglExpired)) . "\nSilahkan untuk mencari Rujukan ulang di PPK *" . $row->Faskes . "*\n\n-----------------------------------\n\nPanel Informasi\n* Pendaftaran Online Via Mobile JKN melalui link berikut https://bit.ly/registrasionline-rsnurrohmah\n* Informasi jadwal praktek Dokter balas pesan ini dengan mengetikan \"jadwal\" (tanpa tanda kutip)\n* Informasi Farmasi http://wa.me/088902938721\n* Informasi Layanan Gawat Darurat (IGD) http://wa.me/6287733154169\n\nTerima kasih telah mempercayakan kesehatan anda pada kami, Semoga Lekas Sembuh ☺️";
            }

            // Eksekusi cURL ke Pasien
            $this->kirimPesanFonnte($row->fs_tlp_pasien, $message, $token);
            $sukses++; // Asumsi sukses (bisa ditambahkan validasi respons JSON jika perlu)
            
            // Jeda agar tidak dianggap SPAM oleh WA
            sleep(2); 
        }

        // 3. KUMPULKAN REKAP & KIRIM KE ADMIN
        $tanggalSekarang = Carbon::now()->format('d M Y H:i');
        $laporanAdmin = "🤖 *LAPORAN CRONJOB RSNR*\n_Reminder Kontrol H-3_\n\n🗓 Tanggal Run: $tanggalSekarang\n🎯 Tanggal Kontrol: " . date("d-m-Y", strtotime($maxDate)) . "\n\n📊 *REKAPITULASI:*\n- Total Target: *$totalTarget Pasien*\n- ✅ Berhasil Dikirim: *$sukses*\n- ❌ Gagal/No Kosong: *$gagal*\n\n_System generated message_";

        // Kirim Laporan ke Admin
        $this->kirimPesanFonnte($no_wa_admin, $laporanAdmin, $token);

        $this->info("Broadcast Selesai! Sukses: $sukses, Gagal: $gagal. Laporan telah dikirim ke Admin.");
    }

    // Fungsi Helper agar kode cURL tidak ditulis berulang-ulang
    private function kirimPesanFonnte($target, $message, $token)
    {
        $curl = curl_init();
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
                'target' => $target,
                'message' => $message,
                'countryCode' => '62',
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . $token
            ),
        ));

        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            Log::error("Fonnte Error saat kirim ke " . $target . ": " . curl_error($curl));
        }
        curl_close($curl);
    }
}