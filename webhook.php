<?php

include 'variables.php';
include 'botcommands.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'vendor/autoload.php';

 use Processor\MessageProcessor;
 use \React\EventLoop\Factory;
 use \unreal4u\TelegramAPI\HttpClientRequestHandler;
 use \unreal4u\TelegramAPI\Telegram\Methods\GetUpdates;
 use \unreal4u\TelegramAPI\Abstracts\TraversableCustomType;
 use \unreal4u\TelegramAPI\TgLog;
// use \unreal4u\TelegramAPI\Telegram\Methods\SetWebhook;

 $loop = Factory::create();

// $setWebhook = new SetWebhook();
// $setWebhook->url = 'https://www.lehich.com/projects/scannotificator/webhook.php';
// $tgLog = new TgLog('929088764:AAGnpF9M69aIabA6OoUlHn93SSbdRnpSNg4', new HttpClientRequestHandler($loop));
// $tgLog->performApiRequest($setWebhook);
// $loop->run();

use \unreal4u\TelegramAPI\Telegram\Types\Update;


// Getting POST request body and decoding it from JSON to associative array
$content = file_get_contents('php://input');
$data = json_decode($content, true);
$update = new Update($data);

$file = 'response.txt';
$message = "date : ".$update->message->date.", text: ".$update->message->text. "chat id : ".$update->message->chat->id."\n";
file_put_contents($file, $message, FILE_APPEND | LOCK_EX);

//Process user command
$processMessage = new MessageProcessor();
$processMessage->ProcessCommand($update->message->text, $update, $loop);

?>