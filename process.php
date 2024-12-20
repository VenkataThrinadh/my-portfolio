<?php
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer files
require '/home/creativeethicsco/public_html/portfolio/PHPMailer/Exception.php';
require '/home/creativeethicsco/public_html/portfolio/PHPMailer/PHPMailer.php';
require '/home/creativeethicsco/public_html/portfolio/PHPMailer/SMTP.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $name = htmlspecialchars(trim($_POST["fullname"]));
    $phone = htmlspecialchars(trim($_POST["phone"]));
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $subject = htmlspecialchars(trim($_POST["subject"])); // Added subject
    $message_content = htmlspecialchars(trim($_POST["message"]));

    // Check if email is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format";
        exit;
    }

    // Default subject if none is provided
    if (empty($subject)) {
        $subject = "Contact Form Submission";
    }

    // Destination email address
    $to = "venkatathrinadh8@gmail.com";

    // SMTP configuration
    $mail = new PHPMailer(true);
    try {
        $mail->SMTPDebug = 2; // Enable detailed debug output
        $mail->Debugoutput = 'html'; // Output debug information in HTML format

        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'venkatathrinadh8@gmail.com'; // Replace with your SMTP username (mail-id)
        $mail->Password   = 'lwjh jujn trht zjqd'; // Replace with your SMTP password (mail-password)
        $mail->SMTPSecure = 'tls'; // tls or ssl
        $mail->Port       = 587; // 465 for ssl, 587 for tls

        // Skip SSL certificate verification (for testing purposes)
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Recipients
        $mail->setFrom($email, $name);
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject; // Use dynamic subject
        $mail->Body    = "<html><body><h2>Contact Form Submission</h2><table style='border:1px solid #000; border-collapse: collapse;'><tr><td style='border:1px solid #000'><b>Name:</b></td><td style='border:1px solid #000'>$name</td></tr><tr><td style='border:1px solid #000'><b>Email:</b></td><td style='border:1px solid #000'>$email</td></tr><tr><td style='border:1px solid #000'><b>Phone:</b></td><td style='border:1px solid #000'>$phone</td></tr><tr><td style='border:1px solid #000'><b>Message:</b></td><td style='border:1px solid #000'>$message_content</td></tr></table></body></html>";

        // Send the email
        $mail->send();

        // Redirect after successful submission
        header("Location: ./thankyou.html");
        exit;

    } catch (Exception $e) {
        // Display error message if email sending fails
        echo "Email sending failed: {$mail->ErrorInfo}";
    }
}
?>
