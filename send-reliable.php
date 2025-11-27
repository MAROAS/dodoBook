<?php
// Simple and Reliable Email Solution for DODO
// This handles common email delivery issues

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
        $subject = "DODO Booking: " . ucfirst($serviceType) . " Request";
        
        // Create a more email-friendly message
        $message = "=== NEW DODO BOOKING REQUEST ===\n\n";
        $message .= "Service: " . strtoupper($serviceType) . "\n";
        $message .= "From: " . $address . "\n";
        $message .= "To: " . $destination . "\n";
        $message .= "Amount Offered: $" . $range . "\n";
        $message .= "Date: " . date('l, F j, Y') . "\n";
        $message .= "Time: " . date('g:i A') . "\n";
        $message .= "Customer IP: " . $_SERVER['REMOTE_ADDR'] . "\n";
        $message .= "\n";
        $message .= "Action Required: Contact customer to confirm booking\n";
        $message .= "Phone: 0415152099\n";
        $message .= "\n";
        $message .= "--- End of Request ---";
        
        // Improved headers to avoid spam filters
        $headers = array();
        $headers[] = "From: DODO Booking <noreply@" . $_SERVER['HTTP_HOST'] . ">";
        $headers[] = "Reply-To: selsogroup@gmail.com";
        $headers[] = "Return-Path: selsogroup@gmail.com";
        $headers[] = "Content-Type: text/plain; charset=UTF-8";
        $headers[] = "X-Mailer: DODO-PHP/" . phpversion();
        $headers[] = "X-Priority: 1";
        $headers[] = "Importance: High";
        
        $header_string = implode("\r\n", $headers);
        
        // Log the attempt
        error_log("DODO: Attempting to send booking email to $to");
        error_log("DODO: Subject: $subject");
        
        // Send email with error checking
        $mail_sent = @mail($to, $subject, $message, $header_string);
        
        if ($mail_sent) {
            // Also try sending a copy to the system
            @mail("noreply@" . $_SERVER['HTTP_HOST'], "DODO Booking Copy", $message, $header_string);
            
            $response = "✅ Booking request submitted! We'll contact you soon at 0415152099.";
            $status = "success";
            error_log("DODO: Email sent successfully - Service: $serviceType, Amount: $range");
        } else {
            $response = "❌ Email system unavailable. Please call us directly at 0415152099.";
            $status = "error";
            error_log("DODO: Email failed - Service: $serviceType, Amount: $range, Error: " . print_r(error_get_last(), true));
        }
        
        // Backup: Log all bookings to a file
        $log_entry = date('Y-m-d H:i:s') . " | $serviceType | $address | $destination | $$range | " . $_SERVER['REMOTE_ADDR'] . "\n";
        file_put_contents('booking-log.txt', $log_entry, FILE_APPEND | LOCK_EX);
    }
} else {
    $response = "❌ Invalid request method.";
    $status = "error";
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