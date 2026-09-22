<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\ActivityLog; // 1. นำเข้า ActivityLog Model

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // บันทึก Log: อัปเดตโปรไฟล์ตนเอง
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'PROFILE_UPDATED',
            'description' => 'ผู้ใช้งานอัปเดตข้อมูลส่วนตัว/โปรไฟล์: ' . $user->email,
            'ip_address' => $request->ip(),
        ]);

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        $userName = $user->name;
        $userEmail = $user->email;
        $userId = $user->id;

        Auth::logout();

        $user->delete();

        // บันทึก Log: ลบบัญชีตนเอง
        ActivityLog::create([
            'user_id' => $userId,
            'action' => 'ACCOUNT_DELETED',
            'description' => 'ผู้ใช้งานลบบัญชีของตนเองออก1 บัญชี: ' . $userName . ' (' . $userEmail . ')',
            'ip_address' => $request->ip(),
        ]);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}