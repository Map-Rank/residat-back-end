<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Models\Zone;
use App\Models\Notification;
use App\Service\UtilService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\NotificationRequest;

/**
 * @group Module Notification
 */
class NotificationController extends Controller
{
    /**
     * list your notifications
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'page' => ['sometimes','numeric'],
            'size'=> ['sometimes', 'numeric'],
        ]);

        if ($validator->fails()) {
            return response()->errors($validator->failed(),  __('bad params'), 400);
        }
        
        $validated = $validator->validated();
        

        $page = $validated['page'] ?? 0;
        $size = $validated['size'] ?? 10;

        $user = Auth::user();

        if (!$user) {
            return response()->error([], __('User not authenticated'), 403);
        }

        $zoneId = $user->zone_id;

        $data = Notification::with('user','zone');

        if(isset($zoneId)){
            $zone = Zone::with('children')->find($zoneId);
            $descendants = collect();
            $descendants->push($zone);
            if ($zone->children != null){
                $descendants =  UtilService::get_descendants($zone->children, $descendants);
            }
            $descendantIds = $descendants->pluck('id');
            $descendantIds->push($zoneId);
            $data = $data->whereIn('zone_id',  $descendantIds);
        }

        $data =  $data->offSet($page * $size)->take($size)->latest()->get();

        return response()->success($data, __('Notifications charged successfully'), 200);
    }

    /**
     * Create notification
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(NotificationRequest $request)
    {
        $notification = Notification::create($request->validated());

        $descendants = collect();

        $zone = Zone::with('children')->find($notification->zone_id);

        $descendants = UtilService::get_descendants($zone->children, $descendants); 

        $descendants->push($notification->zone);

        $users_token = User::whereIn('zone_id',$descendants->pluck('id'))->pluck('fcm_token');

        try{
            UtilService::sendWebNotification($notification->title, $notification->content, $users_token);
        }catch(Exception $ex){
            Log::warning(sprintf('%s: The error is : %s', __METHOD__, $ex->getMessage()));
        }
        
        return response()->success($notification, __('Notification created successfully'), 200);
    }

    /**
     * Show one notification
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
     * Delete notification
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
