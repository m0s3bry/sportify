

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$name = $_SESSION['user_name'];
$role = $_SESSION['user_role'] ?? 'guest'; // التأكد من وجود هذا السطر
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | Sportify</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* التنسيقات الجديدة */
    .player-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 20px;
      margin: 30px 0;
    }

    .player-card {
      background: #fff;
      border-radius: 15px;
      padding: 20px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      border-left: 5px solid #4CAF50;
      transition: transform 0.3s ease;
    }

    .player-card.high {
      border-left-color: #f44336;
      background: linear-gradient(135deg, #fff5f5 0%, #ffe9e9 100%);
    }

    .card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
    }

    .player-name {
      font-size: 1.2em;
      font-weight: 600;
      color: #2c3e50;
    }

    .lactate-box {
      background: #f8f9fa;
      padding: 15px;
      border-radius: 10px;
      text-align: center;
    }

    .lactate-value {
      font-size: 2.2em;
      font-weight: 700;
      color: #2c3e50;
      margin: 10px 0;
    }

    .status-tag {
      display: inline-block;
      padding: 6px 15px;
      border-radius: 20px;
      font-size: 0.9em;
      font-weight: 500;
    }

    .normal { background: #e8f5e9; color: #2e7d32; }
    .high-status { background: #ffebee; color: #c62828; }

    .notification-panel {
      background: #fff;
      border-radius: 15px;
      padding: 20px;
      margin-top: 30px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .last-updated {
      font-size: 0.9em;
      color: #666;
      margin-top: 10px;
    }
  </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="header-section">
            <h2><i class="fas fa-running"></i> Welcome, <?= htmlspecialchars($name) ?></h2>
            <p class="role-tag">Role: <?= ucfirst(htmlspecialchars($role)) ?></p>
        </div>

        <?php if ($role == 'coach'): ?>
            <!-- Coach View -->
            <div class="monitoring-container">
                <h3><i class="fas fa-heartbeat"></i> مراقبة اللاعبين</h3>
                <button class="bluetooth-btn" onclick="connectBluetooth()">
                    <i class="fas fa-bluetooth-b"></i> الاتصال بالجهاز
                </button>
                <div class="players-list">
                    <?php
                    $players = $conn->query("SELECT u.id, u.name, pd.lactate_level, pd.recorded_at 
                                            FROM users u 
                                            JOIN players_data pd ON u.id = pd.user_id 
                                            WHERE u.role = 'player' 
                                            ORDER BY pd.recorded_at DESC");
                    while ($player = $players->fetch_assoc()):
                        $isHigh = $player['lactate_level'] > 4.0;
                    ?>
                        <div class="player-item <?= $isHigh ? 'high' : '' ?>">
                            <div class="player-info">
                                <input type="checkbox" <?= $isHigh ? 'checked' : '' ?>>
                                <span class="player-name"><?= $player['name'] ?></span>
                            </div>
                            <div class="lactate-level">
                                <span class="value"><?= $player['lactate_level'] ?> mmol/L</span>
                                <?= $isHigh ? '<i class="fas fa-exclamation-triangle"></i>' : '' ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- قسم الإشعارات -->
            <div class="notification-panel">
                <h4><i class="fas fa-bell"></i> Recent Alerts</h4>
                <ul>
                    <?php
                    $notif_result = $conn->query("SELECT message, created_at FROM notifications ORDER BY created_at DESC LIMIT 5");
                    if ($notif_result->num_rows > 0):
                        while ($notif = $notif_result->fetch_assoc()):
                    ?>
                        <li class="notification-item">
                            <i class="fas fa-exclamation-triangle"></i>
                            <?= htmlspecialchars($notif['message']) ?>
                            <span class="notification-time"><?= date('M d, H:i', strtotime($notif['created_at'])) ?></span>
                        </li>
                    <?php endwhile; else: ?>
                        <li>No new notifications</li>
                    <?php endif; ?>
                </ul>
            </div>


    <?php elseif ($role == 'player'): ?>
      <!-- Player View -->
      <div class="player-view">
        <h3><i class="fas fa-chart-line"></i> Your Lactate Status</h3>
        <?php
        $uid = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT lactate_level, recorded_at 
                              FROM players_data 
                              WHERE user_id = ? 
                              ORDER BY recorded_at DESC 
                              LIMIT 1");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $stmt->bind_result($lactate, $recorded_at);
        $found = $stmt->fetch();
        $stmt->close();
        ?>

        <?php if ($found): ?>
          <div class="player-card <?= ($lactate > 4.0) ? 'high' : '' ?>">
            <div class="lactate-box">
              <div class="lactate-value">
                <?= $lactate ?> mmol/L
              </div>
              <div class="status-tag <?= ($lactate > 4.0) ? 'high-status' : 'normal' ?>">
                <?= ($lactate > 4.0) ? 'Attention Needed' : 'Normal Range' ?>
              </div>
              <div class="last-updated">
                Last reading: <?= date('M d, H:i', strtotime($recorded_at)) ?>
              </div>
            </div>
          </div>
        <?php else: ?>
          <p>No readings available</p>
        <?php endif; ?>

        <button class="connect-btn" onclick="simulateConnection()">
          <i class="fas fa-bluetooth-b"></i> Connect Device
        </button>
        <p id="device-status" class="status-message"></p>
      </div>

    <?php elseif ($role == 'admin'): ?>
      <!-- Admin View -->
      <div class="admin-panel">
        <h3><i class="fas fa-cogs"></i> Administration Panel</h3>
        <div class="admin-actions">
          <button class="action-btn">
            <i class="fas fa-users-cog"></i> Manage Users
          </button>
          <button class="action-btn">
            <i class="fas fa-database"></i> System Analytics
          </button>
        </div>
      </div>
    <?php endif; ?>

    <div class="footer-section">
      <a href="logout.php" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i> Logout
      </a>
    </div>
  </div>

  <script>
    function simulateConnection() {
      const statusEl = document.getElementById('device-status');
      statusEl.innerHTML = `<i class="fas fa-sync fa-spin"></i> Connecting...`;
      
      setTimeout(() => {
        statusEl.innerHTML = `<i class="fas fa-check-circle"></i> Connected! Syncing data...`;
        setTimeout(() => {
          window.location.reload();
        }, 1500);
      }, 2000);
    }
  </script>
</body>
</html>