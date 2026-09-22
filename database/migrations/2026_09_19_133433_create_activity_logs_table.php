<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            // เชื่อมโยงกับตาราง users (ถ้าผู้ใช้ถูกลบ ให้ตั้งค่าเป็น null)
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            
            $table->string('action');        // ประเภทการกระทำ เช่น CREATED, UPDATED, DELETED, STOCK IN
            $table->text('description');     // รายละเอียดสิ่งที่ทำ เช่น "เพิ่มสินค้าใหม่: ปากกาเคมีสีดำ"
            $table->string('ip_address')->nullable(); // บันทึก IP Address ของผู้ใช้
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};