<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappLog extends Model
{
    protected $table = "whatsapp_logs";
    protected $fillable = ["student_id", "guardian_id", "phone", "message", "status", "response", "sent_at"];
    protected $casts = ["sent_at" => "datetime"];

    public function student()  { return $this->belongsTo(Student::class); }
    public function guardian() { return $this->belongsTo(Guardian::class); }
}
