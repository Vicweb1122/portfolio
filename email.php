<?php
// Include the PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Path to PHPMailer files (adjust this based on your file structure)
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// --- Configuration Variables ---
$smtpUsername = 'rasakioladipupo6@gmail.com';
$smtpPassword = 'Dipo0813'; // IMPORTANT: Use the correct password here.
// i will input it later
$smtpHost = 'mail..com';
// Using the recommended Secure SSL/TLS Port 465
$smtpPort = 465; 
// The email address the message will appear to be from
$fromEmail = $smtpUsername;
$fromName = 'RASAKI OLADIPUPO '; 
// The recipient of the contact form (where the email will be sent - Admin)
$toEmail = $smtpUsername; 
$toName = 'Admin';

// --- Form Data Capture ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input data
    $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : 'N/A';
    $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : 'N/A';
    $subject = isset($_POST['subject']) ? htmlspecialchars(trim($_POST['subject'])) : 'No Subject';
    $message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : 'No Message';

    // Basic validation
    if (empty($name) || empty($email) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Error: Please fill in all required fields and ensure the email is valid.");
    }

    $mail = new PHPMailer(true);

    try {
        // --- Enable Debugging ---
        // DEBUGGING IS DISABLED TO PREVENT "HEADERS ALREADY SENT" ERROR
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER; 
        // $mail->Debugoutput = 'html';

        // --- Server Settings ---
        $mail->isSMTP();                                       // Send using SMTP
        $mail->Host       = $smtpHost;                         // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                              // Enable SMTP authentication
        $mail->Username   = $smtpUsername;                     // SMTP username
        $mail->Password   = $smtpPassword;                     // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;       // Use SMTPS (SSL) for Port 465
        $mail->Port       = $smtpPort;                         // TCP port to connect to

        // ===============================================
        // 1. SEND NOTIFICATION TO ADMIN (YOU) - PLAIN TEXT
        // ===============================================
        $mail->setFrom($fromEmail, $fromName); 
        $mail->addAddress($toEmail, $toName);                  // Admin is the primary recipient
        $mail->addReplyTo($email, $name);                      // Set the form submitter's email as the reply-to address

        $mail->isHTML(false); 
        $mail->Subject = "New Contact Form Submission: " . $subject;
        
        $adminBody = "A new message was submitted:\n\n";
        $adminBody .= "Name: " . $name . "\n";
        $adminBody .= "Sender Email: " . $email . "\n";
        $adminBody .= "Subject: " . $subject . "\n\n";
        $adminBody .= "Message:\n" . $message;
        
        $mail->Body    = $adminBody;

        if (!$mail->send()) {
            throw new Exception("Admin email failed to send: " . $mail->ErrorInfo);
        }

        // ===============================================
        // 2. SEND AUTO-REPLY TO USER (SENDER) - HTML DESIGN
        // ===============================================
        
        // Clear all previous recipients and addresses for the second email
        $mail->clearAllRecipients();
        $mail->clearReplyTos();
        
        // Set the sender address back to your main account
        $mail->setFrom($fromEmail, $fromName);
        
        // Set the user (sender) as the recipient for the auto-reply
        $mail->addAddress($email, $name); 

        // Set the email to use HTML
        $mail->isHTML(true); 

        $mail->Subject = "Confirmation: Your message to " . $fromName;

        // --- EMBED LOGO IMAGE ---
        // !!! IMPORTANT: ENSURE THESE FILE PATHS ARE CORRECT !!!
        $mail->addEmbeddedImage('assets/img/photo.jpg', 'logo_cid', 'photo.jpg'); 
        $mail->addEmbeddedImage('assets/img/facebook.png', 'facebook_cid', 'facebook.png'); 
        $mail->addEmbeddedImage('assets/img/twitter.png', 'twitter_cid', 'twitter.png'); 
        $mail->addEmbeddedImage('assets/img/instagram.png', 'instagram_cid', 'instagram.png'); 


        // --- HTML BODY WITH INLINE CSS ---
        $userBody = '
        <html>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; background-color: #f4f4f4; padding: 20px;">
            <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                <div style="background-color: #3fb598ff; padding: 20px; text-align: center; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                    <img src="cid:logo_cid" alt="' . $fromName . ' Logo" style="max-width: 150px; height: auto; display: block; margin: 0 auto;">
                </div>
                <div style="padding: 30px;">
                    <h1 style="color: #333333; font-size: 24px;">Message Received!</h1>
                    <p style="color: #555555;">
                        Hello <strong>' . $name . '</strong>,
                    </p>
                    <p style="color: #555555;">
                        Thank you for reaching out! We have successfully received your message and will get back to you as soon as possible.
                    </p>
                    <div style="border-left: 4px solid #de2828ff; background-color: #e8eaf6; padding: 15px; margin: 20px 0;">
                        <h3 style="margin-top: 0; color: #f7c500ff;">Your Submitted Message</h3>
                        <p style="margin: 5px 0; color: #555555;"><strong>Subject:</strong> ' . $subject . '</p>
                        <div style="white-space: pre-wrap; margin-top: 10px; padding: 10px; border: 1px solid #c5cae9; background-color: #ffffff; border-radius: 4px; color: #333333;">' 
                            . $message . 
                        '</div>
                    </div>
                    
                    <p style="color: #555555;">
                        Best Regards,<br>
                        Rasaki Oladipupo
                    </p>
                </div>

                <div style="padding: 10px 30px; text-align: center; border-top: 1px solid #eeeeee;">
                    <p style="font-size: 14px; color: #ffffffff; margin-bottom: 10px;">Connect With Us</p>
                    
                    <a href="https://t.me://ThatguyNovaX" target="_blank" style="text-decoration: none; margin: 0 5px;">
                        <img src="cid:facebook_cid" alt="Facebook" style="width: 28px; height: 28px; border: 0;">
                    </a>
                    
                    <a href="https://x.com/thatguynova_?s=21" target="_blank" style="text-decoration: none; margin: 0 5px;">
                        <img src="cid:twitter_cid" alt="Twitter" style="width: 28px; height: 28px; border: 0;">
                    </a>
                    <a href="https://www.instagram.com/thatguynovax" target="_blank" style="text-decoration: none; margin: 0 5px;">
                        <img src="cid:instagram_cid" alt="LinkedIn" style="width: 28px; height: 28px; border: 0;">
                    </a>
                </div>
                <div style="background-color: #cccccc; color: #555555; padding: 15px; text-align: center; font-size: 12px; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;">
                    This is an automated confirmation message.
                </div>
            </div>
        </body>
        </html>
        ';
        $mail->Body = $userBody;
        
        // Set a plain text fallback for email clients that don't display HTML
        $mail->AltBody = "Hello " . $name . ",\n\nThank you for reaching out! We have successfully received your message and will get back to you as soon as possible.\n\nSubject: " . $subject . "\nMessage:\n" . $message;
        
        if (!$mail->send()) {
            throw new Exception("User confirmation email failed to send: " . $mail->ErrorInfo);
        }

        // Redirect upon successful sending of both emails
        header('Location: thank.html');
        exit;    
    } catch (Exception $e) {
        // Display the error for debugging
        echo "Message could not be sent. Mailer Error: " . $e->getMessage();
    }

} else {
    // If someone tries to access email.php directly via GET
    http_response_code(405);
    echo "Method Not Allowed.";
}
?>