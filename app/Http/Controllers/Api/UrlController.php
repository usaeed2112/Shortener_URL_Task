<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Url;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UrlController extends Controller
{
    function shortUrl(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'url' => ['required', 'url'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        $domain = env('APP_URL');
        $url = $request->url;

        $check = Url::where('url', $url)->first();
        if ($check) {
            return response()->json(['url' => $domain . $check->short_url, 'status' => 200]);
        } else {
            // Call Google Safe Browsing API
            $apiKey = env('SAFE_BROWSING_KEY');
            $apiUrl = 'https://safebrowsing.googleapis.com/v4/threatMatches:find';

            $requestData = [
                'client' => [
                    'clientId' => 'YourClientID',
                    'clientVersion' => '1.0.0'
                ],
                'threatInfo' => [
                    'threatTypes' => ['MALWARE', 'SOCIAL_ENGINEERING', 'THREAT_TYPE_UNSPECIFIED', 'UNWANTED_SOFTWARE'],
                    'platformTypes' => ['ANY_PLATFORM'],
                    'threatEntryTypes' => ['URL'],
                    'threatEntries' => [['url' => $url]]
                ]
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post($apiUrl . '?key=' . $apiKey, $requestData);

            $apiResponse = $response->json();

            // Handle Google Safe Browsing API response
            if (isset($apiResponse['matches']) && count($apiResponse['matches']) > 0) {
                $validator->errors()->add('url', 'The URL is not safe.');
                throw new ValidationException($validator);
            } else {


                $md5Hash = md5($url);
                $sixCharHash = substr($md5Hash, 0, 6);
                $shortenedURL = preg_replace('/[^a-zA-Z0-9]/', '', $sixCharHash);
                $finalShortenedURL = $shortenedURL;

                $newUrl = new Url();
                $newUrl->url = $url;
                $newUrl->short_url = $finalShortenedURL;
                $newUrl->save();
                return response()->json(['url' => $domain . $shortenedURL, 'status' => 200]);
            }
        }
    }
}
