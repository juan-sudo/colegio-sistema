<?php
namespace App\Console\Commands;

use App\Jobs\SendAttendanceAlertJob;
use App\Models\Attendance;
use Illuminate\Console\Command;

class SendAbsenceAlerts extends Command
{
    protected $signature = "asistencia:enviar-alertas";
    protected $description = "Envía WhatsApp a los padres de alumnos marcados como falta y aún no notificados";

    public function handle(): int
    {
        $faltas = Attendance::where("status", "falta")
            ->where("guardian_notified", false)
            ->where("date", now()->toDateString())
            ->get();

        foreach ($faltas as $attendance) {
            SendAttendanceAlertJob::dispatch($attendance);
        }

        $this->info("Total encolados: {$faltas->count()}");
        return self::SUCCESS;
    }
}
