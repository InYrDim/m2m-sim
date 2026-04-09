<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Timetable;
use App\Services\WhatsAppService;
use Carbon\Carbon;

class NotifyTeacherSchedule extends Command
{
    protected $signature = 'notif:guru-mengajar';
    protected $description = 'Notifikasi WhatsApp ke guru sebelum mengajar';

    public function handle()
    {
        \Log::info('CRON notif:guru-mengajar jalan', [
            'time' => now()->format('Y-m-d H:i:s')
        ]);

        $now = Carbon::now();
        $startWindow = $now->copy();
        $endWindow   = $now->copy()->addMinutes(10);
        $today       = $now->dayOfWeekIso; // 1 = Senin

        $jadwals = Timetable::with(['teacher', 'lesson', 'timeslot', 'classroom'])
            ->whereNotNull('teacher_code')
            ->where('day_id', $today)
            ->whereHas('timeslot', function ($q) use ($startWindow, $endWindow) {
                $q->whereBetween('time_start', [
                    $startWindow->format('H:i:s'),
                    $endWindow->format('H:i:s')
                ]);
            })
            ->where(function ($q) {
                $q->whereNull('last_notified_date')
                  ->orWhereDate('last_notified_date', '!=', today());
            })
            ->get();

        \Log::info('Jumlah jadwal ditemukan', [
            'count' => $jadwals->count(),
            'window_start' => $startWindow->format('H:i:s'),
            'window_end' => $endWindow->format('H:i:s'),
            'day' => $today
        ]);

        foreach ($jadwals as $j) {

            if (
                !$j->teacher ||
                !$j->teacher->phone ||
                !$j->lesson ||
                !$j->timeslot ||
                !$j->classroom
            ) {
                continue;
            }

            $phone = $this->formatPhone($j->teacher->phone);

            $jamMulai = \Carbon\Carbon::parse($j->timeslot->time_start)->format('H:i');
            $jamSelesai = \Carbon\Carbon::parse($j->timeslot->time_end)->format('H:i');

            $pesan = "📢 *NOTIFIKASI SESI MENGAJAR*\n\n"
                . "Yth. *{$j->teacher->name}*\n\n"
                . "📚 Mata Pelajaran : {$j->lesson->name}\n"
                . "🏫 Kelas : {$j->classroom->name}\n"
                . "⏰ Jam   : {$jamMulai} - {$jamSelesai}\n\n"
                . "Mohon kesediaan Bapak/Ibu untuk segera bersiap, karena waktu pembelajaran akan dimulai dalam 5 menit lagi.\n"
                . "Terima kasih 🙏";

            try {

                $response = WhatsAppService::send($phone, $pesan);

                // Update agar tidak terkirim lagi hari ini
                $j->update([
                    'last_notified_date' => today()
                ]);

                \Log::info('WA guru terkirim', [
                    'guru' => $j->teacher->name,
                    'phone' => $phone,
                    'time' => now()->format('H:i'),
                    'response' => $response,
                ]);

            } catch (\Throwable $e) {

                \Log::error('WA guru gagal', [
                    'guru' => $j->teacher->name ?? null,
                    'phone' => $phone ?? null,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info('Notifikasi guru berhasil diproses');

        return Command::SUCCESS;
    }

    private function formatPhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            return '62' . substr($phone, 1);
        }

        if (str_starts_with($phone, '62')) {
            return $phone;
        }

        return '62' . $phone;
    }
}
