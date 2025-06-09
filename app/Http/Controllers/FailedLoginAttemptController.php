<?php
namespace App\Http\Controllers;

use App\Models\FailedLoginAttempt;
use Illuminate\Http\Request;

class FailedLoginAttemptController extends Controller
{
    public function index(Request $request)
    {
        
        $attempts = FailedLoginAttempt::when($request->username, fn($query) => $query->where('username', $request->username))
            ->when($request->ip_address, fn($query) => $query->where('ip_address', $request->ip_address))
            ->get();
        return response()->json($attempts);
    }
}