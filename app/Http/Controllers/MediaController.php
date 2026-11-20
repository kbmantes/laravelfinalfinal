<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class MediaController extends Controller
{
    public function show(string $name): Response
    {
        abort_unless(preg_match('/^[A-Za-z0-9-]+\.jpg$/D', $name) && Storage::disk('local')->exists('uploads/'.$name), 404);
        return response(Storage::disk('local')->get('uploads/'.$name), 200, ['Content-Type'=>'image/jpeg','X-Content-Type-Options'=>'nosniff','Cache-Control'=>'private, max-age=3600']);
    }
}
