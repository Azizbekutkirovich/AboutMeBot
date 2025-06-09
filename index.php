<?php

require_once "Telegram.php";

$telegram = new Telegram('8039036628:AAGpZTK49835NAhi0slTYlGil-lrFclFx3g');

$data = $telegram->getData();
$message = $data['message'];
$chat_id = $message['chat']['id'];
$text = $message['text'];
$userId = $message['from']['id'];
$admin_id = "5747477057";

switch ($text) {
	case "/start":
		start();
		break;
	case "🛈 Batafsil ma'lumot":
		detail();
		break;
	case "📄 Rezyume":
		rezyume();
		break;
	case "☎️ Bog'lanish uchun":
		contact();
		break;
	case "🔙 Ortga qaytish":
		back();
		break;
	default:
		$telegram->sendMessage([
			"chat_id" => $chat_id,
			"text" => "Iltimos! Pastdagi tugmalardan birini tanlang!"
		]);
		break;
}

function back() {
	home();
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

function start() {
	global $chat_id, $telegram, $message;
	$first_name = $message['chat']['first_name'] ?? "";
	$last_name = $message['chat']['last_name'] ?? "";
	$content = array('chat_id' => $chat_id, 'text' => "Assalomu aleykum $last_name $first_name. Men dasturchi Safarov Azizbek @anonym_811 haqida ma'lumot bera olaman!");
	$telegram->sendMessage($content);
	sendDataToAdmin();
	home();
}

function sendDataToAdmin() {
	global $message, $telegram, $admin_id, $userId;
	$username = $message['from']['username'];
	$first_name = $message['chat']['first_name'] ?? "";
	$last_name = $message['chat']['last_name'] ?? "";
	$telegram->sendMessage([
		"chat_id" => $admin_id,
		"text" => "Botga @$username foydalanuvchi kirdi! Qo'shimcha ma'lumotlar:
			id: $userId,
			nickname: $first_name $last_name
			Profil: @$username
		"
	]);
}

function home() {
	global $chat_id, $telegram;
	$option = array(
	    array($telegram->buildKeyboardButton("🛈 Batafsil ma'lumot"), $telegram->buildKeyboardButton("📄 Rezyume")),
	    array($telegram->buildKeyboardButton("☎️ Bog'lanish uchun")));
    $keyb = $telegram->buildKeyBoard($option, true, true);
    $content = ["chat_id" => $chat_id, "text" => "Qanday ma'lumot kerak?", "reply_markup" => $keyb];
	$telegram->sendMessage($content);
}

function detail() {
	global $chat_id, $telegram;
	$content = array('chat_id' => $chat_id, 'text' => "Bot asoschisi: @anonym_811
F.I.SH: Safarov Azizbek O'tkir o'g'li
Qisqacha ma'lumot:
2007-yil 25-iyunda Navoiy viloyati, Zarafshon shahrida tug‘ilgan. Dasturlashga bo‘lgan qiziqishi yoshligidan boshlangan. Asosan PHP dasturlash tilida ishlaydi va REST API, JWT autentifikatsiya tizimlari, hamda web-ilovalar yaratishda tajribaga ega.
2023-yildan boshlab mustaqil dasturchi sifatida turli veb-loyihalar ustida ishlay boshlagan. U sun’iy intellekt texnologiyalariga qiziqadi va bu yo‘nalishda o‘z bilimlarini kengaytirib boradi.

Ko‘nikmalari:

PHP (sof PHP va Yii2 framework)

PHP OOP (Obyektga yo'naltirilgan dasturlash)

MySQL

REST API

JWT autentifikatsiya

Git va GitHub

Docker asoslari

Telegram bot yaratish (PHP orqali)

Hozirgi loyihalari:

LARTWA — Login and Register With API (PHP + JWT)

SevaraShop — Kiyim va kosmetikaga ixtisoslashgan internet-do‘kon

OnlineShop — Umumiy maqsadli internet-do‘kon

Quiz-school — O‘quvchilar uchun online test platformasi

AboutMeBot — Telegram bot (Render.com orqali deploy qilingan)

Bu loyihalar barchasi GitHub profilida mavjud
GitHub profiliga havola: https://github.com/Azizbekutkirovich/

Kelajak rejasi:

Sun’iy intellekt bilan integratsiyalashgan web-ilovalar yaratish

PHP framework’larni chuqur o‘rganish

Full Stack Dasturchi sifatida rivojlanish

Bog'lanish uchun:
	☎️ Telefon: +998(93)315-23-70
	✉ Email: azizbek250607@gmail.com
	Instagram: https://www.instagram.com/az1z_0607/
	🐙 GitHub: https://github.com/Azizbekutkirovich/");
	$telegram->sendMessage($content);
	backButton();
}

function contact() {
	global $chat_id, $telegram;
	$content = array('chat_id' => $chat_id, 'text' => "
		📍 Manzil: Toshkent shahar Yangi hayot tumani Ibrat 2-tor ko'cha 38

	 	☎️ Telefon: +998(93)315-23-70

	 	✉ Email: azizbek250607@gmail.com

	 	🐙 GitHub: https://github.com/Azizbekutkirovich/");
	$telegram->sendMessage($content);
	backButton();
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