<?php
// Enhanced DODO Email Handler with SMTP Support
// Choose between basic mail() or SMTP

// Configuration
$USE_SMTP = false; // Set to true to use SMTP instead of basic mail()

// SMTP Configuration (if using SMTP)
$SMTP_CONFIG = [
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'username' => 'selsogroup@gmail.com',
    'password' => 'your-app-password', // Use App Password for Gmail
    'encryption' => 'tls'
];

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
        // Your email address
        $to = "selsogroup@gmail.com";
        
        // Email subject
        $subject = "New DODO Booking Request - " . ucfirst($serviceType);
        
        // Create email message
        $message = "New booking request received!\n\n";
        $message .= "Service Type: " . ucfirst($serviceType) . "\n";
        $message .= "Pickup Address: " . $address . "\n";
        $message .= "Destination: " . $destination . "\n";
        $message .= "Offered Amount: $" . $range . "\n";
        $message .= "Request Time: " . date('Y-m-d H:i:s') . "\n";
        $message .= "Customer IP: " . $_SERVER['REMOTE_ADDR'] . "\n\n";
        $message .= "Please contact the customer to confirm the booking.";
        
        // Email headers
        $headers = "From: noreply@dodo.com\r\n";
        $headers .= "Reply-To: noreply@dodo.com\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        // Send email
        if ($USE_SMTP) {
            // SMTP sending (requires PHPMailer or similar)
            $email_sent = sendSMTPEmail($to, $subject, $message, $SMTP_CONFIG);
        } else {
            // Basic PHP mail
            $email_sent = mail($to, $subject, $message, $headers);
        }
        
        if ($email_sent) {
            $response = "✅ Booking request sent successfully! We will contact you soon.";
            $status = "success";
            
            // Optional: Log successful booking
            error_log("DODO Booking: " . $serviceType . " from " . $_SERVER['REMOTE_ADDR']);
        } else {
            $response = "❌ Failed to send booking request. Please try again.";
            $status = "error";
            
            // Log error
            error_log("DODO Email Error: Failed to send booking email");
        }
    }
} else {
    $response = "❌ Invalid request method.";
    $status = "error";
}

// Function for SMTP sending (basic implementation)
function sendSMTPEmail($to, $subject, $message, $config) {
    // This is a placeholder for SMTP implementation
    // In production, you would use PHPMailer or SwiftMailer
    // For now, fall back to basic mail
    return mail($to, $subject, $message, "From: " . $config['username']);
}

// Return response
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    header('Content-Type: application/json');
    echo json_encode(['message' => $response, 'status' => $status]);
} else {
    // Redirect back to the main page with message
    header("Location: dodo.html?message=" . urlencode($response) . "&status=" . $status);
    exit();
}
?>