<?php
echo "<h2>🔧 DODO PHP Environment Test</h2>";

// Check PHP version
echo "<h3>1. PHP Version Check</h3>";
echo "PHP Version: " . phpversion() . "<br>";
if (version_compare(phpversion(), '7.4.0', '>=')) {
    echo "✅ PHP version is good!<br>";
} else {
    echo "⚠️ Consider upgrading PHP for better performance<br>";
}

// Check if mail function exists
echo "<h3>2. Mail Function Check</h3>";
if (function_exists('mail')) {
    echo "✅ mail() function is available<br>";
} else {
    echo "❌ mail() function is NOT available - Email won't work!<br>";
}

// Check POST support
echo "<h3>3. POST Method Check</h3>";
echo "POST supported: " . (in_array('POST', get_defined_vars()) ? '✅ Yes' : '✅ Yes (default)') . "<br>";

// Check required extensions
echo "<h3>4. Required Extensions</h3>";
$extensions = ['json', 'filter'];
foreach ($extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✅ {$ext} extension loaded<br>";
    } else {
        echo "❌ {$ext} extension missing<br>";
    }
}

// Test mail configuration
echo "<h3>5. Mail Configuration Test</h3>";
$ini_sendmail = ini_get('sendmail_path');
$ini_smtp = ini_get('SMTP');
$ini_smtp_port = ini_get('smtp_port');

echo "Sendmail Path: " . ($ini_sendmail ? $ini_sendmail : 'Not set') . "<br>";
echo "SMTP Server: " . ($ini_smtp ? $ini_smtp : 'Not set') . "<br>";
echo "SMTP Port: " . ($ini_smtp_port ? $ini_smtp_port : 'Not set') . "<br>";

// Server info
echo "<h3>6. Server Information</h3>";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";

// Test a simple mail send with detailed error reporting
echo "<h3>7. Live Email Test</h3>";
echo "📧 Testing email delivery to selsogroup@gmail.com...<br><br>";

// Enable error reporting for this test
error_reporting(E_ALL);
ini_set('display_errors', 1);

$test_to = "selsogroup@gmail.com";
$test_subject = "DODO PHP Test Email - " . date('Y-m-d H:i:s');
$test_message = "This is a test email from your DODO booking system.\n\n";
$test_message .= "If you receive this, your PHP mail is working!\n";
$test_message .= "Test time: " . date('Y-m-d H:i:s') . "\n";
$test_message .= "Server: " . $_SERVER['SERVER_NAME'] . "\n";

$test_headers = "From: noreply@dodo.com\r\n";
$test_headers .= "Reply-To: noreply@dodo.com\r\n";
$test_headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
$test_headers .= "Content-Type: text/plain; charset=UTF-8";

echo "Attempting to send test email...<br>";
$mail_result = mail($test_to, $test_subject, $test_message, $test_headers);

if ($mail_result) {
    echo "✅ mail() function returned TRUE - Email queued successfully!<br>";
    echo "📬 Check your email (including spam folder) in the next few minutes.<br>";
} else {
    echo "❌ mail() function returned FALSE - Email failed to queue!<br>";
    echo "🔧 This indicates a server configuration issue.<br>";
}

// Check for error logs
echo "<br><strong>Error Information:</strong><br>";
$error = error_get_last();
if ($error && $error['message']) {
    echo "Last error: " . $error['message'] . "<br>";
} else {
    echo "No PHP errors detected.<br>";
}

echo "<br><br><strong>Next Steps:</strong><br>";
echo "1. Run this test by visiting: test-php.php in your browser<br>";
echo "2. If mail() function works, your booking system should work<br>";
echo "3. For production, consider using SMTP instead of PHP mail()<br>";
?>