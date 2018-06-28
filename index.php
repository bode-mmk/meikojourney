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

foreach ($events as $event) {
  if (!($event instanceof \LINE\LINEBot\Event\MessageEvent) ||
      !($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage)) {
    continue;
  }

  if($event->getText() === "おはよう"){
    $bot->replyText($event->getReplyToken(), "おはよう！さあ、今日も今日とて旅立ちますか！お仕事にね！".$emoticon["100004"]);
  }elseif($event->getText() === "行ってきます"){
    $bot->replyText($event->getReplyToken(), "いってらっしゃい！頑張ってねプロデューサー！".$emoticon["100008"]);
  }elseif($event->getText() === "おやすみ"){
    $bot->replyText($event->getReplyToken(), "おやすみプロデューサー！今日も一日お疲れ様！".$emoticon["100007"]);
  }else{
    $bot->replyText($event->getReplyToken(), $event->getText());
  }
}