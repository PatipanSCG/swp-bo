<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckMenuPermission
{
    public function handle($request, Closure $next, $menuKey)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }

        // ดึงข้อมูล permission ทั้งหมดจาก session
        $permissions = session('menu_permissions', []);

     // ดึง path ปัจจุบัน เช่น 'employees', 'stations/data'
        $currentPath = ltrim($request->path(), '/'); // ลบ '/' ด้านหน้า

        // ตรวจสอบว่า path นี้มีสิทธิ์หรือไม่
        $allowed = collect($permissions)->contains(function ($perm) use ($currentPath) {
            return strtolower(trim($perm->Route, '/')) === strtolower($currentPath);
        });

        \Log::info("Route {$currentPath} allowed? " . ($allowed ? 'YES' : 'NO'));
// dd($permissions);exit;
        if (!$allowed) {
            abort(403, 'ไม่มีสิทธิ์เข้าถึงเมนูนี้');
        }

        return $next($request);
    }
}
