<?php

use App\Livewire\DemoPage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get('/locale/{lang}', function (string $lang) {
    $allowed = ['en', 'es', 'pt', 'fr', 'de', 'it', 'nl', 'pl', 'ru', 'zh', 'ja', 'ko', 'tr', 'id'];
    if (in_array($lang, $allowed, true)) {
        Session::put('locale', $lang);
    }

    return Redirect::back();
})->name('locale.switch');

Route::livewire('/{theme?}', DemoPage::class)->where('theme', 'tailwind|bootstrap-5|bootstrap-4|bootstrap5|bootstrap4|bootstrap');
