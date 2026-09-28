<?php

// Composer autoload
require_once __DIR__ . '/vendor/autoload.php';
require_once 'connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Sanitize input
function sanitize($data)
{
  return htmlspecialchars(strip_tags(trim($data)));
}

// Get POST data
$name    = sanitize($_POST['name'] ?? '');
$email   = sanitize($_POST['email'] ?? '');
$subject = sanitize($_POST['subject'] ?? '');
$message = sanitize($_POST['message'] ?? '');

// Validation
if (empty($name) || empty($email) || empty($subject) || empty($message)) {
  exit('All fields are required.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  exit('Invalid email address.');
}

// Save to database
try {
  $stmt = $pdo->prepare("
        INSERT INTO contact_messages (name, email, subject, message)
        VALUES (:name, :email, :subject, :message)
    ");

  $stmt->execute([
    ':name'    => $name,
    ':email'   => $email,
    ':subject' => $subject,
    ':message' => $message
  ]);
} catch (PDOException $e) {
  error_log($e->getMessage());
  exit('Database error.');
}

// Send Email
$mail = new PHPMailer(true);

try {
  // SMTP config
  $mail->isSMTP();
  $mail->Host       = 'smtp.gmail.com';
  $mail->SMTPAuth   = true;
  $mail->Username   = 'vers.cscpas@gmail.com';
  $mail->Password   = 'elrm feaj vilb idfs'; // Gmail App Password
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
  $mail->Port       = 587;

  // Email headers
  $mail->setFrom(
    'vers.cscpas@gmail.com',
    $name . ' via Contact Form'
  );

  $mail->addAddress('vers.cscpas@gmail.com');
  $mail->addReplyTo($email, $name);

  // ✅ Embed logo image (works without a website)
  $mail->addEmbeddedImage(__DIR__ . '/CSL&CO.png', 'company_logo');

  $mail->isHTML(true);
  $mail->Subject = "New Contact Message from $name";

  // HTML Email Body
  $mail->Body = '
<!doctype html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>Contact Form Message</title>
  </head>

  <body
    style="
      margin: 0;
      padding: 0;
      background-color: #f4f6f8;
      font-family: Arial, Helvetica, sans-serif;
    "
  >
    <table
      width="100%"
      cellpadding="0"
      cellspacing="0"
      style="padding: 20px; background: #f4f6f8"
    >
      <tr>
        <td align="center">
          <table
            width="600"
            cellpadding="0"
            cellspacing="0"
            style="background: #ffffff; border-radius: 6px; overflow: hidden"
          >
            <!-- HEADER -->
            <tr>
              <td
                style="background: #0f172a; padding: 20px; text-align: center"
              >
                <!-- ✅ Use CID for embedded image -->
                <img
                  src="cid:company_logo"
                  alt="Company Logo"
                  style="max-height: 60px"
                />
                <h2 style="color: #ffffff; margin: 10px 0 0">
                  CASAS SAN LUIS & CO.
                </h2>
              </td>
            </tr>

            <!-- CONTENT -->
            <tr>
              <td style="padding: 25px">
                <h3 style="margin-top: 0; color: #111827">
                  New Contact Form Message
                </h3>

                <table
                  width="100%"
                  cellpadding="10"
                  cellspacing="0"
                  style="border-collapse: collapse; font-size: 14px"
                >
                  <tr>
                    <td
                      style="
                        background: #f9fafb;
                        border: 1px solid #e5e7eb;
                        font-weight: bold;
                        width: 120px;
                      "
                    >
                      Name
                    </td>
                    <td style="border: 1px solid #e5e7eb">' . $name . '</td>
                  </tr>

                  <tr>
                    <td
                      style="
                        background: #f9fafb;
                        border: 1px solid #e5e7eb;
                        font-weight: bold;
                      "
                    >
                      Email
                    </td>
                    <td style="border: 1px solid #e5e7eb">' . $email . '</td>
                  </tr>

                  <tr>
                    <td
                      style="
                        background: #f9fafb;
                        border: 1px solid #e5e7eb;
                        font-weight: bold;
                      "
                    >
                      Subject
                    </td>
                    <td style="border: 1px solid #e5e7eb">' . $subject . '</td>
                  </tr>

                  <tr>
                    <td
                      style="
                        background: #f9fafb;
                        border: 1px solid #e5e7eb;
                        font-weight: bold;
                        vertical-align: top;
                      "
                    >
                      Message
                    </td>
                    <td style="border: 1px solid #e5e7eb; width: 100%;">' . $message . '</td>
                  </tr>
                </table>
              </td>
            </tr>

            <!-- FOOTER -->
            <tr>
              <td
                style="
                  background: #f9fafb;
                  padding: 15px;
                  text-align: center;
                  font-size: 12px;
                  color: #6b7280;
                "
              >
                This message was sent from your website contact form.<br />
                © ' . date('Y') . ' Casas San Luis & Co
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>
';



  $mail->send();
  echo 'Message sent successfully!';
} catch (Exception $e) {
  echo 'Message saved but email failed: ' . $mail->ErrorInfo;
}
