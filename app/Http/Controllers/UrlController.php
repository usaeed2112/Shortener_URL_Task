<?php

namespace App\Http\Controllers;

use App\Models\Url;
use Illuminate\Http\Request;

class UrlController extends Controller
{
    function index($hash)
    {
        $url = Url::where('short_url', $hash)->firstOrFail();

        return redirect($url->url);
    }
}
