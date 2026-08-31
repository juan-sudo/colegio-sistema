<?php
namespace App\Console\Commands;

use App\Models\Attendance;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;

class SendAbsenceAlerts extends Command
{
    protected $signature = "asistencia:enviar-alertas";
    protected $description = "Envía WhatsApp a los padres de alumnos marcados como falta y aún no notificados";

    public function handle(WhatsAppService $whatsapp): int
    {
        $faltas = Attendance::where("status", "falta")
            ->where("guardian_notified", false)
            ->where("date", now()->toDateString())
            ->get();

        foreach ($faltas as $attendance) {
            $whatsapp->notificarFalta($attendance->student, $attendance);
            $this->info("Notificado: {$attendance->student->fullName()}");
        }

        $this->info("Total notificados: {$faltas->count()}");
        return self::SUCCESS;
    }
}
