<?php
/**
     * Sends telegram message via Bot API warning that a new scan came out and was added to the database
     */
    function messageNewScan($_manga_name, $_scan_number, $_scan_date, $_scan_url)
    {
        //$text = '<h1>Un nouveau scan est sorti !</h1>' . '<p><a href="' . $_scan_url . '" target=_blank>' . $_manga_name . ' Chapitre ' . $_scan_number . '</a></p>' . '<p>Date de sortie : ' . $_scan_date . '</p>';
        $text = $_manga_name . ' Chapitre ' . $_scan_number . $_scan_url . ' ' . '   Date de sortie : ' . $_scan_date;
        sendToTelegram($text);
    }

    /**
     * Sends HTTP POST Request with message $text
     */
    function sendToTelegram($text)
    {
        include 'variables.php';
        $url_telegram = "https://api.telegram.org/bot".$bot_id."/sendMessage";
        $data = array(
            'chat_id' => $chat_id,
            'text' => $text
        );

        $options = array(
            'http' => array(
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );

        $context = stream_context_create($options);
        $result = file_get_contents($url_telegram, false, $context);

        if ($result === FALSE) {
            echo ("<strong>There was an error</strong>");
        }
    }

    /**
     * Sends HTTP POST Request with message $text
     */
    function sendContact($_phone_number)
    {
        include 'variables.php';
        $url_telegram = "https://api.telegram.org/bot".$bot_id."/sendContact";
        $data = array(
            'chat_id' => $chat_id,
            'first_name' => "Some random text",
            'phone_number' => $_phone_number
        );

        $options = array(
            'http' => array(
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );

        $context = stream_context_create($options);
        $result = file_get_contents($url_telegram, false, $context);

        return $result;
    }

    function deleteMessage($_chat_id, $_message_id)
    {
        include 'variables.php';
        $url_telegram = "https://api.telegram.org/bot".$bot_id."/deleteMessage";
        $data = array(
            'chat_id' => $_chat_id,
            'message_id' => $_message_id
        );

        $options = array(
            'http' => array(
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );

        $context = stream_context_create($options);
        $result = file_get_contents($url_telegram, false, $context);

        return $result;
    }

    function getUserID($_phone_number)
    {
        $contact = json_decode(sendContact($_phone_number));
        $status = $contact->ok;
        if ($status) {
            $user_id = $contact->result->contact->user_id;
            $message_id = $contact->result->message_id;
            $chat_id = $contact->result->chat->id;
            deleteMessage($chat_id, $message_id);
            return $user_id;
        }
        return null;
    }

    function setWebHook() 
    {
        include "variables.php";
        $url_telegram = "https://api.telegram.org/bot{$bot_id}/setWebhook?url={$webhook_url}";
        $options = array(
            'http' => array(
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'method' => 'GET'
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url_telegram, false);
    }

?>