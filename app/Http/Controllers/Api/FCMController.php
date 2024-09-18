<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Google\Client as GoogleClient;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FcmController extends Controller
{
    public function updateDeviceToken(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'fcm_token' => 'required|string',
        ]);

        $request->user()->update(['fcm_token' => $request->fcm_token]);

        return response()->json(['message' => 'Device token updated successfully']);
    }

    public function sendFcmNotification(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        $user = \App\Models\User::find($request->user_id);
        $fcm = $user->fcm_token;

        if (!$fcm) {
            return response()->json(['message' => 'User does not have a device token'], 400);
        }

        $title = $request->title;
        $description = $request->body;
        $projectId = config('services.fcm.project_id');

        $credentialsFilePath = Storage::path('json/store-ify-836cc-firebase-adminsdk-7s9w5-dfc5c6c3b8.json');
        $client = new GoogleClient();
        $client->setAuthConfig($credentialsFilePath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();

        $access_token = $token['access_token'];

        $headers = [
            "Authorization: Bearer $access_token",
            'Content-Type: application/json'
        ];

        $data = [
            "message" => [
                "token" => $fcm,
                "notification" => [
                    "title" => $title,
                    "body" => $description,
                ],
            ]
        ];
        $payload = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_VERBOSE, true); // Enable verbose output for debugging
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return response()->json([
                'message' => 'Curl Error: ' . $err
            ], 500);
        } else {
            return response()->json([
                'message' => 'Notification has been sent',
                'response' => json_decode($response, true)
            ]);
        }
    }
}














// namespace App\Http\Controllers\Api;


// use App\Http\Controllers\Controller;
// use App\Services\FirebaseService;
// use Illuminate\Http\Request;
// use App\Models\Notification;

// class FCMController extends Controller
// {
//     protected $firebaseService;
//     public function __construct(FirebaseService $firebaseService)
//     {
//         $this->firebaseService = $firebaseService;
//     }
//     public function sendNotification(Request $request){
//         $request->validate([
//             'token' => 'required|string',
//             'title' => 'required|string',
//             'body' => 'required|string',
//             'data' => 'required|array',
//         ]);
//         $token = $request->token;
//         $title = $request->title;
//         $body = $request->body;
//         $data = $request->data;

//         $notification = Notification::create([
//             'type' => 'individual',
//             'title' => $title,
//             'body' => $body,
//         ]);

//         $this->firebaseService->sendNotification($token, $title, $body, $data);
//         return $this->successResponse();
//     }
// }
