<?php

$botToken = 'BOT_TOKEN';
$adminChatId = 'ADMIN_TELEGRAM_ID';
$apiUrl = "https://api.telegram.org/bot$botToken/";

$update = json_decode(file_get_contents('php://input'), TRUE);

if (isset($update['message'])) {
    $message = $update['message'];
    $chatId = $message['chat']['id'];
    $text = $message['text'];

    if ($text == "/start") {
        if ($chatId == $adminChatId) {
            sendMessage($chatId, "Welcome, Administrator! Wait for incoming messages from users.");
        } else {
            sendMessage($chatId, "Share your problem, and we will respond as soon as possible.");
        }
    } else {
        if ($chatId == $adminChatId && !isset($message['reply_to_message'])) {
            return;
        }

        if ($chatId == $adminChatId && isset($message['reply_to_message'])) {
            $userChatId = $message['reply_to_message']['forward_from']['id'];
            copyMessage($userChatId, $chatId, $message['message_id']);
        } else {
            forwardMessage($adminChatId, $chatId, $message['message_id']);
        }
    }
}

function sendMessage($chatId, $text) {
    global $apiUrl;
    file_get_contents($apiUrl . "sendMessage?chat_id=$chatId&text=" . urlencode($text));
}

function forwardMessage($toChatId, $fromChatId, $messageId) {
    global $apiUrl;
    file_get_contents($apiUrl . "forwardMessage?chat_id=$toChatId&from_chat_id=$fromChatId&message_id=$messageId");
}

function copyMessage($toChatId, $fromChatId, $messageId) {
    global $apiUrl;
    file_get_contents($apiUrl . "copyMessage?chat_id=$toChatId&from_chat_id=$fromChatId&message_id=$messageId");
}

?>
