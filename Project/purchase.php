<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Giriş yapmanız gerekiyor!']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tour_name = trim($_POST['tour_name'] ?? '');
    $tour_price = floatval($_POST['tour_price'] ?? 0);
    $tour_date = trim($_POST['tour_date'] ?? '');
    
    if (empty($tour_name)) {
        echo json_encode(['success' => false, 'message' => 'Tur bilgisi eksik!']);
        exit;
    }
    
    try {
        $conn = getDBConnection();
        $user = getUserInfo();
        
        // Eğer tur tarihi varsa, aynı tarihte başka bir tur satın alınıp alınmadığını kontrol et
        if (!empty($tour_date)) {
            // Önce bu turun tarihini tours tablosundan al
            $tour_stmt = $conn->prepare("SELECT date FROM tours WHERE tour_name = ?");
            if ($tour_stmt) {
                $tour_stmt->bind_param("s", $tour_name);
                $tour_stmt->execute();
                $tour_result = $tour_stmt->get_result();
                if ($tour_result->num_rows > 0) {
                    $tour_data = $tour_result->fetch_assoc();
                    $tour_date = $tour_data['date'] ? $tour_data['date'] : $tour_date;
                }
                $tour_stmt->close();
            }
            
            // Kullanıcının aynı tarihte başka bir tur satın alıp almadığını kontrol et
            $check_stmt = $conn->prepare("SELECT p.tour_name, t.date 
                                         FROM purchases p 
                                         LEFT JOIN tours t ON p.tour_name = t.tour_name 
                                         WHERE p.user_id = ? AND t.date = ? AND p.tour_name != ?");
            if ($check_stmt) {
                $check_stmt->bind_param("iss", $user['id'], $tour_date, $tour_name);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();
                
                if ($check_result->num_rows > 0) {
                    $existing_tour = $check_result->fetch_assoc();
                    $check_stmt->close();
                    $conn->close();
                    echo json_encode(['success' => false, 'message' => 'Bu tarihte zaten "' . htmlspecialchars($existing_tour['tour_name']) . '" turunu satın aldınız! Aynı tarihte birden fazla tur satın alamazsınız.']);
                    exit;
                }
                $check_stmt->close();
            }
        }
        
        // Satın alma kaydı ekle
        $stmt = $conn->prepare("INSERT INTO purchases (user_id, tour_name, tour_price) VALUES (?, ?, ?)");
        $stmt->bind_param("isd", $user['id'], $tour_name, $tour_price);
        
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            echo json_encode(['success' => true, 'message' => 'Başarıyla satın alındı!']);
        } else {
            $stmt->close();
            $conn->close();
            echo json_encode(['success' => false, 'message' => 'Kayıt sırasında bir hata oluştu!']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek!']);
}
?>

