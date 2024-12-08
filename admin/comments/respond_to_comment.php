require '../db_connection.php';
require '../vendor/autoload.php'; // Load PHPMailer (if using Composer)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$pdo = db_connect();
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("location: ../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $commentId = $_POST['comment_id'];
    $response = $_POST['response'];
    $adminId = $_SESSION['admin_id'];

    // Insert the admin response into the database
    $stmt = $pdo->prepare("INSERT INTO responses (comment_id, admin_id, response) VALUES (:comment_id, :admin_id, :response)");
    $stmt->execute([
        ':comment_id' => $commentId,
        ':admin_id' => $adminId,
        ':response' => $response
    ]);

    // Update the comment status to "responded"
    $stmt = $pdo->prepare("UPDATE comments SET status = 'responded' WHERE comment_id = :comment_id");
    $stmt->execute([':comment_id' => $commentId]);

    // Fetch the user email from the comment's user_id
    $stmt = $pdo->prepare("SELECT u.email, c.comment FROM comments c JOIN users u ON c.user_id = u.user_id WHERE c.comment_id = :comment_id");
    $stmt->execute([':comment_id' => $commentId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $userEmail = $user['email'];
        $userComment = $user['comment'];

        // Prepare email notification using PHPMailer
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.example.com'; // Set your SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'your_email@example.com'; // SMTP username
            $mail->Password = 'your_password'; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            //Recipients
            $mail->setFrom('your_email@example.com', 'Room Booking System');
            $mail->addAddress($userEmail); // Add user's email

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Response to Your Comment';
            $mail->Body    = "Hello,<br><br>There is a new response to your comment:<br><br><i>$userComment</i><br><br><b>Admin Response:</b><br><br><i>$response</i><br><br>Best regards,<br>Room Booking System";

            // Send email
            $mail->send();
            echo json_encode(['success' => true, 'message' => 'Response submitted and notification sent.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo]);
        }
    }
}
