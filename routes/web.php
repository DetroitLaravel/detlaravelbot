<?php

use DMS\Service\Meetup\MeetupKeyAuthClient;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', function () {
    return view('welcome');
});

Route::match(['get', 'post'], '/botman', 'BotManController@handle');
Route::get('/botman/tinker', 'BotManController@tinker');

// Test for output
Route::get('/next', function () {
    $client = MeetupKeyAuthClient::factory(['key' => env('MEETUP_API_KEY')]);

    $command = $client->getCommand('getGroupEvents', array('urlname' => 'Laravel-Detroit'));

    $command->prepare();
    $response = $command->execute();

    collect($response->getData())->map(function ($event) {
        dump($event['name'] .
            ' on ' .
            Carbon::parse($event['local_date'] . ' ' . $event['local_time'])->format('l, F jS Y \a\t h A') .
            '. RSVP: ' .
            $event['link']);
    });
});
