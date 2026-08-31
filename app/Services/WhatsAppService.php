<?php
namespace App\Services;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\WhatsappLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Servicio de envío de WhatsApp.
 *
 * Soporta 3 proveedores intercambiables vía .env (WHATSAPP_PROVIDER):
 *   - twilio      (Twilio WhatsApp API)
 *   - meta        (WhatsApp Business Cloud API de Meta)
 *   - ultramsg    (Ultramsg - más simple/económico)
 *
 * Configura las credenciales en config/services.php + .env
 */
class WhatsAppService
{
    public function notificarFalta(Student $student, Attendance $attendance): void
    {
        foreach ($student->guardians as $guardian) {
            $mensaje = sprintf(
                "Estimado(a) %s, le informamos que su hijo(a) %s no registró asistencia el día %s en el colegio. Si esto es un error o cuenta con justificación, por favor comuníquese con el centro educativo.",
                $guardian->fullName(),
                $student->fullName(),
                $attendance->date->format("d/m/Y")
            );

            $this->enviar($guardian->phone_whatsapp, $mensaje, $student->id, $guardian->id);
        }

        $attendance->update(["guardian_notified" => true]);
    }

    public function enviar(string $telefono, string $mensaje, ?int $studentId = null, ?int $guardianId = null): bool
    {
        $log = WhatsappLog::create([
            "student_id" => $studentId,
            "guardian_id" => $guardianId,
            "phone" => $telefono,
            "message" => $mensaje,
            "status" => "pendiente",
        ]);

        try {
            $provider = config("services.whatsapp.provider", "ultramsg");

            $response = match ($provider) {
                "twilio" => $this->enviarTwilio($telefono, $mensaje),
                "meta" => $this->enviarMeta($telefono, $mensaje),
                default => $this->enviarUltramsg($telefono, $mensaje),
            };

            $log->update([
                "status" => $response->successful() ? "enviado" : "error",
                "response" => $response->body(),
                "sent_at" => now(),
            ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error("Error enviando WhatsApp: " . $e->getMessage());
            $log->update(["status" => "error", "response" => $e->getMessage()]);
            return false;
        }
    }

    private function enviarUltramsg(string $telefono, string $mensaje)
    {
        $instanceId = config("services.whatsapp.ultramsg_instance");
        $token = config("services.whatsapp.ultramsg_token");

        return Http::asForm()->post("https://api.ultramsg.com/{$instanceId}/messages/chat", [
            "token" => $token,
            "to" => $telefono,
            "body" => $mensaje,
        ]);
    }

    private function enviarTwilio(string $telefono, string $mensaje)
    {
        $sid = config("services.whatsapp.twilio_sid");
        $token = config("services.whatsapp.twilio_token");
        $from = config("services.whatsapp.twilio_from"); // ej: whatsapp:+14155238886

        return Http::withBasicAuth($sid, $token)->asForm()
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                "From" => $from,
                "To" => "whatsapp:{$telefono}",
                "Body" => $mensaje,
            ]);
    }

    private function enviarMeta(string $telefono, string $mensaje)
    {
        $phoneNumberId = config("services.whatsapp.meta_phone_id");
        $token = config("services.whatsapp.meta_token");

        return Http::withToken($token)->post("https://graph.facebook.com/v19.0/{$phoneNumberId}/messages", [
            "messaging_product" => "whatsapp",
            "to" => $telefono,
            "type" => "text",
            "text" => ["body" => $mensaje],
        ]);
    }
}
