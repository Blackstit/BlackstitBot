<?php

function getReferal($uid)
{
    $referals = 0;

    $pdo = connect();
    if ($pdo) {
        $stmt = $pdo->prepare('SELECT * FROM ref WHERE owner = :owner and referals <> :id');
        $stmt->execute(array("owner" => $uid, "id" => $uid));
        $referals = $stmt->rowCount();
    }
    return $referals;
}


function RefSave($uid, $refs, $first_name, $username)
{

    $referals = 0;
    $pdo = connect();
    if ($pdo) {

        $stmt = $pdo->prepare('SELECT owner FROM ref WHERE referals = ?');
        $stmt->execute([$uid]);
        $referals = $stmt->rowCount();
        if ($referals == 0) {
            $data = array("owner" => $refs, "referals" => $uid,
                "first_name" => $first_name, "username" => $username,
                "date_begin" => gmdate("d.m.Y H:i:s", time() + (3 * 60 * 60)));
            $st = $pdo->prepare("INSERT INTO  ref (owner, referals, first_name, username, date_begin) 
          VALUES(:owner, :referals, :first_name, :username, :date_begin)");
            $st->execute($data);
        }
        $stmt = $pdo->prepare('SELECT referals FROM ref WHERE owner = ?');
        $stmt->execute([$uid]);
        $referals = $stmt->rowCount();
    }
    return $referals;
}


function getUsers($table)
{
    $i = 0;
    $pdo = connect();
    if ($pdo) {
        $stmt = $pdo->prepare("SELECT distinct(id) FROM {$table}");
        $stmt->execute();
        foreach ($stmt as $row) {
            $arr[$i] = $row['id'];
            $i++;
        }
        return $arr;
    }
}


function connect()

{
    $host = 'localhost';
    $db = 'id12137289_blackstitbot'; // Имя БД
    $user = 'id12137289_bot';  // Имя пользователя БД
    $pass = 'Qwekoil123'; // Пароль БД
    $charset = 'utf8';
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $opt = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    try {
        $pdo = new PDO($dsn, $user, $pass, $opt);

        return $pdo;
    } catch (PDOException $e) {
        echo 'Подключение не удалось: ' . $e->getMessage();
        return false;
        // die('Подключение не удалось: ' . $e->getMessage());
    }
}


function updateColumns($data, $uid)
{
    $pdo = connect();
    foreach ($data as $key => $value) {

        if ($value != null && $value != "null" && $value != "") {

            $val = array(
                "value" => $value,
                "uid" => $uid
            );
            $st = $pdo->prepare("UPDATE users  set {$key} = :value where id = :uid;");
            $st->execute($val);
        }
    }
    return true;
}


function MessageSave($data)
{
    $pdo = connect();
    if ($pdo) {

        $st = $pdo->prepare("INSERT INTO  messages (user_id, username, first_name, message_id, text, date,ndate)
				VALUES (:user_id, :username, :first_name, :message_id, :text, :date, :ndate)");
        $st->execute($data);

        return true;

    } else {
        return false;
    }
}

function addUser($uid, $username, $first_name)
{

    $pdo = connect();
    if ($pdo) {

        //users
        $stmt = $pdo->prepare('SELECT id FROM users WHERE id = ?');
        $stmt->execute([$uid]);

        if ($stmt->rowCount() == 0) // юзера нет - пишем
        {

            $data = array("id" => $uid, "username" => $username, "first_name" => $first_name,
                "date_begin" => gmdate("d.m.Y H:i:s", time() + (3 * 60 * 60)));
            $st = $pdo->prepare("INSERT INTO  users (id, username, first_name,status, date_begin) VALUES(:id, :username, :first_name,0, :date_begin)");
            $st->execute($data);
            account_init($uid);// Инициализация баланса
        }
    }
}

function userget($uid)
{
    $pdo = connect();
    if ($pdo) {
        $stmt = $pdo->prepare('SELECT username, first_name, status, admin FROM users WHERE id = ?');
        $stmt->execute([$uid]);
        $stat = [];
        foreach ($stmt as $row) {
            $stat['username'] = $row['username'];
            $stat['admin'] = $row['admin'];
            $stat['first_name'] = $row['first_name'];
            $stat['status'] = $row['status'];
        }
        return $stat;
    }
    return false;
}


function getLogin($username)
{
    $pdo = connect();
    if ($pdo) {
        $stmt = $pdo->prepare('SELECT id, first_name, date_begin, admin FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $stat = [];
        foreach ($stmt as $row) {
            $stat['id'] = $row['id'];
            $stat['admin'] = $row['admin'];
            $stat['first_name'] = $row['first_name'];
            $stat['date_begin'] = $row['date_begin'];
        }
        return $stat;
    }
    return false;
}

?>