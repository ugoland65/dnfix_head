<?php

namespace App\Utils;

class TelegramUtils {

    private $botToken;

    public function __construct() {
        $config = require __DIR__ . '/../config/Telegram_config.php';

        if (!isset($config['botToken']) || empty($config['botToken'])) {
            throw new \Exception('Telegram bot token is not configured.');
        }

        $this->botToken = $config['botToken'];
    }

    /**
     * 텔레그램 메시지를 특정 사용자에게 전송하는 함수
     *
     * @param string $chatId 메시지를 받을 사용자의 Chat ID
     * @param string $message 전송할 메시지 내용
     * @return array API 호출 결과
     */
    public function sendMessage($chatId, $message, $parseMode = null) {

        if (empty($chatId) || empty($message)) {
            return [
                'status' => 'error',
                'message' => 'Chat ID and message cannot be empty.'
            ];
        }

        $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";

        $postData = [
            'chat_id' => $chatId,
            'text' => $message
        ];

		if (!empty($parseMode)) {
			$postData['parse_mode'] = $parseMode; // HTML 또는 MarkdownV2
		}

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return [
                'status' => 'error',
                'message' => $error
            ];
        }

        curl_close($ch);

        $decodedResponse = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'status' => 'error',
                'message' => 'Invalid JSON response: ' . json_last_error_msg()
            ];
        }

        return $decodedResponse;
    }
}
