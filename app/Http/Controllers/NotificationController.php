<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        return NotificationResource::collection($this->notifications->latestFor($user))
            ->additional(['unread_count' => $this->notifications->unreadCountFor($user)])
            ->response();
    }

    public function markAsRead(Request $request, string $notification): JsonResponse
    {
        $read = $this->notifications->markAsRead($request->user(), $notification);

        return NotificationResource::make($read)->response();
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $this->notifications->markAllAsRead($request->user());

        return response()->json(['unread_count' => 0]);
    }
}
