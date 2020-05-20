<?php 

namespace Processor;
require('vendor/autoload.php');
use \React\EventLoop\Factory;
use \unreal4u\TelegramAPI\HttpClientRequestHandler;
use \unreal4u\TelegramAPI\Telegram\Methods\GetUpdates;
use \unreal4u\TelegramAPI\Abstracts\TraversableCustomType;
use \unreal4u\TelegramAPI\TgLog;
use \unreal4u\TelegramAPI\Telegram\Methods\SendMessage;


class MessageProcessor
{
    function __contruct()
    {
        
    }
    
    /**
     * Process the messages sent by user in bot scan radar
     */
    public function ProcessCommand($command, $update, $loop)
    {
        include 'variables.php';
        include 'utils.php';
        include 'connect.php';
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
    
                $this->BotSendMessage($loop, $update->message->chat->id, $text);
            break;
            // Default message, it is here that commands to add a specific manga will pass
            default:
                if (preg_match('#^/#', $command)) 
                {
                    if (!Exists($command))
                    {
                        $text = "Je n'ai pas compris ta commande. N'hésite pas regarder quel manga tu peux ajouter via la commande /addmanga";
                        $this->BotSendMessage($loop, $update->message->chat->id, $text);
                    }
                    else 
                    {
                        if (InsertMangaToUser($command, $update->message->chat->id))
                        {
                            $message = "Le scan a été ajouté avec succès ! Tu receveras des nouvelles des prochaines sorties bientôt.";
                            $this->BotSendMessage($loop, $update->message->chat->id, $message);
                        }
                        else 
                        {
                            $message = "Il y a eu une erreur, réessaye prochainement.";
                            $this->BotSendMessage($loop, $update->message->chat->id, $message);
                        }
                        
                    }
                }
                else 
                {
                    $message = "Mon radar ne détecte pas de message inutile à la recherche de manga. Essaye plutôt de regarder tous les mangas que tu peux suivre en tapant /addmanga.";
                    $this->BotSendMessage($loop, $update->message->chat->id, $message);
                }
            break;
        }
    }
    
        /**
         * Sends message to a chat id
         */
        public function BotSendMessage($_loop, $_chat_id, $_message)
        {
            include 'variables.php';
            //Sends message
            $sendMessage = new SendMessage();
            $sendMessage->chat_id = $_chat_id;
            $sendMessage->text = $_message;
            $tgLog = new TgLog($bot_id, new HttpClientRequestHandler($_loop));
            $tgLog->performApiRequest($sendMessage);
            $_loop->run();
        }
}
?>