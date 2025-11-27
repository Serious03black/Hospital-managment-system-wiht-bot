<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../include/config.php';

if (!isset($_SESSION['login']) || strlen($_SESSION['login']) === 0) {
	http_response_code(401);
	echo json_encode(['error' => 'Unauthorized request. कृपया दोबारा लॉगिन करें।']);
	exit;
}

$apiKey = defined('GEMINI_API_KEY') ? GEMINI_API_KEY : '';

if ($apiKey === '') {
	http_response_code(500);
	echo json_encode(['error' => 'error']);
	exit;
}

$rawInput = file_get_contents('php://input');
$payload = json_decode($rawInput, true);

if (!is_array($payload)) {
	http_response_code(400);
	echo json_encode(['error' => 'error']);
	exit;
}

$message = isset($payload['message']) ? trim($payload['message']) : '';

if ($message === '') {
	http_response_code(400);
	echo json_encode(['error' => 'कृपया कोई प्रश्न पूछें।']);
	exit;
}

$historyPayload = [];
if (!empty($payload['history']) && is_array($payload['history'])) {
	foreach ($payload['history'] as $entry) {
		if (!is_array($entry) || empty($entry['text'])) {
			continue;
		}
		$role = strtolower($entry['role'] ?? 'user') === 'assistant' ? 'model' : 'user';
		$text = trim($entry['text']);
		if ($text === '') {
			continue;
		}
		$historyPayload[] = [
			'role'  => $role,
			'parts' => [
				['text' => $text],
			],
		];
	}
	$historyPayload = array_slice($historyPayload, -8);
}

$historyPayload[] = [
	'role'  => 'user',
	'parts' => [
		['text' => $message],
	],
];

$systemPrompt = 'You are a concise, friendly bilingual (Hindi+English) assistant for the Hospital Management System portal. Your name is Nisha ' .
	'Guide patients through choose correct category from the andguide the people with his problem and help them to solve their problem.portal features like booking appointments, updating profiles, and viewing history. ' .
	'Never invent data from the medical database; instead, tell users where to find it in the portal. dont use any symbole like "" or / or ** etc. and use only hindi and english language and talk like friendly and helpfull manner.
	assistance answerquestion from the hospital management system portal and dont answer any question that is not related to the hospital management system portal. first ask user about launguage and then answer the question.then talk in that launguage only.';

$requestBody = [
	'contents'          => array_merge(
		[
			[
				'role'  => 'user',
				'parts' => [
					['text' => $systemPrompt],
				],
			],
		],
		$historyPayload
	),
	'generationConfig'  => [
		'temperature'     => 0.4,
		'topP'            => 0.8,
		'maxOutputTokens' => 512,
	],
];

$model = GEMINI_MODEL ?: 'gemini-1.5-flash-latest';
$apiVersion = strpos($model, 'flash') !== false || strpos($model, 'pro') !== false ? 'v1' : 'v1beta';
$apiUrl = sprintf(
	'https://generativelanguage.googleapis.com/%s/models/%s:generateContent?key=%s',
	$apiVersion,
	urlencode($model),
	urlencode($apiKey)
);

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
curl_setopt($ch, CURLOPT_TIMEOUT, 20);

$apiResponse = curl_exec($ch);
$curlError = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($apiResponse === false) {
	http_response_code(500);
	echo json_encode(['error' => 'API अनुरोध विफल: ' . $curlError]);
	exit;
}

$decoded = json_decode($apiResponse, true);

if ($httpCode >= 400 || isset($decoded['error'])) {
	$message = $decoded['error']['message'] ?? 'error';
	http_response_code(502);
	echo json_encode(['error' => $message]);
	exit;
}

$reply = $decoded['candidates'][0]['content']['parts'][0]['text'] ?? '';

if ($reply === '') {
	http_response_code(502);
	echo json_encode(['error' => 'error']);
	exit;
}

echo json_encode([
	'reply' => $reply,
	'usage' => $decoded['usageMetadata'] ?? null,
]);

