<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// เพิ่ม 'role' เข้าไปใน Fillable เพื่อให้สามารถบันทึกค่าสิทธิ์ลงฐานข้อมูลได้
#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * แปลงค่า role ให้เป็นตัวพิมพ์เล็กเสมอ (ป้องกันปัญหาพิมพ์ใหญ่-พิมพ์เล็ก)
     */
    protected function role(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => strtolower($value ?? 'user'),
            set: fn ($value) => strtolower($value ?? 'user'),
        );
    }
}