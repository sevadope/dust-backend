<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

Artisan::command('secret {length=20}', function ($length) {

	$this->comment(bin2hex(random_bytes($length)));
	
})->describe('Generate cryptographically secure string');

Artisan::command('bot:keys {length=20}', function ($length) {

	$this->info('ID: ' . \Ramsey\Uuid\Uuid::uuid4());
	$this->comment('Secret: ' . bin2hex(random_bytes($length)));

})->describe('Generate id and secret for bot');
