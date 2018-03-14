<?php

namespace App\Http\Controllers;


use App\Models\Lang;

class LanguagesController
{
    public function set($lang) {
        $lang = Lang::where('lang', $lang)->first()->lang;

        session(['applocale' => $lang]);
        session(['locale' => $lang]);

        app()->setLocale($lang);

        return back();
    }
}