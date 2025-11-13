<?php
// --- CONFIG ---
$LISTMONK_API_URL = "https://listmonk.bellereveretreats.com/api/subscribers";
$LIST_ID = 3;
$LISTMONK_USER = "webform";
$API_TOKEN = "REDACTED_LISTMONK_API_TOKEN";
$THANK_YOU_URL = "/thank-you.html";

// --- GET FORM DATA ---
$name  = trim($_POST['name']) ?? '';
$email = trim($_POST['email']) ?? '';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: $THANK_YOU_URL?error=invalid-email");
    exit;
}

// --- BUILD PAYLOAD ---
$data = [
    "email" => $email,
    "name" => $name,
    "lists" => [$LIST_ID],
    "status" => "enabled",
    "consent" => true,
];

// --- CURL REQUEST ---
$ch = curl_init($LISTMONK_API_URL);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: token $LISTMONK_USER:$API_TOKEN"
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// --- Temporary logging
file_put_contents("/tmp/listmonk_debug.log", date('c') . " | Payload: " . json_encode($data) . "\n", FILE_APPEND);


$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// --- REDIRECT TO THANK-YOU PAGE ---
header("Location: $THANK_YOU_URL");
exit;
?>

