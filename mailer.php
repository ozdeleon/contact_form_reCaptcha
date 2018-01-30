<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["username"]);
    $phone = trim($_POST["phone"]);
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $message = trim($_POST["message"]);

    if(isset($_POST['g-recaptcha-response'])){
        $captcha = $_POST['g-recaptcha-response'];
    }

    //Validate the data
    if (empty($name) OR empty($phone) OR !filter_var($email, FILTER_VALIDATE_EMAIL) OR empty($message) OR empty($captcha)) {
        http_response_code(400);
        echo "<span class='glyphicon glyphicon-remove' aria-hidden='true'></span> <strong>Please fill all the form inputs and check the captcha to submit.</strong>";
        exit;
    }

    //recipient email address.
    $recipient = "RECIPIENT_EMAIL";

    //email subject.
    $subject = "New message from $name";

    //email content.
    $email_content = "Name: $name\n";
    $email_content .= "Phone: $phone\n\n";
    $email_content .= "Email: $email\n\n";
    $email_content .= "Message:\n$message\n";

    //email headers.
    $email_headers = "From: $name <$email>";

    $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=RECAPTCHA_SECRET_KEY&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
    $decoded_response = json_decode($response, true);

    if($decoded_response['success'] == true)	{
        // Send the email.
        if (mail($recipient, $subject, $email_content, $email_headers)) {
            http_response_code(200);
            echo "<span class='glyphicon glyphicon-ok' aria-hidden='true'></span> <strong>Thank You! Your message has been sent.</strong>";
        } else {
            http_response_code(500);
            echo "Whoa! message could not be sent.";
        }
    } else {
        http_response_code(400);
        echo 'You are a spammer!';
    }

}

?>
