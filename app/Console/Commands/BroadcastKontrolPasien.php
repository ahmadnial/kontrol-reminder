<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\WaBroadcastLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BroadcastKontrolPasien extends Command
{
    protected $signature = 'broadcast:kontrol';
    protected $description = 'Kirim broadcast WhatsApp otomatis untuk pasien kontrol besok';

    public function handle()
    {
        $targetDate = Carbon::now()->addDay(2)->format('Y-m-d');

        $list = DB::select("
            SELECT a.noSuratKontrol,a.TglTrs,a.NoSep,a.NoKartu,a.norujukan,a.Nama,a.TglRencanaKontrol,a.NamaDokter,
                   a.TglExpired, a.Faskes,b.fs_mr, b.fs_alm_pasien, b.fs_jns_kelamin, b.fs_tlp_pasien, c.FS_NM_LAYANAN 
            FROM VCLAIM_SKDP a 
            LEFT JOIN tc_mr b ON a.noMr = b.FS_MR
            LEFT JOIN TA_LAYANAN c ON a.PoliKontrol = c.FS_KD_LAYANAN
            WHERE a.tglvoid = '3000-01-01'
              AND a.NoSepKontrol=''
              AND a.isspri = 0 
              AND a.isskdphidok = 0 
              AND a.tglRencanaKontrol = '$targetDate'
        ");

        if (count($list) == 0) {
            Log::info("BroadcastKontrolPasien: tidak ada pasien untuk tanggal $targetDate");
            return 0;
        }

        foreach ($list as $row) {
            
            $tglKontrol = Carbon::parse($row->TglRencanaKontrol)->format('d-m-Y');
            $tglExpired = Carbon::parse($row->TglExpired)->format('d-m-Y');

            $rujukanExpired = ($row->NoSep != $row->norujukan);

            // ===== Pesan Dioptimasi =====
            $message = "Selamat Pagi Sahabat Sehat RS Nur Rohmah! 🌤️

Mengingatkan Bapak/Ibu/Sdr *{$row->Nama}*
{$row->fs_alm_pasien}

untuk periksa sesuai jadwal dengan dr. *{$row->NamaDokter}*
di *{$row->FS_NM_LAYANAN}* pada tanggal *$tglKontrol*.";

            if ($rujukanExpired) {
                $message .= "

❗ Rujukan anda berakhir pada *$tglExpired*
Jika kontrol **setelah $tglExpired**, silakan membuat rujukan ulang di PPK *{$row->Faskes}*.";
            }

            $message .= "

-----------------------------------
*Informasi Penting RSNR*
• Daftar Online Mobile JKN → https://bit.ly/registrasionline-rsnurrohmah
• Info jadwal dokter → Balas: jadwal
• Farmasi → http://wa.me/088902938721
• IGD 24 Jam → http://wa.me/6287733154169

Terima kasih, semoga lekas sembuh 😊";

            // ===================================
            // ===== Kirim WA via API Fonnte =====
            // ===================================

            $response = Http::withHeaders([
                'Authorization' => env('FONNTE_TOKEN'),
            ])->asMultipart()->post('https://api.fonnte.com/send', [
                // 'target' => $row->fs_tlp_pasien,
                'target' => '6285974077234',
                // 'target' => $row->fs_tlp_pasien,
                'message' => $message,
                'delay' => 5,
                'countryCode' => '62'
            ]);

            $status = $response->successful() ? 'success' : 'failed';

            // Log ke file laravel
            Log::info("Broadcast WA: {$row->fs_tlp_pasien} - $status - " . $response->body());

            // Log ke database
            WaBroadcastLog::create([
                'no_surat_kontrol' => $row->noSuratKontrol,
                'nama_pasien' => $row->Nama,
                'nomor_wa' => $row->fs_tlp_pasien,
                'layanan' => $row->FS_NM_LAYANAN,
                'dokter' => $row->NamaDokter,
                'tgl_rencana' => $row->TglRencanaKontrol,
                'tgl_expired' => $row->TglExpired,
                'rujukan_expired' => $rujukanExpired,
                'message' => $message,
                'status' => $status,
                'response' => $response->body(),
            ]);
        }

        $this->info("Broadcast selesai.");
        return 0;
    }
}
