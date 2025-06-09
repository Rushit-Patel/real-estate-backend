<?php
namespace App\Http\Controllers;

use App\Models\UserActivity;
use Illuminate\Http\Request;

class UserActivityController extends Controller
{
    public function index(Request $request)
    {
        
        $activities = UserActivity::with('user')
            ->when($request->user_id, fn($query) => $query->where('user_id', $request->user_id))
            ->when($request->action, fn($query) => $query->where('action', $request->action))
            ->get();
        return response()->json($activities);
    }
}