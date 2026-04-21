<?php
// Şifre hash'leme aracı
// Kullanım: http://localhost/Project/hash.php?password=sifreniz

if (isset($_GET['password'])) {
    $password = $_GET['password'];
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    echo "<h2>Şifre Hash'i:</h2>";
    echo "<p><strong>Şifre:</strong> " . htmlspecialchars($password) . "</p>";
    echo "<p><strong>Hash:</strong> <code>" . $hash . "</code></p>";
    echo "<hr>";
    echo "<h3>MySQL'de kullanmak için:</h3>";
    echo "<pre>INSERT INTO users (username, password, full_name) VALUES ('kullanici_adi', '" . $hash . "', 'Ad Soyad');</pre>";
} else {
    echo "<h2>Şifre Hash'leme Aracı</h2>";
    echo "<p>Kullanım: <code>?password=sifreniz</code></p>";
    echo "<p>Örnek: <a href='?password=admin123'>?password=admin123</a></p>";
}
?>

