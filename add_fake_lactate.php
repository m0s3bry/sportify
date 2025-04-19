<?php
include 'includes/db.php';

// توليد بيانات وهمية للاعبين
$players = $conn->query("SELECT id FROM users WHERE role = 'player'");

while ($player = $players->fetch_assoc()) {
    $user_id = $player['id'];
    $lactate = rand(20, 70) / 10; // قيم بين 2.0 و 7.0
    
    // إضافة قراءة جديدة
    $stmt = $conn->prepare("INSERT INTO players_data (user_id, lactate_level) VALUES (?, ?)");
    $stmt->bind_param("id", $user_id, $lactate);
    $stmt->execute();
    
    // إضافة إشعار إذا كانت القيمة مرتفعة
    if ($lactate > 4.0) {
        $message = "⚠️ تحذير: مستوى اللاكتات للاعب ID $user_id هو $lactate mmol/L";
        $conn->query("INSERT INTO notifications (user_id, message) VALUES ($user_id, '$message')");
    }
}

echo "تم إنشاء بيانات وهمية بنجاح!";
?>