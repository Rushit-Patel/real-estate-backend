<?php
namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\FcmTokens;
use Illuminate\Http\Request;
use Validator;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = Notification::where('user_id', $request->user()->id)->get();
        return response()->json($notifications);
    }

    public function markAsRead(Request $request, Notification $notification)
    {
        if ($notification->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $notification->update(['read_at' => now()]);
        return response()->json(['message' => 'Notification marked as read']);
    }

    public function markAllAsRead(Request $request)
    {
        Notification::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'All notifications marked as read']);
    }

    public function fcmTokens(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'device_name' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!$request->user()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = $request->only(['token', 'device_name']);
        $data['user_id'] = $request->user()->id;

        FcmTokens::create($data);

        return response()->json(['message' => 'FCM token registered successfully.'], 200);
    }

    public function destroy(Request $request, Notification $notification)
    {
        if ($notification->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $notification->delete();

        return response()->json(['message' => 'Notification deleted']);
    }
}