<?php

function parseReferal($command)
{
  $referal = explode("/start ", $command)[1];
  return $referal;
}

function inline_keyboard(){
    $inline_button1 = array("text"=>"Назначить админа","callback_data"=>'/in_hello');
    $inline_button2 = array("text"=>"Разжаловать админа","callback_data"=>'/in_bye');
    $inline_button3 = array("text"=>"Blackstit","url"=>'t.me/blackstit');

    $inline_keyboard = [
//        [$inline_button1, $inline_button2],
        [$inline_button3]
    ];

    $keyboard = json_encode(array("inline_keyboard"=>$inline_keyboard));
    return $keyboard;
}

function keyboard() {
  
  var_dump($keyboard = json_encode($keyboard = ['keyboard' => [
  ['Обучение','Реферал'],
//  ['Обратная связь']
  ] ,
  'resize_keyboard' => true,
  'one_time_keyboard' => false,
  'selective' => true
  ]),true);

  return $keyboard;
}

function keyboard_learning() {
  
  var_dump($keyboard = json_encode($keyboard = ['keyboard' => [
  ['Instagram','Facebook'],
  ['Telegram','Назад']
  ] ,
  'resize_keyboard' => true,
  'one_time_keyboard' => false,
  'selective' => true
  ]),true);

  return $keyboard;
}

function keyboard_instagram() {
  
  var_dump($keyboard = json_encode($keyboard = ['keyboard' => [
  ['Дизайн','Целевая аудитория'],
  ['Таргет','Назад']
  ] ,
  'resize_keyboard' => true,
  'one_time_keyboard' => false,
  'selective' => true
  ]),true);

  return $keyboard;
}

function keyboard_ref() {
         var_dump($keyboard = json_encode($keyboard = ['keyboard' => [
              ['Получить линк', 'Рефералы'],
              ['Назад']
              ] ,
              'resize_keyboard' => true,
              'one_time_keyboard' => false,
              'selective' => true
              ]),true);
  return $keyboard;
}

function keyboard_admin() {
  
  var_dump($keyboard = json_encode($keyboard = ['keyboard' => [
    ['Разослать всем','Сколько юзеров'],
    ['Инфо', 'Назад']
  ] ,
  'resize_keyboard' => true,
  'one_time_keyboard' => false,
  'selective' => true
  ]),true);

  return $keyboard;
}

function delete_keyboard()
{
  var_dump($keyboard = json_encode($keyboard =  array('remove_keyboard' => true)));
  return $keyboard;
}

function valid() {
  $request_from_telegram = false;
  if(isset($_POST)) {

    $data = file_get_contents("php://input");
      if (json_decode($data) != null)
        $request_from_telegram = json_decode($data,1);
  }
  return $request_from_telegram;
} 

function sendMessage($chat_id,$text,$markup=null) 
{ 

    if (isset($chat_id)) 
    { 
  return get($GLOBALS['url'].'sendMessage?chat_id='.$chat_id.'&text='.urlencode($text).'&reply_markup='.$markup.'&parse_mode=Markdown'); 
    } 

} 

function get($url) 
{ 
$ch = curl_init($url); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
curl_setopt($ch, CURLOPT_HEADER, 0); 
$data = curl_exec($ch); 
curl_close($ch); 
return $data; 
} 

?>