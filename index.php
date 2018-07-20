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

// 返答ファイルの読み込み
$response_sentence = file("response_list");

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
      $str = preg_replace_callback('/\(([0-9A-F]{6})\)/',
        function($matches) use ($emoticon){
          $bin = hex2bin(str_repeat('0', 8 - strlen($matches[1])).$matches[1]);
          return mb_convert_encoding($bin, 'UTF-8', 'UTF-32BE');
        },
        $response[1]
      );
      $bot->replyText($event->getReplyToken(), $str);

      break;
    }
  }

  // 取り敢えず
  $mecab = new MeCab\Tagger();
  $nodes = $mecab->parseToNode($event->getText());
  $response_text = "";
  foreach ($nodes as $n){
    $response_text = $response_text . $n->getFeature();
  }
  $bot->replyText($event->getReplyToken(), $response_text);
}