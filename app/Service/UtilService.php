<?php

namespace App\Service;

use App\Models\Zone;
use Kreait\Firebase\Messaging;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Exception\MessagingException;



class UtilService
{
    protected $messaging;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

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
            
            // Vérifier que la zone existe
            if (!$zone) {
                return collect(); // Retourner une collection vide si la zone n'est pas trouvée
            }
            
            // Initialiser une collection pour les descendants
            $descendants = collect();
            
            // Ajouter la zone de l'utilisateur à la collection des descendants
            $descendants->push($zone);
            
            // Récupérer tous les descendants de la zone de l'utilisateur, si la relation children est disponible
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

    /**
     * @codeCoverageIgnore
     */
    public static function sendWebNotification($title, $body, array $deviceKeys): array
    {
        $url = 'https://fcm.googleapis.com/v1/projects/rankit-74583/messages:send';
        $serverKey = env('FCM_SERVER_KEY');

        $data = [
            "registration_ids" => $deviceKeys,
            "notification" => [
                "title" => $title,
                "body" => $body,
                "sound" => "default",
                "click_action" => "FLUTTER_NOTIFICATION_CLICK"
            ],
            "priority" => "high",
            "data" => [
                "custom_key" => "custom_value"
            ]
        ];
        
        $encodedData = json_encode($data);

        $headers = [
            'Authorization: key=' . $serverKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);

        $result = curl_exec($ch);
        $res = array();
        if (!$result) {
            $res['success'] = false;
            $res['data'] = curl_error($ch);
        } else {
            $res['success'] = true;
            $res['data'] = $result;
        }

        curl_close($ch);

        Log::info(sprintf('%s: Message response is %s', __METHOD__, $res['data']));

        return $res;
    }

    public function sendNotification($title, $body, array $deviceTokens): array
    {
        Log::debug('Device Tokens:', $deviceTokens);  // Log received tokens

        $notification = Notification::create($title, $body);
        $message = CloudMessage::new()
            ->withNotification($notification)
            ->withData(['key' => 'value']);

        try {
            // Validate tokens using kreait/laravel-firebase (if applicable)
            $firebase = app('firebase.messaging');
            $validTokens = [];
            foreach ($deviceTokens as $token) {
            if ($firebase->messaging()->registrationToken()->isRegistered($token)) {
                $validTokens[] = $token;
            }
            }

            // Send notification only with valid tokens
            if (!empty($validTokens)) {
            $this->messaging->sendMulticast($message, $validTokens);
            return ['success' => true, 'message' => 'Notification sent successfully'];
            } else {
            return ['success' => false, 'message' => 'No valid registration tokens found'];
            }
        } catch (MessagingException $e) {
            Log::error('Failed to send notification', [
            'error' => $e->getMessage(),
            'stack' => $e->getTraceAsString(),
            'deviceTokens' => $deviceTokens
            ]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
