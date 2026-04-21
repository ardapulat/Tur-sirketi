<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    // Validasyon
    if (empty($full_name) || empty($username) || empty($password) || empty($password_confirm)) {
        header('Location: register.html?error=required');
        exit;
    }
    
    if ($password !== $password_confirm) {
        header('Location: register.html?error=password_mismatch');
        exit;
    }
    
    try {
        $conn = getDBConnection();
        
        // Kullanıcı adı kontrolü
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        if (!$stmt) {
            throw new Exception("Veritabanı hatası: " . $conn->error);
        }
        
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Kullanıcı adı zaten var
            $stmt->close();
            $conn->close();
            header('Location: register.html?error=username_exists');
            exit;
        }
        
        $stmt->close();
        
        // Şifreyi hash'le
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Yeni kullanıcı ekle
        $stmt = $conn->prepare("INSERT INTO users (username, password, full_name) VALUES (?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Kayıt hatası: " . $conn->error);
        }
        
        $stmt->bind_param("sss", $username, $hashed_password, $full_name);
        
        if ($stmt->execute()) {
            // Kayıt başarılı
            $stmt->close();
            $conn->close();
            
            // Giriş sayfasına yönlendir
            header('Location: login.html?success=registered');
            exit;
        } else {
            // Kayıt hatası
            $error_msg = $stmt->error;
            $stmt->close();
            $conn->close();
            header('Location: register.html?error=db_error');
            exit;
        }
    } catch (Exception $e) {
        // Hata durumunda
        header('Location: register.html?error=system_error');
        exit;
    }
} else {
    header('Location: register.html');
    exit;
}
?>

