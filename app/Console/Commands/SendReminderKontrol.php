<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendReminderKontrol extends Command
{
    protected $signature = 'wa:reminder-kontrol';
    protected $description = 'Kirim otomatis WA Reminder Kontrol H-2 ke Pasien dan Laporan ke Admin';

    public function handle()
    {
        // 1. ARMOR MUTLAK: Cegah script PHP mati karena eksekusi terlalu lama
        set_time_limit(0);

        $dateNowFull = Carbon::now();
        $maxDate = $dateNowFull->addDays(2)->format('Y-m-d');
        
        $this->info("Memulai proses tarik data untuk tanggal kontrol: " . $maxDate);

        // Tarik Data Pasien
        $listAll = DB::select(" SELECT a.noSuratKontrol,a.TglTrs,a.NoSep,a.NoKartu,a.norujukan,a.Nama,a.TglRencanaKontrol,a.NamaDokter, a.TglExpired, a.Faskes,b.fs_mr, b.fs_alm_pasien, b.fs_jns_kelamin, b.fs_tlp_pasien, c.FS_NM_LAYANAN from VCLAIM_SKDP a 
                        left join tc_mr b on a.noMr = b.FS_MR
                        left join TA_LAYANAN c on a.PoliKontrol = c.FS_KD_LAYANAN
                        where a.tglvoid = '3000-01-01' and a.NoSepKontrol='' and a.isspri = 0 and a.isskdphidok = 0 and a.tglRencanaKontrol = '$maxDate' ");

        $token = '-iwXrDQw9Yt9NZtZXX1d'; // Token Fonnte
        $no_wa_admin = '6285974077234'; 
        
        $totalTarget = count($listAll);
        $sukses = 0;
        $gagal = 0;

        $this->info("Total target pengiriman: $totalTarget pasien. Memulai Broadcast dengan mode Anti-Banned...");

        // 2. Looping Pengiriman Pesan ke Pasien
        foreach ($listAll as $row) {
            // Lewati jika nomor telepon kosong atau terlalu pendek
            if (empty($row->fs_tlp_pasien) || strlen($row->fs_tlp_pasien) < 9) {
                $gagal++;
                continue; 
            }

            // Logika Pesan Pasien
            if ($row->NoSep == $row->norujukan) {
                $message = "Selamat Pagi Sahabat Sehat RS Nur Rohmah!\nMengingatkan Bapak/Ibu/Saudara/Saudari *". $row->Nama ."* \n" . $row->fs_alm_pasien . "\nuntuk periksa sesuai Jadwal dengan Dokter *" . $row->NamaDokter . "* di *" . $row->FS_NM_LAYANAN . "* pada tanggal " . date("d-m-Y ", strtotime($row->TglRencanaKontrol)) . "\n\n-----------------------------------\n\nPanel Informasi\n* Pendaftaran Online Via Mobile JKN melalui link berikut https://bit.ly/registrasionline-rsnurrohmah\n* Informasi Farmasi http://wa.me/088902938721\n* Informasi Layanan Gawat Darurat (IGD) http://wa.me/6287733154169\n\nTerima kasih telah mempercayakan kesehatan anda pada kami, Semoga Lekas Sembuh ☺️";
            } else {
                $message = "Selamat Pagi Sahabat Sehat RS Nur Rohmah!\nMengingatkan Bapak/Ibu/Saudara/Saudari *" . $row->Nama . "* \n" . $row->fs_alm_pasien . "\nuntuk periksa sesuai Jadwal dengan Dokter *" . $row->NamaDokter . "* di *" . $row->FS_NM_LAYANAN . "* pada tanggal " . date("d-m-Y ", strtotime($row->TglRencanaKontrol)) . "\n\nRujukan anda berakhir pada " . date("d-m-Y ", strtotime($row->TglExpired)) . " \nJika anda periksa setelah tanggal " . date("d-m-Y ", strtotime($row->TglExpired)) . "\nSilahkan untuk mencari Rujukan ulang di PPK *" . $row->Faskes . "*\n\n-----------------------------------\n\nPanel Informasi\n* Pendaftaran Online Via Mobile JKN melalui link berikut https://bit.ly/registrasionline-rsnurrohmah\n* Informasi Farmasi http://wa.me/088902938721\n* Informasi Layanan Gawat Darurat (IGD) http://wa.me/6287733154169\n\nTerima kasih telah mempercayakan kesehatan anda pada kami, Semoga Lekas Sembuh ☺️";
            }

            // Eksekusi cURL ke Pasien
            $this->kirimPesanFonnte($row->fs_tlp_pasien, $message, $token);
            $sukses++;
            
            // --- ALGORITMA ANTI-SPAM (HUMANIZER) DIMULAI DI SINI ---

            // A. Jeda Acak (Random Sleep) antar 5 sampai 12 detik
            $waktu_jeda = rand(5, 12); 
            $this->info("Pesan terkirim ke {$row->fs_tlp_pasien}. Jeda acak: {$waktu_jeda} detik...");
            sleep($waktu_jeda);

            // B. Sistem Batching (Istirahat panjang setiap kelipatan 25 pesan)
            if ($sukses > 0 && $sukses % 25 == 0) {
                $this->info("Telah mengirim 25 pesan. Robot istirahat 60 detik agar tidak diblokir WA...");
                sleep(60); // Jeda 1 menit penuh
            }
            // --------------------------------------------------------
        }

        // 3. KUMPULKAN REKAP & KIRIM KE ADMIN
        $tanggalSekarang = Carbon::now()->format('d M Y H:i');
        $laporanAdmin = "🤖 *Laporrr Mas Ahmad, dari Jarvis Cronjob RSNR*\n_Reminder Kontrol H-2_\n\n🗓 Tanggal Run: $tanggalSekarang\n🎯 Tanggal Kontrol: " . date("d-m-Y", strtotime($maxDate)) . "\n\n📊 *REKAPITULASI:*\n- Total Target: *$totalTarget Pasien*\n- ✅ Berhasil Dikirim: *$sukses*\n- ❌ Gagal/No Kosong: *$gagal*\n\n_Laporan Selesai Mas Ahmad,Terimakasih_";

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