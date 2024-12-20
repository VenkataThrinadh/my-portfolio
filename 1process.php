<?php
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include database connection file
require './connection.php';

// Check if connection is successful
if (!$conn) {
    echo("Connection failed: " . mysqli_connect_error());
    exit;
} else {
    echo "Connected successfully<br>";
}

// Include PHPMailer files
require './PHPMailer/Exception.php';
require './PHPMailer/PHPMailer.php';
require './PHPMailer/SMTP.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $name = htmlspecialchars(trim($_POST["fullname"]));
    $phone = htmlspecialchars(trim($_POST["phone"]));
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $subject = htmlspecialchars(trim($_POST["subject"]));
    $message_content = htmlspecialchars(trim($_POST["message"]));

    // Check if email is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format";
        exit;
    }

    // Check if the email already exists in the database
    $query = "SELECT email FROM Email_Data WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt === false) {
        echo "Prepared Statement Error: " . mysqli_error($conn) . "<br>";
        exit;
    }

    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        echo "<script>alert('This email address has already been used. Please use a different email address.');</script>";
        mysqli_stmt_close($stmt);
        exit;
    }
    mysqli_stmt_close($stmt);

    // Subject
    $subject = "Contact Form Submission";

    // Destination email address
    $to = "codeccrafter@gmail.com";

    // SMTP configuration
$mail = new PHPMailer(true);
try {
    $mail->SMTPDebug = 2; // Enable detailed debug output
    $mail->Debugoutput = 'html'; // Output debug information in HTML format

    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'ind10.gofordns.com'; // Use the correct domain name
    $mail->SMTPAuth   = true;
    $mail->Username   = 'creative@grvco.in'; // Replace with your SMTP username
    $mail->Password   = 'Creative@2024'; // Replace with your SMTP password
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
    $mail->Subject = $subject;
    $mail->Body    = "<html><body><h2>Contact Form Submission</h2><table style='border:1px solid #000; border-collapse: collapse;'><tr><td style='border:1px solid #000'><b>Name:</b></td><td style='border:1px solid #000'>$name</td></tr><tr><td style='border:1px solid #000'><b>Email:</b></td><td style='border:1px solid #000'>$email</td></tr><tr><td style='border:1px solid #000'><b>Phone:</b></td><td style='border:1px solid #000'>$phone</td></tr><tr><td style='border:1px solid #000'><b>Subject:</b></td><td style='border:1px solid #000'>$subject</td></tr><tr><td style='border:1px solid #000'><b>Message:</b></td><td style='border:1px solid #000'>$message_content</td></tr></table></body></html>";

    // Send the email
    $mail->send();

    // Insert data into database using prepared statements
    $query = "INSERT INTO Email_Data (name, phone, email, subject, message) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt === false) {
        echo "Prepared Statement Error: " . mysqli_error($conn) . "<br>";
    } else {
        mysqli_stmt_bind_param($stmt, 'ssss', $name, $phone, $email, $subject, $message_content);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Data inserted successfully');</script>";
            // Redirect after successful submission
            header("Location: thankyou.html");
            exit;
        } else {
            echo "<script>alert('There is an error: " . mysqli_stmt_error($stmt) . "');</script>";
        }

        mysqli_stmt_close($stmt);
    }

} catch (Exception $e) {
    // Display error message if email sending fails
    echo "Email sending failed: {$mail->ErrorInfo}";
}
}
?>
