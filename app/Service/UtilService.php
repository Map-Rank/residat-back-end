<?php

namespace App\Service;

use App\Models\User;
use App\Models\Zone;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Laravel\Firebase\Facades\Firebase;

class UtilService
{
    protected $messaging;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    public static function get_descendants($children, $descendants)
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
            if (! $zone) {
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
            'registration_ids' => $deviceKeys,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ],
            'priority' => 'high',
            'data' => [
                'custom_key' => 'custom_value',
            ],
        ];

        $encodedData = json_encode($data);

        $headers = [
            'Authorization: key='.$serverKey,
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
        $res = [];
        if (! $result) {
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

    /**
     * @codeCoverageIgnore
     */
    public function sendNotification($title, $body, array $deviceTokens)
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
            if (! empty($validTokens)) {
                $this->messaging->sendMulticast($message, $validTokens);

                return ['success' => true, 'message' => 'Notification sent successfully'];
            } else {
                return ['success' => false, 'message' => 'No valid registration tokens found'];
            }
        } catch (MessagingException $e) {
            Log::error('Failed to send notification', [
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
                'deviceTokens' => $deviceTokens,
            ]);

            return ['success' => false, 'message' => $e->getMessage()];
        }

        // try {
        //     // Batch validate tokens in chunks of 100
        //     $validTokens = [];
        //     foreach (array_chunk($deviceTokens, 100) as $tokenChunk) {
        //         $validTokens = array_merge($validTokens, $firebase->messaging()->registrationToken()->areRegistered($tokenChunk));
        //     }

        //     if (!empty($validTokens)) {
        //         $this->messaging->sendMulticast($message, $validTokens);
        //         return ['success' => true, 'message' => 'Notification sent successfully'];
        //     } else {
        //         return ['success' => false, 'message' => 'No valid registration tokens found'];
        //     }
        // } catch (MessagingException $e) {
        //     Log::error('Failed to send notification', [
        //         'error' => $e->getMessage(),
        //         'stack' => $e->getTraceAsString(),
        //         'deviceTokens' => $deviceTokens
        //     ]);
        //     return ['success' => false, 'message' => $e->getMessage()];
        // }
    }

    public function sendNewNotification(string $title, string $body, array $tokens)
    {
        $firebase = (new Factory)->withServiceAccount(config('firebase.projects.app.credentials'));
        $messaging = $firebase->createMessaging();

        // Définir le contenu du message
        $message = [
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
        ];

        try {
            // Envoyer le message multicast
            $report = $messaging->sendMulticast($message, $tokens);

            $successes = $report->successes()->count();
            $failures = $report->failures()->count();
            Log::info(sprintf('%s: Message failures is %s', __METHOD__, $failures));
            Log::info(sprintf('%s: Message successes is %s', __METHOD__, $successes));

            $successesUser = $report->successes();
            $failuresUser = $report->failures();

            // Logger les utilisateurs avec succès
            $successUserNames = [];
            foreach ($successesUser as $success) {
                $token = $success->target(); // Récupère le token
                $userId = array_search($token, $tokens); // Trouve l'utilisateur correspondant
                if ($userId) {
                    $user = User::find($userId);
                    if ($user) {
                        $successUserNames[] = $user->first_name;
                    }
                }
            }
            Log::info(sprintf('%s: Users with successful notifications: %s', __METHOD__, implode(', ', $successUserNames)));

            // Logger les utilisateurs avec des échecs
            $failureUserNames = [];
            foreach ($failuresUser as $failure) {
                $token = $failure->target(); // Récupère le token
                $userId = array_search($token, $tokens); // Trouve l'utilisateur correspondant
                if ($userId) {
                    $user = User::find($userId);
                    if ($user) {
                        $failureUserNames[] = $user->first_name;
                    }
                }
            }
            Log::info(sprintf('%s: Users with failed notifications: %s', __METHOD__, implode(', ', $failureUserNames)));

            $data = [
                'successes' => $successes,
                'failures' => $failures,
                'message' => __('Firebase notification send successfully'),
            ];

            return $data;

        } catch (MessagingException $e) {
            // Gérer les erreurs imprévues
            Log::info(sprintf('%s: Erreur lors de l\'envoi du message multicast %s', __METHOD__, $e->getMessage()));

            return null;
        }
    }

    /**
     * @codeCoverageIgnore
     */
    public function test()
    {
        $token = 'f8004cshTcuE8BYmxBUN9B:APA91bFNd3hcTHmz8ButxzYofEQBr3QqDmFYPRX-Nulx_Rv5nb_3NWHoT8yS9LRcMv1f435GtxngVDXoVPGJsd8sxSibFYfH_jVjhSI7xiSmUGr6ZEC4MujiuVS7ZK4IgPhlaRRCSEZV';

        $firebase = (new Factory)->withServiceAccount(config('firebase.projects.app.credentials'));
        $messaging = $firebase->createMessaging();

        // Define message content
        $message = [
            'notification' => [
                'title' => 'Hello!',
                'body' => 'Test ronald 1.',
            ],
            // 'token' => $token,  // Specify the recipient device token
        ];

        $deviceTokens = [
            $token,
            'DEVICE_TOKEN_2',
            'DEVICE_TOKEN_3',
            // Add more tokens as needed
        ];

        try {
            // Send the multicast message
            $report = $messaging->sendMulticast($message, $deviceTokens);

            echo "Messages sent successfully! \n";
            echo 'Success count: '.$report->successes()->count()."\n";
            echo 'Failure count: '.$report->failures()->count()."\n";

            dd($report);
            // Check for failed tokens
            // foreach ($report->failures()->all() as $failure) {
            //     echo "Failed to send to: " . $failure->target()->value() . "\n";
            //     echo "Reason: " . $failure->error()->getMessage() . "\n";
            // }
        } catch (MessagingException $e) {
            // Handle any unexpected errors
            echo 'Error sending multicast message: '.$e->getMessage();
        }
    }

    /**
     * @codeCoverageIgnore
     */
    public static function groupDailyWeatherData($hourlyData) {
        // Extract the arrays from the input
        $times = $hourlyData["time"] ?? [];
        $temperaturesMax = $hourlyData["temperature_2m_max"] ?? [];
        $temperaturesMin = $hourlyData["temperature_2m_min"] ?? [];
        $precipitationSums = $hourlyData["precipitation_sum"] ?? [];
        $winSpeedMaxs = $hourlyData["wind_speed_10m_max"] ?? [];
        
        $mergedData = [];
        $count = min(count($times), count($temperaturesMax), count($temperaturesMin), count($precipitationSums), count($winSpeedMaxs));
        
        // Merge the arrays into the desired structure
        for ($i = 0; $i < $count; $i++) {
            $mergedData[] = [
                "time" => $times[$i],
                "temperature_2m_max" => $temperaturesMax[$i],
                "temperature_2m_min" => $temperaturesMin[$i],
                "precipitation_sum" => $precipitationSums[$i],
                "wind_speed_10m_max" => $winSpeedMaxs[$i]
            ];
        }

        return $mergedData;
    }

    /**
     * @codeCoverageIgnore
     */
    public static function mergeDailyAndHourly($hourlyData, $dailyData) {
        
        $mergedData = [];
        $count = min(count($hourlyData), count($dailyData));
        
        // Merge the arrays into the desired structure
        for ($i = 0; $i < $count; $i++) {
            $mergedData[] = [
                "date" => $hourlyData[$i]['date'],
                "temperature_2m_max" => $dailyData[$i]['temperature_2m_max'],
                "temperature_2m_min" => $dailyData[$i]['temperature_2m_min'],
                "precipitation_sum" => $dailyData[$i]['precipitation_sum'],
                "wind_speed_10m_max" => $dailyData[$i]['wind_speed_10m_max'],
                "average_humidity" => $hourlyData[$i]["average_humidity"],
                "average_soil_moisture" => $hourlyData[$i]["average_soil_moisture"]
            ];
        }

        return $mergedData;
    }

    // Get the fetched hourly data and yeild that in the an average daily data
    /**
     * @codeCoverageIgnore
     */
    public static function groupAndAverageDailyData($hourlyData) {
        // Extract the arrays from the input
        $times = $hourlyData["time"] ?? [];
        $humidities = $hourlyData["relative_humidity_2m"] ?? [];
        $soilMoistures = $hourlyData["soil_moisture_0_to_7cm"] ?? [];
        
        $dailyData = [];

        // Iterate through the time array
        foreach ($times as $index => $timestamp) {
            $date = substr($timestamp, 0, 10); // Extract the date part (YYYY-MM-DD)
            
            if (!isset($dailyData[$date])) {
                $dailyData[$date] = [
                    "humidity_sum" => 0,
                    "moisture_sum" => 0,
                    "count" => 0
                ];
            }

            $dailyData[$date]["humidity_sum"] += $humidities[$index] ?? 0;
            $dailyData[$date]["moisture_sum"] += $soilMoistures[$index] ?? 0;
            $dailyData[$date]["count"]++;
        }

        // Calculate averages for each day
        $averagedData = [];
        foreach ($dailyData as $date => $data) {
            $averagedData[] = [
                "date" => $date,
                "average_humidity" => $data["humidity_sum"] / $data["count"],
                "average_soil_moisture" => $data["moisture_sum"] / $data["count"]
            ];
        }

        return $averagedData;
    }
}
