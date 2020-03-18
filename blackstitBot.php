<?php
ini_set('log_errors', 'On');
ini_set('error_log', 'php_errors.log');

include('config.php');
include('function.php');
include('mysql.php');

echo get($url.'setWebhook?url='.$webhook);


if (($json = valid()) == false) { exit();}

if (isset($json['callback_query'])) {
    $callback_data = $json['callback_query']['data'];
    $uid = $json['callback_query']['message']['chat']['id'];
}else {
    $uid = $json['message']['from']['id'];
    $first_name = $json['message']['from']['first_name'];
    $username = $json['message']['from']['username'];
    $date = $json['message']["date"];
    $msgid = $json['message']['message_id'];
    $text = $json['message']['text'];
}


$message_array = array(
    "user_id" => $uid,
    "username" => $username,
    "first_name" => $first_name,
    "message_id" => $msgid,
    "text" => $text,
    "date" => $date,
    "ndate" => gmdate("d.m.Y H:i:s", $date)
);

addUser($uid, $username, $first_name);
MessageSave($message_array);

// Проверим статус пользователя
$user_info = userget($uid); // извлекаем информацию о пользователе из базы данных
$user_status = $user_info['status']; // извлекаем статус из массива
$user_admin = $user_info['admin']; // проверка админа

//sendMessage($uid, "Твой статус на момент обработки запроса: ".$user_status);



///////////// referal init /////////////////////////////
$ref = parseReferal($text);  // извлечение ref
if ($ref) {
    RefSave($uid, $ref, $first_name, $username);
    $hello = "Привлеки двух юзеров и получи приватный линк";
} // сохраняем реф
/////////////////////////////////////////////////////////
$referals = getReferal($uid);


$ANSWER = "\n_команда не определена...попробуй _ /start ";//.$first_name;

switch ($text) {

    case ($text == '/update'):
        $ANSWER = "Bot version 1.5.3 
        На самом деле я уже задолбался и время 3 часа ночи
        все изменения напишу потом, они есть, но это больше 
        подкопотка и админка ;)
        нужно разобраться с ilnine";

        break;

    case ($text == '/help' || $text == 'Помощь'):
        $ANSWER = "Добро пожаловать в раздел Помощи! Твой идентификатор: " . $uid;
        $keyboard = keyboard();
        updateColumns(array("status" => "0"), $uid);
        break;

//    case '/reset':
//        $ANSWER = "Клавиатура сброшена!";
//        $keyboard = delete_keyboard();
//        break;

    case '/pidor':
        $ANSWER = $first_name . ", ну ты пидр!😁
а что ты еще ожидал?";
        break;


    case 'Обучение':
        $ANSWER = "Ты в разделе Обучения.";
        $keyboard = keyboard_learning();
        updateColumns(array("status" => "2"), $uid);
        break;


    case 'Instagram':
        $ANSWER = "Ты в разделе Instagram.";
        $keyboard = keyboard_instagram();
        updateColumns(array("status" => "3"), $uid);
        break;

// если статус = 2, значит он в разделе "Обучение", вернем его в основное меню и обновим статус на 0
//Если статус = 3, значит он в разделе Instagram, вернем его в раздел Обучения и дадим статус 2
    case 'Назад':
        $ANSWER = "Отлично! Верну тебя на один пункт назад";

        if ($user_status == 2) {
            $keyboard = keyboard();
            updateColumns(array("status" => "0"), $uid);
        } else if ($user_status == 3) {
            $keyboard = keyboard_learning();
            updateColumns(array("status" => "2"), $uid);
        }
        break;
    case ($text == '/author'):
        $ANSWER = 'Автором всего этого являюсь я';
        $keyboard = inline_keyboard();
        break;


    case '/start':
        $ANSWER = "Привет, " . $first_name . "  Ты пидор! И вас таких уже - " .$users =15 + count(getUsers('users')). "
      Но спасибо что пришел посмотреть)
      Список комманд для бота:
      /start - ну это понятно
      /update - Проверить версию бота и прочитать о нововведениях
      /help - бесполезная хуйня но чекни
      /send - Можешь попробовать, но это только админам
      /pidor - Проверка на пидора 😁
      /author - Ссылочка на меня )
      На самом деле есть еще, но там ничего интересного)
      Потом научусь подрубать сюда платежку и можно даже 
      что-то продавать 🌚🌚🌚🌚
      
      P.S Если вдруг что-то не так, напиши /start
      P.P.S Это мой первый бот
      ";
        $keyboard = keyboard();
        break;
}

// Кнопки Instagram
switch ($text) {
    case 'Дизайн':
        $ANSWER = "https://telegra.ph/Test-bota-03-13";
        updateColumns(array("status" => "3"), $uid);
        break;
    case 'Целевая аудитория':
        $ANSWER = "тут должна быть ссылка";
        updateColumns(array("status" => "3"), $uid);
        break;
    case 'Таргет':
        $ANSWER = "тут должна быть ссылка";
        updateColumns(array("status" => "3"), $uid);
        break;

}

// Кнопки админки

switch ($text) {
    case ($user_admin == '1' and $user_status == '30'):
        updateColumns(array("admin" => "1"), $text);
        $ANSWER = "Администратор назначен";
        updateColumns(array("status" => "0"), $uid);
        break;

    case ($text == '/addadmin' && $user_admin == '1'):
        $ANSWER = "Пришлите ID...";
        updateColumns(array("status" => "30"), $uid);
        break;

    case ($user_admin == '1' and $user_status == '31'):
        updateColumns(array("admin" => "0"), $text);
        $ANSWER = "Администратор разжалован";
        updateColumns(array("status" => "0"), $uid);
        break;

    case ($text == '/deladmin' && $user_admin == '1'):
        $ANSWER = "Пришлите ID...";
        updateColumns(array("status" => "31"), $uid);
        break;

    case ($user_admin == '1' and $user_status == '50'):
        $keyboard = inline_keyboard();
        $user_info =  getLogin($text);
        updateColumns(array("status" => "0"), $uid);
        $ANSWER = "ID - " .$user_info['id'] . " | Имя - " .$user_info['first_name'] . " | Дата регистрации - " .$user_info['date_begin'] . " | Админка - " .$user_info['admin'];
        break;

    case($text == '/info'  || $text == 'Инфо'):
        $keyboard = delete_keyboard();
        $ANSWER = "Пришлите логин...";
        updateColumns(array("status" => "50"), $uid);

        break;

    case ($text == '/admin'):
        if ($user_admin == '1') {
            $ANSWER = 'Здравствуй, администратор!';
            $keyboard = keyboard_admin();
            updateColumns(array("status" => "2"), $uid);
        }else {
            $ANSWER = "Ты не админ, ты пидор! \nТебе сюда нельзя!";
        }
        break;

    case ($text == 'Сколько юзеров' && $user_admin == '1'):
        $users = getUsers('users');
        $ANSWER = "\nВсего юзеров: " . count($users);
        break;

    case ($user_admin == '1' and $user_status == '100'):
        $users = getUsers('users');

        $ANSWER = "Отлично, рассылаю текст:\n\n" . $text . "\n
			на всех пользователей.\n\nВсего пользователей: " . count($users);
        sendMessage($uid, $ANSWER);

        updateColumns(array("status" => "0"), $uid);
        for ($i = 0; $i < count($users); $i++) {
            sendMessage($users[$i], $text);
        }
        $ANSWER = "Рассылка успешно отправлена!";
        break;


    case ($text == '/send' || $text == 'Разослать всем'):
        if ($user_admin == '1') {
            $ANSWER = 'Пришлите текст для рассылки...';
            updateColumns(array("status" => "100"), $uid);
        } else {
            $ANSWER = 'Ты не админ, ты пидор! тебе сюда нельзя! Только я могу рассылать сообщения!';
        }
        break;

}

switch ($callback_data){

    case '/in_hello':
        $ANSWER = "Сработала кнопка приветствия!";
        break;

    case '/in_bye':
        $ANSWER = "Сработала кнопка прощания!";
        break;
}

// Кнопки рефералки

switch ($text){
    case ($text == '/ref' || $text == 'Реферал'):
        $ANSWER = "Привлеки двух юзеров и получи приватный линк";
        $keyboard = keyboard_ref();
        updateColumns(array("status" => "2"), $uid);
        break;
    case "Получить линк":
        if ($referals < 2) { // Устанавливаем нужное число рефералов
            $ANSWER =
                "Чтобы получить приватный линк, необходимо, чтобы по ваше ссылке перешло *всего 2 человека*.\n
Привлечено:" . $referals . "\n
_Ваша реферальная ссылка:\n_ t.me/" . $BOT_USERNAME . "?start=" . $uid;

        } else {
            $ANSWER = 'http://google.com';
        }
        break;

    case "Рефералы":
        $ANSWER = "\nПривлечено рефералов: " . $referals;
        break;
}


sendMessage($uid, $ANSWER, $keyboard);

// Проверим статус пользователя
//$user_info =  userget($uid); // извлекаем информацию о пользователе из базы данных
//$user_status = $user_info['status']; // извлекаем статус из массива
// sendMessage($uid, "Твой статус после обработки запроса: ".$user_status);

?>