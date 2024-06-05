<?php

namespace App\Http\Controllers\Api;

use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\NotificationRequest;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->error([], __('User not authenticated'), 403);
        }

        $zoneId = $user->zone_id;

        $notifications = Notification::where('zone_id', $zoneId)->get();

        return response()->success($notifications, __('Notifications charged successfully'), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(NotificationRequest $request)
    {
        $notification = Notification::create($request->validated());

        return response()->success($notification, __('Notification created successfully'), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function show(Notification $notification)
    {
        return response()->success($notification, __('Notification charged successfully'), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function update(NotificationRequest $request, Notification $notification)
    {
        $notification->update($request->validated());

        return response()->success($notification, __('Notification updated successfully'), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function destroy(Notification $notification)
    {
        // Vérifier si l'utilisateur est authentifié et administrateur
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->error([], __('Unauthorized'), 403);
        }

        $notification->delete();

        return response()->success([], __('Notification deleted successfully'), 200);
    }
}
