<?php
// DODO Email Handler with PHPMailer SMTP
// This is a more reliable solution than basic PHP mail()

$to = "selsogroup@gmail.com";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $serviceType = $_POST["serviceType"] ?? '';
    $address = $_POST["address"] ?? '';
    $destination = $_POST["destination"] ?? '';
    $range = $_POST["range"] ?? '';
    
    // Validate input
    if (empty($serviceType) || empty($address) || empty($destination) || empty($range)) {
        $response = "❌ All fields are required.";
        $status = "error";
    } else {
        // Email content
        $subject = "New DODO Booking Request - " . ucfirst($serviceType);
        $message = "New booking request received!\n\n";
        $message .= "Service Type: " . ucfirst($serviceType) . "\n";
        $message .= "Pickup Address: " . $address . "\n";
        $message .= "Destination: " . $destination . "\n";
        $message .= "Offered Amount: $" . $range . "\n";
        $message .= "Request Time: " . date('Y-m-d H:i:s') . "\n";
        $message .= "Customer IP: " . $_SERVER['REMOTE_ADDR'] . "\n\n";
        $message .= "Please contact the customer to confirm the booking.";
        
        // Try to send email using SMTP
        $email_sent = sendEmailWithPHPMailer($to, $subject, $message);
        
        if ($email_sent) {
            $response = "✅ Booking request sent successfully! We will contact you soon.";
            $status = "success";
            error_log("DODO: Booking email sent successfully to $to");
        } else {
            $response = "❌ Failed to send booking request. Please call us at 0415152099.";
            $status = "error";
            error_log("DODO: Failed to send booking email to $to");
        }
    }
} else {
    $response = "❌ Invalid request method.";
    $status = "error";
}

function sendEmailWithPHPMailer($to, $subject, $message) {
    // Check if PHPMailer is available
    if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        // PHPMailer not installed, fall back to basic mail
        error_log("DODO: PHPMailer not found, using basic mail()");
        return sendBasicEmail($to, $subject, $message);
    }
    
    // Use PHPMailer with Gmail SMTP
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'selsogroup@gmail.com'; // Your Gmail
        $mail->Password = 'YOUR_APP_PASSWORD_HERE'; // ⚠️ Replace with Gmail App Password
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Recipients
        $mail->setFrom('selsogroup@gmail.com', 'DODO Booking System');
        $mail->addAddress($to);
        
        // Content
        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body = $message;
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("DODO PHPMailer Error: " . $e->getMessage());
        // Fall back to basic mail if SMTP fails
        return sendBasicEmail($to, $subject, $message);
    }
}

function sendBasicEmail($to, $subject, $message) {
    $headers = "From: noreply@dodo.com\r\n";
    $headers .= "Reply-To: selsogroup@gmail.com\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    return mail($to, $subject, $message, $headers);
}

// Return response
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    header('Content-Type: application/json');
    echo json_encode(['message' => $response, 'status' => $status]);
} else {
    header("Location: dodo.html?message=" . urlencode($response) . "&status=" . $status);
    exit();
}
?>