<?php

use App\Livewire\DemoPage;
use Illuminate\Support\Facades\Route;

Route::livewire('/{theme?}', DemoPage::class)->where('theme', 'tailwind|bootstrap-5|bootstrap-4|bootstrap5|bootstrap4|bootstrap');
