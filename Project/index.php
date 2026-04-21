<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="tr">
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="style.css">
        <title>PRU Turizm</title>
        <link rel="icon" type="image/x-icon" href="images/icon.png">
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
        </style>
    </head>
    <body>
        <div class="page">
            <?php if (isLoggedIn()): 
                $user = getUserInfo();
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
            
            <div class="navBar">
                <ul>
                    <li><a href="tours.html">Turlar</a></li>
                    <li><a href="biletlerim.html">Biletlerim</a></li>
                    <li class="home"><a href="index.php"><img src="images/icon.png" alt="home" width="60px"></a></li>
                    <li><a href="aboutus.html">Hakkımızda</a></li>
                    <li><a href="faq.html">Destek</a></li>
                    
                </ul>
            </div>
            <div class="body">
        <div class="wrapper">
            <div class="slider-container">
                <button class="slide-btn left" onclick="prevImage()">&#10094;</button>
                <img id="sliderImage" src="images/Kapadokya.jpg" alt="tur resmi" onclick="goToTour()" style="cursor: pointer;">
                <button class="slide-btn right" onclick="nextImage()">&#10095;</button>
            </div>
<div class="searchBar">
    <form action="/submit" method="post" class="search-card" onsubmit="handleSearch(event)">
        <h3>Tur Ara</h3>

        <div class="field-row">
            <div class="field">
                <label for="from">Nereden</label>
                <input type="text" name="from" id="from" value="İstanbul" disabled style="background-color: #f5f5f5; color: #666; cursor: not-allowed;">
            </div>

            <div class="field" style="position: relative;">
                <label for="where">Nereye</label>
                <input type="text" name="where" id="where" placeholder="Şehir seçin..." autocomplete="off">
                <div id="autocomplete-list" class="autocomplete-list"></div>
            </div>
        </div>

        <button type="submit" class="searchBtn">Ara</button>
    </form>
</div>
            </div>
            </div>

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
            </div>

        <?php
        // Veritabanından turları çek ve JavaScript'e aktar
        $conn = getDBConnection();
        $tours_query = "SELECT tour_name, from_location, to_location, image_path FROM tours ORDER BY created_at DESC LIMIT 15";
        $tours_result = $conn->query($tours_query);
        
        $cityTourMap = [];
        $tourDataArray = [];
        
        if ($tours_result && $tours_result->num_rows > 0) {
            while ($tour = $tours_result->fetch_assoc()) {
                // Tour ID oluştur (tours.php'deki gibi)
                $tour_id = strtolower(str_replace(' ', '-', $tour['tour_name']));
                $tour_id = preg_replace('/[^a-z0-9-]/', '', $tour_id);
                
                // Tour data için (slider için)
                $image_path = !empty($tour['image_path']) ? $tour['image_path'] : 'images/img1.png';
                $tourDataArray[] = [
                    'image' => $image_path,
                    'link' => 'tours.php#' . $tour_id
                ];
                
                // From ve to location'ları şehir listesine ekle
                if (!empty($tour['from_location'])) {
                    $from_lower = mb_strtolower($tour['from_location'], 'UTF-8');
                    $from_simple = str_replace(['ı', 'ğ', 'ü', 'ş', 'ö', 'ç'], ['i', 'g', 'u', 's', 'o', 'c'], $from_lower);
                    $cityTourMap[$from_lower] = $tour_id;
                    $cityTourMap[$from_simple] = $tour_id;
                }
                
                if (!empty($tour['to_location'])) {
                    $to_lower = mb_strtolower($tour['to_location'], 'UTF-8');
                    $to_simple = str_replace(['ı', 'ğ', 'ü', 'ş', 'ö', 'ç'], ['i', 'g', 'u', 's', 'o', 'c'], $to_lower);
                    $cityTourMap[$to_lower] = $tour_id;
                    $cityTourMap[$to_simple] = $tour_id;
                }
                
                // Tur adından da şehir çıkarmaya çalış (örn: "Rize Turu" -> "rize")
                $tour_name_lower = mb_strtolower($tour['tour_name'], 'UTF-8');
                $tour_name_parts = explode(' ', $tour_name_lower);
                foreach ($tour_name_parts as $part) {
                    $part_clean = preg_replace('/[^a-zğüşıöç]/', '', $part);
                    if (strlen($part_clean) > 2 && $part_clean !== 'turu' && $part_clean !== 'tur') {
                        $cityTourMap[$part_clean] = $tour_id;
                    }
                }
            }
        }
        $conn->close();
        ?>
        <script>
            // PHP'den gelen dinamik verileri kullan
            window.dynamicCityTourMap = <?php echo json_encode($cityTourMap, JSON_UNESCAPED_UNICODE); ?>;
            window.dynamicTourData = <?php echo json_encode($tourDataArray, JSON_UNESCAPED_UNICODE); ?>;
        </script>
        <script src="script.js"></script>
        <script>
            // script.js yüklendikten sonra verileri güncelle
            if (window.dynamicCityTourMap && typeof cityTourMap !== 'undefined') {
                // cityTourMap'i güncelle (mevcut olanları koru, yenileri ekle)
                Object.assign(cityTourMap, window.dynamicCityTourMap);
                // cities listesini güncelle
                if (typeof cities !== 'undefined') {
                    cities.length = 0;
                    cities.push(...Object.keys(cityTourMap).sort());
                }
            }
            
            // tourData'yı güncelle (slider için)
            if (window.dynamicTourData && typeof tourData !== 'undefined') {
                tourData.length = 0;
                tourData.push(...window.dynamicTourData);
                // İlk resmi göster
                if (typeof showImage === 'function') {
                    showImage();
                }
            }
        </script>
    </body>
</html>