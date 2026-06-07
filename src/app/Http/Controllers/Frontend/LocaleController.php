<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Language;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function set(Request $request, string $code)
    {
        $language = Language::where('code', $code)->where('is_active', true)->first();

        if (! $language) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Language not available.'], 422);
            }

            return back()->with('error', 'Language not available.');
        }

        $request->session()->put('locale', $code);
        cookie()->queue('locale', $code, 60 * 24 * 365);

        if ($user = auth()->user()) {
            if (in_array('locale', $user->getFillable(), true) || array_key_exists('locale', $user->getAttributes())) {
                $user->locale = $code;
                $user->save();
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['code' => $code]);
        }

        return back();
    }
}
