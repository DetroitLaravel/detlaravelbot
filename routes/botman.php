<?php
use App\Http\Controllers\BotManController;
use DMS\Service\Meetup\MeetupKeyAuthClient;
use Carbon\Carbon;

$botman = resolve('botman');

$botman->hears('Hi', function ($bot) {
    $bot->reply('Hello!');
});
$botman->hears('Start conversation', BotManController::class . '@startConversation');

$botman->hears('Hands up', function ($bot) {
    $bot->reply('For Detroit!');
});

$botman->hears('Next up\?', function ($bot) {
    $client = MeetupKeyAuthClient::factory(['key' => env('MEETUP_API_KEY')]);

    $command = $client->getCommand('getGroupEvents', array('urlname' => 'Laravel-Detroit'));

    $command->prepare();
    $response = $command->execute();

    collect($response->getData())->map(function ($event) use ($bot) {
        $bot->reply(
            $event['name'] .
                ' on ' .
                Carbon::parse($event['local_date'] . ' ' . $event['local_time'])->format('l, F jS Y \a\t h A') .
                '. RSVP: ' .
                $event['link']
        );
    });
});
