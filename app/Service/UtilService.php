<?php

namespace App\Service;

use App\Models\Zone;
use Illuminate\Support\Facades\Log;


class UtilService
{

    public static function get_descendants ($children, $descendants)
    {
        foreach ($children as $child) {
            $child = $child->load('children');
            $descendants->push($child);
            if ($child->children != null) {
                $descendants = UtilService::get_descendants($child->children, $descendants);
            }
        }
        return $descendants;
    }

    public static function get_ascendants($child, $ascendants)
    {
        $parent = $child->parent;

        while ($parent) {
            $ascendants->push($parent);
            $parent = $parent->parent;
        }

        return $ascendants;
    }

    public static function getZonesWithLevelId4ForUser($user)
    {
        if ($user->zone_id) {
            // Récupérer la zone de l'utilisateur connecté
            $zone = Zone::find($user->zone_id);
            
            // Initialiser une collection pour les descendants
            $descendants = collect();
            
            // Ajouter la zone de l'utilisateur à la collection des descendants
            $descendants->push($zone);
            
            // Récupérer tous les descendants de la zone de l'utilisateur
            if ($zone->children != null) {
                $descendants = UtilService::get_descendants($zone->children, $descendants);
            }
            
            // Filtrer les descendants pour ne garder que ceux avec level_id égal à 4
            $zones = $descendants->filter(function ($descendant) {
                return $descendant->level_id == 4;
            });
        } else {
            // Si l'utilisateur n'a pas de zone_id, retourner une collection vide
            $zones = collect();
        }

        return $zones;
    }

    public static function sendWebNotification($title, $body, array $deviceKeys): array
    {
        $url = 'https://fcm.googleapis.com/fcm/send';

        $serverKey = env('FCM_SERVER_KEY');

        // dd($serverKey);

        $data = [
            "registration_ids" => $deviceKeys,
            "notification" => [
                "title" => $title,
                "body" => $body,
            ]
        ];
        $encodedData = json_encode($data);

        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
        // Execute post
        $result = curl_exec($ch);
        $res = array();
        if (!$result) {
            $res['success'] = false;
            $res['data'] =  curl_error($ch);

        }else {
            $res['success'] = true;
            $res['data'] =  $result;
        }
        // Close connection
        curl_close($ch);

        Log::info(sprintf('%s: Message response is %s', __METHOD__, $res['data']));

        return $res;
    }
}
