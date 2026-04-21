<?php
// Basit test sayfası - Veritabanı bağlantısını test et
session_start();
require_once 'config.php';

echo "<h1>Login Test Sayfası</h1>";

// Veritabanı bağlantısını test et
try {
    $conn = getDBConnection();
    echo "<p style='color: green;'>✓ Veritabanı bağlantısı başarılı!</p>";
    
    // Users tablosunu kontrol et
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    if ($result->num_rows > 0) {
        echo "<p style='color: green;'>✓ Users tablosu mevcut!</p>";
        
        // Kullanıcı sayısını göster
        $count_result = $conn->query("SELECT COUNT(*) as count FROM users");
        $count = $count_result->fetch_assoc()['count'];
        echo "<p>Toplam kullanıcı sayısı: <strong>$count</strong></p>";
        
        // Kullanıcıları listele
        $users_result = $conn->query("SELECT id, username, full_name FROM users");
        if ($users_result->num_rows > 0) {
            echo "<h3>Mevcut Kullanıcılar:</h3>";
            echo "<ul>";
            while($row = $users_result->fetch_assoc()) {
                echo "<li>ID: {$row['id']} - Kullanıcı: {$row['username']} - Ad: {$row['full_name']}</li>";
            }
            echo "</ul>";
        }
    } else {
        echo "<p style='color: red;'>✗ Users tablosu bulunamadı! database.sql dosyasını çalıştırın.</p>";
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Hata: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<h2>Test Girişi</h2>";
echo "<form method='POST' action='login.php'>";
echo "<p>Kullanıcı Adı: <input type='text' name='username' required></p>";
echo "<p>Şifre: <input type='password' name='password' required></p>";
echo "<p><button type='submit'>Giriş Yap / Kayıt Ol</button></p>";
echo "</form>";

echo "<hr>";
echo "<p><a href='login.html'>Normal Giriş Sayfası</a> | <a href='index.php'>Ana Sayfa</a></p>";
?>

