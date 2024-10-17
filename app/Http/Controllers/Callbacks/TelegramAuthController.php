<?php

namespace App\Http\Controllers\Callbacks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TelegramAuthController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'id' => ['required', 'string'],
        ]);

        auth()->user()->update([
           'telegram_id' => $data['id'],
        ]);

        return redirect()->back();
    }
}
