<?php
require_once __DIR__ . '/vendor/autoload.php';

$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(getenv('CHANNEL_ACCESS_TOKEN'));
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => getenv('CHANNEL_SECRET')]);
$sign = $_SERVER["HTTP_" . \LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];
$events = $bot->parseEventRequest(file_get_contents('php://input'), $sign);

foreach ($events as $event) {
  if (!($event instanceof \LINE\LINEBot\Event\MessageEvent) ||
      !($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage)) {
    continue;
  }

  if($event->getText() === "おはよう"){
    $bot->replyText($event->getReplyToken(), "おはよう！さあ、今日も今日とて旅立ちますか！お仕事にね！");
  }elseif($event->getText() === "行ってきます"){
    $bot->replyText($event->getReplyToken(), "いってらっしゃい！頑張ってねプロデューサー！");
  }else{
    $bot->replyText($event->getReplyToken(), $event->getText());
  }
}