<?php 
$to = "selsogroup@gmail.com";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $serviceType = $_POST["serviceType"] ?? '';
    $address = $_POST["address"] ?? '';
    $destination = $_POST["destination"] ?? '';
    $range = $_POST["range"] ?? '';
    
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
    
    // Enable error reporting for debugging
    error_reporting(E_ALL);
    
    // Log the attempt
    error_log("DODO: Attempting to send email to $to");
    
    // Send email
    $mail_result = mail($to, $subject, $message, $headers);
    
    if ($mail_result) {
        $response = "✅ Booking request sent successfully! We will contact you soon.";
        $status = "success";
        error_log("DODO: Email queued successfully for $to");
    } else {
        $response = "❌ Failed to send booking request. Please try again or contact us directly.";
        $status = "error";
        error_log("DODO: Email failed to queue for $to - " . error_get_last()['message']);
    }
} else {
    $response = "❌ Invalid request method.";
    $status = "error";
}

// Return JSON response for AJAX or redirect back with message
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    header('Content-Type: application/json');
    echo json_encode(['message' => $response, 'status' => $status]);
} else {
    // Redirect back to the main page with message
    header("Location: dodo.html?message=" . urlencode($response) . "&status=" . $status);
    exit();
}
?>