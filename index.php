<?php

require_once "Telegram.php";
require_once "db.php";


// $query = $pdo->prepare("INSERT INTO zakaz (user_id, phone_Number) VALUES (?, ?)");
// $query->execute([5667777776, "+998933152370"]);

$telegram = new Telegram('7712252153:AAE9ZG7gCLWT3E3jJCnGOclp82-3OFjO2So');

$data = $telegram->getData();
$message = $data['message'];
$chat_id = $message['chat']['id'];
$text = $message['text'];
$userId = $message['from']['id'];

switch ($text) {
	case "/start":
		start();
		addUserPage("start");
		break;
	case "🛈 Batafsil ma'lumot":
		detail();
		changePage("detail");
		break;
	case "📄 Rezyume":
		rezyume();
		changePage("rezyume");
		break;
	case "📞 Bog'lanish uchun":
		contact();
		changePage("contact");
		break;
	case "🤖 Bot zakaz qilish":
		zakazBot();
		changePage("zakazBot");
		break;
	case "🔙 Ortga qaytish":
		back();
		break;
	default:
		if ((!empty($message['entities']) && $message['entities'][0]['type'] === "phone_number") || !empty($message['contact'])) {
			$phoneNumber = $message['contact']['phone_number'] ?? $message['text'];
			$username = $message['chat']['username'];
			if (!isZakaz()) {
				$query = $pdo->prepare("INSERT INTO zakaz (userId, phoneNumber, username) VALUES (?, ?, ?)");
				$query->execute([$userId, $phoneNumber, $username]);
				$telegram->sendMessage([
					"chat_id" => $chat_id,
					"text" => "Tez orada siz bilan bog'lanishadi!"
				]);
			} else {
				$query = $pdo->prepare("UPDATE zakaz SET phoneNumber = ?, username = ? WHERE userId = ?");
				$query->execute([$phoneNumber, $username, $userId]);
				$telegram->sendMessage([
					"chat_id" => $chat_id,
					"text" => "Tez orada siz bilan bog'lanishadi!"
				]);
			}
		}
		break;
}

function changePage($newPage) {
	global $pdo, $userId;
	$query = $pdo->prepare("UPDATE userPage SET page = ? WHERE userId = ?");
	$query->execute([$newPage, $userId]);
}

function getUserPage() {
	global $userId, $pdo;
	$query = $pdo->prepare("SELECT page FROM userPage WHERE userId = ?");
	$query->execute([$userId]);
	$data = $query->fetch(PDO::FETCH_ASSOC);
	return $data['page'];
}

function back() {
	$user_page = getUserPage();
	home();
	changePage("start");
}

function backButton() {
	global $chat_id, $telegram;
	$option = [
		[$telegram->buildKeyboardButton("🔙 Ortga qaytish")]
	];
	$keyb = $telegram->buildKeyBoard($option, true, true);
	$telegram->sendMessage([
		"chat_id" => $chat_id,
		"text" => "Ortga qaytish uchun pastdagi tugmani bosing",
		"reply_markup" => $keyb
	]);
}

function addUserPage($page) {
	global $pdo, $userId;
	if (!issetUserPage()) {
		$query = $pdo->prepare("INSERT INTO userPage (userId, page) VALUES (?, ?)");
		$query->execute([$userId, $page]);
	} else {
		changePage($page);
	}
}

function issetUserPage() {
	global $pdo, $userId;
	$query = $pdo->prepare("SELECT * FROM userPage WHERE userId = ?");
	$query->execute([$userId]);
	$row = $query->fetch(PDO::FETCH_ASSOC);
	return $row !== false;
}

function start() {
	global $chat_id, $telegram, $message;
	$first_name = $message['chat']['first_name'] ?? "";
	$last_name = $message['chat']['last_name'] ?? "";
	$content = array('chat_id' => $chat_id, 'text' => "Assalomu aleykum $last_name $first_name. Men dasturchi Safarov Azizbek haqida ma'lumot bera olaman!");
	$telegram->sendMessage($content);
	home();
}

function home() {
	global $chat_id, $telegram;
	$option = array(
	    array($telegram->buildKeyboardButton("🛈 Batafsil ma'lumot"), $telegram->buildKeyboardButton("📄 Rezyume")),
	    array($telegram->buildKeyboardButton("📞 Bog'lanish uchun"), $telegram->buildKeyboardButton("🤖 Bot zakaz qilish")));
    $keyb = $telegram->buildKeyBoard($option, true, true);
    $content = ["chat_id" => $chat_id, "text" => "Qanday ma'lumot kerak?", "reply_markup" => $keyb];
	$telegram->sendMessage($content);
}

function detail() {
	global $chat_id, $telegram;
	$content = array('chat_id' => $chat_id, 'text' => "Batafsil ma'lumot uchun havola: <a href='https://telegra.ph/Biz-haqimizda-05-06'>Havola</a>", "parse_mode" => "html");
	$telegram->sendMessage($content);
	backButton();
}

function contact() {
	global $chat_id, $telegram;
	$content = array('chat_id' => $chat_id, 'text' => "
		📍 Адрес: Toshkent shahar Yangi hayot tumani Ibrat 2-tor ko'cha 38

	 	📞 Телефон: +998(93)315-23-70

	 	✉ Email: azizbek250607@gmail.com

	 	🐙 GitHub: https://github.com/Azizbekutkirovich/");
	$telegram->sendMessage($content);
	backButton();
}

function zakazBot() {
	global $chat_id, $telegram;
	$option = [
		[$telegram->buildKeyboardButton("Raqam qoldirish", true)]
	];
	$keyb = $telegram->buildKeyBoard($option, true, true);
	$content = array("chat_id" => $chat_id, "text" => "Telefon raqamingizni yozing", "reply_markup" => $keyb);
	$telegram->sendMessage($content);
}

function rezyume() {
	global $chat_id, $telegram;
	$content = array("chat_id" => $chat_id, "text" => "Rezyume tez orada qo'shiladi!");
	$telegram->sendMessage($content);
	backButton();
}

function isZakaz() {
	global $pdo, $userId;
	$query = $pdo->prepare("SELECT * FROM zakaz WHERE userId = ?");
	$query->execute([$userId]);
	$row = $query->fetch(PDO::FETCH_ASSOC);
	return $row !== false;
}