<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
if (!empty($_POST['_honeypot'])) { header('Location: /thank-you/'); exit; }

$name     = htmlspecialchars(strip_tags(trim($_POST['name']     ?? '')));
$phone    = htmlspecialchars(strip_tags(trim($_POST['phone']    ?? '')));
$email    = htmlspecialchars(strip_tags(trim($_POST['email']    ?? '')));
$postcode = htmlspecialchars(strip_tags(trim($_POST['postcode'] ?? '')));
$message  = htmlspecialchars(strip_tags(trim($_POST['message']  ?? '')));
$service  = htmlspecialchars(strip_tags(trim($_POST['service']  ?? '')));
$location = htmlspecialchars(strip_tags(trim($_POST['location'] ?? '')));

if (empty($name) || empty($phone) || empty($postcode) || empty($message)) {
    http_response_code(400);
    echo 'Please fill in all required fields.';
    exit;
}

require '/var/www/vhosts/houseclearancejkrs.co.uk/private/smtp.php';
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = SMTP_PORT;

    $mail->setFrom(SMTP_FROM, 'JKRS House Clearance Website');
    $mail->addAddress(SMTP_TO);
    if (!empty($email)) {
        $mail->addReplyTo($email, $name);
    }

    $mail->Subject = 'New Enquiry – ' . $name . ' – ' . $postcode;
    $mail->Body    =
        "Name:     $name\n" .
        "Phone:    $phone\n" .
        "Email:    $email\n" .
        "Postcode: $postcode\n" .
        "Service:  $service\n" .
        "Location: $location\n\n" .
        "Message:\n$message";

    $mail->send();
    header('Location: /thank-you/');
    exit;

} catch (Exception $e) {
    error_log('JKRS mailer error: ' . $mail->ErrorInfo);
    http_response_code(500);
    echo 'Mail failed. Please call us on 07931 476 744.';
    exit;
}
