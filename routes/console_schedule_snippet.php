<?php
// Agregar esto en routes/console.php de tu proyecto Laravel para que
// corra automáticamente todos los días a las 9:00am (requiere el
// scheduler de Laravel corriendo vía cron, ver README).
use Illuminate\Support\Facades\Schedule;

Schedule::command("asistencia:enviar-alertas")->dailyAt("09:00");
