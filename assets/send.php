<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
if (!empty($_POST['website'])) { http_response_code(200); exit; }
if (empty($_POST['privacy_agreed'])) { http_response_code(400); echo 'Privacy policy must be accepted.'; exit; }

$name     = htmlspecialchars(strip_tags(trim($_POST['name'] ?? '')));
$phone    = htmlspecialchars(strip_tags(trim($_POST['phone'] ?? '')));
$email    = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$postcode = htmlspecialchars(strip_tags(trim($_POST['postcode'] ?? '')));
$message  = htmlspecialchars(strip_tags(trim($_POST['message'] ?? '')));
$location = htmlspecialchars(strip_tags(trim($_POST['location'] ?? '')));
$service  = htmlspecialchars(strip_tags(trim($_POST['service'] ?? '')));

if (empty($name) || empty($phone) || empty($email) || empty($message)) {
    http_response_code(400); echo 'Please fill in all required fields.'; exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400); echo 'Invalid email address.'; exit;
}

$to      = 'john@houseclearancejkrs.co.uk';
$subject = 'New Enquiry from houseclearancejkrs.co.uk';
$body    = "Name: $name\nPhone: $phone\nEmail: $email\nPostcode: $postcode\nService: $service\nLocation: $location\n\nMessage:\n$message";
$headers = "From: noreply@houseclearancejkrs.co.uk\r\nReply-To: $email\r\nX-Mailer: PHP/" . phpversion();

if (mail($to, $subject, $body, $headers)) {
    header('Location: /thank-you/'); exit;
} else {
    http_response_code(500); echo 'Mail failed. Please call John directly on 07931 476 744.'; exit;
}
