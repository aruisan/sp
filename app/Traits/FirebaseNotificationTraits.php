<?php
namespace App\Traits;
use App\User;


trait FirebaseNotificationTraits
{
    public function sendTokenMovil($title, $body, $rol){
         //$firebaseToken = User::whereNotNull('device_token')->pluck('device_token')->all();
        //dd($firebaseToken);
       
        $users = User::whereNotNull('device_token')->get()->filter(function($e) use($rol){
            return in_array($rol, $e->getRoleNames()->toArray());
        });

        $SERVER_API_KEY = 'AAAA0mExU9w:APA91bGxod5-izUzUe3YDaw6mnGtmF6DIhUHaPvZnsQ_NU76mS7uRgv6Apm_p08wCVS10U3xQdaEbS-qQBeaRkZs3pjREI8ZCgy4UJydqdkA9cIl5rBBpG6-ebRSm8BOpLrpvpU1_9jp';
        

        foreach($users as $user):
            $data = [
                "priority" => "high",
                "to" => $user->device_token,
                "notification" => [
                    "title" => $title,
                    "body" => $body,  
                ],
                "data" => Null
            ];

            $headers = [
                'Authorization: key=' . $SERVER_API_KEY,
                'Content-Type: application/json',
            ];
        
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                
            $response = curl_exec($ch);
        endforeach;
        /*
        para enviar por web
        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => $request->title,
                "body" => $request->body,  
            ]
        ];
        */
        return TRUE;
    }
}
