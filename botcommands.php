<?php 

require 'vendor/autoload.php';

use \React\EventLoop\Factory;
use \unreal4u\TelegramAPI\HttpClientRequestHandler;
use \unreal4u\TelegramAPI\Telegram\Methods\GetUpdates;
use \unreal4u\TelegramAPI\Abstracts\TraversableCustomType;
use \unreal4u\TelegramAPI\TgLog;

use \unreal4u\TelegramAPI\Telegram\Methods\SendMessage;

/**
 * Process the messages sent by user in bot scan radar
 */
function ProcessCommand($command, $update)
{
    include 'variables.php';
    include 'utils.php';
    $loop = Factory::create();
    switch ($command) 
    {
        // Command to add a manga
        case "/addmanga":

            // get the file contents, assuming the file to be readable (and exist)
            $file = "mangas.txt";
            $arrayOfMangaCommands = ConvertFileToArray($file);

            // Configures text
            $text = "Selectionne un manga si dessous : \n";

            foreach ($arrayOfMangaCommands as $command)
            {
                $text = $text.$command."\n";
            }

            SendMessage($loop, $update->message->chat->id, $text);
        break;
        // Default message, it is here that commands to add a specific manga will pass
        default:
            if (preg_match('#^/#', $command)) 
            {
                if (!Exists($command))
                {
                    $text = "Je n'ai pas compris ta commande. N'hésite pas regarder quel manga tu peux ajouter via la commande /addmanga";
                    SendMessage($loop, $update->message->chat->id, $text);
                }
                else 
                {
                    if (InsertMangaToUser($command, $update->message->chat->id))
                    {
                        $message = "Le scan a été ajouté avec succès ! Tu receveras des nouvelles des prochaines sorties bientôt.";
                        SendMessage($loop, $update->message->chat->id, $message);
                    }
                    else 
                    {
                        $message = "Il y a eu une erreur, réessaye prochainement.";
                        SendMessage($loop, $update->message->chat->id, $message);
                    }
                    
                }
            }
        break;
    }

    /**
     * Sends message to a chat id
     */
    function SendMessage($loop, $chat_id, $message)
    {
        //Sends message
        $sendMessage = new SendMessage();
        $sendMessage->chat_id = $chat_id;
        $sendMessage->text = $message;
        $tgLog = new TgLog($bot_id, new HttpClientRequestHandler($loop));
        $tgLog->performApiRequest($sendMessage);
        $loop->run();
    }
}

?>