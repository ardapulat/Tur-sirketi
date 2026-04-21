<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        echo '<script>alert("Lütfen kullanıcı adı ve şifre girin!"); window.location.href = "login.html";</script>';
        exit;
    }
    
    try {
        $conn = getDBConnection();
        
        // Kullanıcıyı bul
        $stmt = $conn->prepare("SELECT id, username, password, full_name FROM users WHERE username = ?");
        if (!$stmt) {
            throw new Exception("Veritabanı hatası: " . $conn->error);
        }
        
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            // Kullanıcı var, şifre kontrolü yap
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                // Şifre doğru, giriş yap
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                
                $stmt->close();
                $conn->close();
                
                echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Giriş Başarılı</title></head><body>';
                echo '<script>window.location.href = "index.php";</script>';
                echo '<p>Giriş başarılı! Yönlendiriliyorsunuz... <a href="index.php">Buraya tıklayın</a></p>';
                echo '</body></html>';
                exit;
            } else {
                // Şifre yanlış
                $stmt->close();
                $conn->close();
                echo '<script>alert("Şifre hatalı!"); window.location.href = "login.html";</script>';
                exit;
            }
        } else {
            // Kullanıcı yok
            $stmt->close();
            $conn->close();
            echo '<script>alert("Kullanıcı adı veya şifre hatalı!"); window.location.href = "login.html?error=invalid";</script>';
            exit;
        }
    } catch (Exception $e) {
        // Hata durumunda detaylı bilgi göster
        echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Hata</title></head><body>';
        echo '<h2>Hata Oluştu:</h2>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<p><a href="login.html">Geri Dön</a></p>';
        echo '</body></html>';
        exit;
    }
} else {
    header('Location: login.html');
    exit;
}

