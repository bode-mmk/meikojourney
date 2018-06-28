<?php

use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Exception\InvalidEventRequestException;
use LINE\LINEBot\Exception\InvalidSignatureException;

require_once __DIR__ . '/vendor/autoload.php';

$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(getenv('CHANNEL_ACCESS_TOKEN'));
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => getenv('CHANNEL_SECRET')]);
$sign = $_SERVER["HTTP_" . \LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];
$events = $bot->parseEventRequest(file_get_contents('php://input'), $sign);

// エモーティコンの置換準備
$raw_code = [
  '100001' => '100001',
  '100002' => '100002',
  '100003' => '100003',
  '100004' => '100004',
  '100005' => '100005',
  '100006' => '100006',
  '100007' => '100007',
  '100008' => '100008',
];

$emoticon = [];
foreach($raw_code as $code){
  $bin = hex2bin(str_repeat('0', 8 - strlen($code)). $code);
  $emoticon[$code] = mb_convert_encoding($bin, 'UTF-8', 'UTF-32BE');
}

// 返答ファイルの読み込み
$response_sentence = file("response_list");

// error_log('message', 3, 'test.log');

foreach ($events as $event) {
  if (!($event instanceof \LINE\LINEBot\Event\MessageEvent) ||
      !($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage)) {
    continue;
  }

  // 返答リストから該当するものを返答する
  foreach($response_sentence as $line){
    // Pの発言,芽衣子さんの発言
    $response = explode(",", trim($line));

    if($event->getText() === $response[0]){
      // 絵文字は置換する → (100007とか)
      //$str = preg_replace('/\((\d{6})\)/', $emoticon['100004'], $response[1] ); みたいに本来はしたい……
      $str = preg_replace_callback('/\((\d{6})\)/',
        function($matches) use ($emoticon){
          return $emoticon[$matches[1]];
        },
        $response[1]
      );
      $bot->replyText($event->getReplyToken(), $str);

      break;
    }
  }
}