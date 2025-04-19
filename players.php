<?php
session_start();
include('includes/db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'coach') {
  header("Location: login.php");
  exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>All Players | Sportify</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <h2>Players Lactate Levels</h2>
  <table border="1" cellpadding="10">
    <tr>
      <th>Player Name</th>
      <th>Email</th>
      <th>Lactate Level</th>
      <th>Recorded At</th>
      <th>Status</th>
    </tr>

    <?php
    $query = "
      SELECT u.name, u.email, pd.lactate_level, pd.recorded_at
      FROM users u
      JOIN players_data pd ON u.id = pd.user_id
      WHERE u.role = 'player'
      ORDER BY pd.recorded_at DESC
    ";

    $result = $conn->query($query);

    if ($result->num_rows > 0):
      while ($row = $result->fetch_assoc()):
        $status = ($row['lactate_level'] > 4.0) ? "<span style='color:red;'>⚠ High</span>" : "✔ Normal";
    ?>
        <tr>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td><?= $row['lactate_level'] ?> mmol/L</td>
          <td><?= $row['recorded_at'] ?></td>
          <td><?= $status ?></td>
        </tr>
    <?php
      endwhile;
    else:
    ?>
      <tr><td colspan="5">No players found</td></tr>
    <?php endif; ?>
  </table>

  <br><a href="dashboard.php">⬅ Back to Dashboard</a>
</body>
</html>
