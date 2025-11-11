<?php
// process_contact.php

// Database configuration
$servername = "localhost";
$username = "root";  // Default XAMPP
$password = "";      // Default XAMPP (kosong)
$dbname = "ksixteen_cafe";

// Function to save contact to database
function save_contact_to_database($name, $email, $phone, $subject, $message) {
    global $servername, $username, $password, $dbname;
    
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, phone, subject, message, created_at) 
                               VALUES (:name, :email, :phone, :subject, :message, NOW())");
        
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':subject', $subject);
        $stmt->bindParam(':message', $message);
        
        $stmt->execute();
        return true;
    } catch(PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}

// Process form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form data
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));
    
    // Validation
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Nama lengkap harus diisi";
    }
    
    if (empty($email)) {
        $errors[] = "Email harus diisi";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid";
    }
    
    if (empty($subject)) {
        $errors[] = "Subjek harus dipilih";
    }
    
    if (empty($message)) {
        $errors[] = "Pesan harus diisi";
    }
    
    // If no errors, process the form
    if (empty($errors)) {
        // Save to database
        $db_success = save_contact_to_database($name, $email, $phone, $subject, $message);
        
        // Send email (optional)
        $to = "your-email@domain.com"; // Ganti dengan email Anda
        $email_subject = "Pesan Kontak dari K SIXTEEN CAFE: " . $subject;
        $email_body = "
        Anda menerima pesan baru dari website K SIXTEEN CAFE:
        
        Nama: $name
        Email: $email
        Telepon: " . ($phone ?: 'Tidak diisi') . "
        Subjek: $subject
        
        Pesan:
        $message
        
        ---
        Pesan ini dikirim dari form kontak website K SIXTEEN CAFE.
        ";
        
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";
        
        // Uncomment to enable email sending
        // $email_sent = mail($to, $email_subject, $email_body, $headers);
        $email_sent = true; // For demo purposes
        
        if ($db_success || $email_sent) {
            $success = true;
        } else {
            $errors[] = "Maaf, terjadi kesalahan saat mengirim pesan. Silakan coba lagi.";
        }
    }
}
?>

<!-- Rest of the HTML code remains the same -->