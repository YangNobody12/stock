<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'ip_address',
    ];

    // ความสัมพันธ์เชื่อมกับตาราง users เพื่อดึงชื่อผู้ทำรายการ
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}