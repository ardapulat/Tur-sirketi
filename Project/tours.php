<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turlarımız</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/x-icon" href="images/icon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .user-info {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.95);
            padding: 10px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 15px;
            z-index: 1000;
        }
        .user-info span {
            color: #111827;
            font-weight: 500;
        }
        .user-info a {
            color: #4f46e5;
            text-decoration: none;
            font-size: 14px;
        }
        .user-info a:hover {
            text-decoration: underline;
        }
        .login-link {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.95);
            padding: 10px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        .login-link a {
            color: #4f46e5;
            text-decoration: none;
            font-weight: 500;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
                .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 16px 24px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 10000;
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 300px;
            animation: slideIn 0.3s ease-out;
            font-weight: 500;
        }
        .footer {
            width: 90%;
            max-width: 1200px;
            margin: 50px auto 0;
        }
        
        .toast i {
            font-size: 20px;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
        
        .toast.hide {
            animation: slideOut 0.3s ease-out forwards;
        }
    </style>
</head>
<body>
    <?php 
    $purchasedTours = [];
    if (isLoggedIn()): 
        $user = getUserInfo();
        // Kullanıcının satın aldığı turları al
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT tour_name FROM purchases WHERE user_id = ?");
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $purchasedTours[] = $row['tour_name'];
        }
        $stmt->close();
        $conn->close();
    ?>
        <div class="user-info">
            <span>Hoşgeldin, <?php echo htmlspecialchars($user['full_name']); ?>!</span>
            <a href="logout.php">Çıkış Yap</a>
            <a href="admin_login.php" style="margin-left: 15px;">Admin</a>
        </div>
    <?php else: ?>
        <div class="login-link">
            <a href="login.html">Giriş Yap</a>
            <a href="admin_login.php" style="margin-left: 15px;">Admin</a>
        </div>
    <?php endif; ?>
    <div class="page">
            <div class="navBar">
                <ul>
                    <li><a href="tours.html">Turlar</a></li>
                    <li><a href="index.php">Biletlerim</a></li>
                    <li class="home"><a href="index.php"><img src="images/icon.png" alt="home" width="60px"></a></li>
                    <li><a href="index.php">Hakkımızda</a></li>
                    <li><a href="faq.html">Destek</a></li>
                    
                </ul>
            </div>

    <div class="tours-container">

        <?php
        // MySQL'den turları çek
        $conn = getDBConnection();
        
        $tours_query = "SELECT * FROM tours ORDER BY created_at DESC";
        $tours_result = $conn->query($tours_query);
        
        $static_tours = [
        ];
        
        // MySQL'den gelen turları göster
        if ($tours_result && $tours_result->num_rows > 0) {
            while ($tour = $tours_result->fetch_assoc()) {
                $tour_id = strtolower(str_replace(' ', '-', $tour['tour_name']));
                $tour_id = preg_replace('/[^a-z0-9-]/', '', $tour_id);
                $image_path = $tour['image_path'] ? $tour['image_path'] : 'images/img1.png';
                $is_purchased = in_array($tour['tour_name'], $purchasedTours);
        ?>
            <div class="tour-card" id="<?php echo $tour_id; ?>">
                <img src="<?php echo htmlspecialchars($image_path); ?>" alt="<?php echo htmlspecialchars($tour['tour_name']); ?>" class="tour-image" style="width: 100%; height: 200px; object-fit: cover;">
                <div class="tour-content">
                    <h3 class="tour-title"><?php echo htmlspecialchars($tour['tour_name']); ?></h3>
                    <p class="tour-desc"><?php echo htmlspecialchars($tour['description']); ?></p>
                    <div class="tour-meta">
                        <?php if ($tour['date']): ?>
                            <span class="meta-item"><i class="fas fa-calendar-alt"></i> <?php echo htmlspecialchars($tour['date']); ?></span>
                        <?php endif; ?>
                        <?php if ($tour['from_location'] && $tour['to_location']): ?>
                            <span class="meta-item"><i class="fas fa-route"></i> <?php echo htmlspecialchars($tour['from_location']); ?> <i class="fas fa-arrow-right"></i> <?php echo htmlspecialchars($tour['to_location']); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="tour-footer">
                        <?php if ($tour['price']): ?>
                            <span class="tour-price"><?php echo number_format($tour['price'], 0); ?> ₺</span>
                        <?php endif; ?>
                        <button class="btn-buy" onclick="purchaseTour('<?php echo htmlspecialchars($tour['tour_name'], ENT_QUOTES); ?>', <?php echo $tour['price'] ? $tour['price'] : 0; ?>, '<?php echo htmlspecialchars($tour['date'] ?? '', ENT_QUOTES); ?>', this)" <?php echo $is_purchased ? 'disabled style="background-color: #28a745; cursor: not-allowed;"' : ''; ?>>
                            <?php echo $is_purchased ? '<i class="fas fa-check"></i> Satın Alındı' : 'Satın Al'; ?>
                        </button>
                    </div>
                </div>
            </div>
            <br>
            
        <?php
            }
        }
        ?>
            <div class="footer">
                <ul class="iletisim">
                    <li>İletişim Bilgilerimiz</li>
                    <li><img src="images/mail.png" alt="mail" width="18px">PRU@gmail.com</li>
                    <li><img src="images/ph.png" alt="phone" width="18px">Tel: (0216) 581 00 50</li>
                    <li><img src="images/ph.png" alt="phone" width="18px">Tel: (0216) 581 00 51</li>
                </ul>
                <ul class="adres">
                    <li>Adresimiz</li>
                    <li>📍Postane, Eflatun Sk. No:8, 34940 Tuzla/İstanbul</li>
                    <li class="ikon">
                        <a href="instagram.com" target="_blank"><img src="images/ins.png" alt="Instagram"></a>
                        <a href="x.com" target="_blank"><img src="images/x.webp" alt="Twitter"  ></a>
                        <a href="facebook.com" target="_blank"><img src="images/fb.png" alt="Facebook"  ></a>
                        <a href="youtube.com" target="_blank"><img src="images/youtube.png" alt="Youtube"  ></a>
                        <a href="web.whatsapp.com" target="_blank"><img src="images/wp.png" alt="Whatsapp"></a></li>
                </ul>
            </div>
        
        <?php
        // Veritabanı bağlantısını kapat
        if (isset($conn)) {
            $conn->close();
        }
        ?>


    <script>
        // Satın alınan turları JavaScript'e aktar
        const purchasedTours = <?php echo json_encode($purchasedTours); ?>;
        
        // Sayfa yüklendiğinde satın alınan turları işaretle
        window.addEventListener('DOMContentLoaded', function() {
            purchasedTours.forEach(function(tourName) {
                // Tüm butonları bul
                const buttons = document.querySelectorAll('.btn-buy');
                buttons.forEach(function(button) {
                    // Butonun onclick attribute'undan tur adını al
                    const onclickAttr = button.getAttribute('onclick');
                    if (onclickAttr && onclickAttr.includes("'" + tourName + "'")) {
                        // Bu tur satın alınmış, butonu güncelle
                        button.innerHTML = '<i class="fas fa-check"></i> Satın Alındı';
                        button.style.backgroundColor = '#28a745';
                        button.disabled = true;
                        button.style.cursor = 'not-allowed';
                    }
                });
            });
        });
        
        // Toast bildirimi göster
        function showToast(message, type = 'success') {
            // Mevcut toast'ı kaldır
            const existingToast = document.querySelector('.toast');
            if (existingToast) {
                existingToast.remove();
            }
            
            // Yeni toast oluştur
            const toast = document.createElement('div');
            toast.className = 'toast';
            
            const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            const bgColor = type === 'success' ? '#28a745' : '#dc3545';
            toast.style.background = bgColor;
            
            toast.innerHTML = `
                <i class="fas ${icon}"></i>
                <span>${message}</span>
            `;
            
            document.body.appendChild(toast);
            
            // 3 saniye sonra kaldır
            setTimeout(() => {
                toast.classList.add('hide');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 3000);
        }
        
        // Tur satın alma fonksiyonu
        function purchaseTour(tourName, price, tourDate, button) {
            // Butonu devre dışı bırak
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> İşleniyor...';
            
            // AJAX ile satın alma işlemi
            const formData = new FormData();
            formData.append('tour_name', tourName);
            formData.append('tour_price', price);
            formData.append('tour_date', tourDate || '');
            
            fetch('purchase.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Başarılı - Tik işareti göster
                    button.innerHTML = '<i class="fas fa-check"></i> Satın Alındı';
                    button.style.backgroundColor = '#28a745';
                    button.style.cursor = 'not-allowed';
                    
                    // Güzel bildirim göster
                    showToast('Başarıyla satın alındı!', 'success');
                } else {
                    // Hata
                    button.disabled = false;
                    button.innerHTML = 'Satın Al';
                    showToast(data.message || 'Bir hata oluştu!', 'error');
                }
            })
            .catch(error => {
                button.disabled = false;
                button.innerHTML = 'Satın Al';
                showToast('Bir hata oluştu!', 'error');
            });
        }
        
        // URL'de hash varsa ilgili tura scroll yap
        if (window.location.hash) {
            window.addEventListener('DOMContentLoaded', function() {
                const hash = window.location.hash.substring(1);
                const targetElement = document.getElementById(hash);
                if (targetElement) {
                    setTimeout(function() {
                        targetElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        // Belirgin vurgulama efekti
                        targetElement.classList.add('highlighted');
                        
                        // 4 saniye sonra efekti kaldır
                        setTimeout(function() {
                            targetElement.classList.remove('highlighted');
                            // Smooth geçiş için
                            targetElement.style.transition = 'all 0.5s ease';
                        }, 4000);
                    }, 100);
                }
            });
        }
    </script>

</body>
</html>