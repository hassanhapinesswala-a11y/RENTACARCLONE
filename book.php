?php
// book.php
require_once 'db.php';
 
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php'); exit;
}
 
// gather & basic validation
$car_id = intval($_POST['car_id'] ?? 0);
$customer_name = trim($_POST['customer_name'] ?? '');
$customer_phone = trim($_POST['customer_phone'] ?? '');
$pickup_location = trim($_POST['pickup_location'] ?? '');
$start_date = $_POST['start_date'] ?? '';
$end_date = $_POST['end_date'] ?? '';
 
if (!$car_id || !$customer_name || !$customer_phone || !$start_date || !$end_date) {
    echo "Missing booking details."; exit;
}
 
// calculate days and total price using car price
$stmt = $mysqli->prepare("SELECT price_per_day FROM cars WHERE id = ?");
$stmt->bind_param('i', $car_id);
$stmt->execute();
$res = $stmt->get_result();
$car = $res->fetch_assoc();
$stmt->close();
if (!$car) { echo "Car not found"; exit; }
 
$sd = new DateTime($start_date);
$ed = new DateTime($end_date);
$diff = $ed->diff($sd)->days + 1;
if ($diff <= 0) { echo "Invalid date range."; exit; }
$total = $diff * floatval($car['price_per_day']);
 
// insert booking (prepared)
$ins = $mysqli->prepare("INSERT INTO bookings (car_id, customer_name, customer_phone, pickup_location, start_date, end_date, total_price) VALUES (?,?,?,?,?,?,?)");
$ins->bind_param('isssssd', $car_id, $customer_name, $customer_phone, $pickup_location, $start_date, $end_date, $total);
$ok = $ins->execute();
$booking_id = $ins->insert_id;
$ins->close();
 
if (!$ok) {
    echo "Failed to save booking. Try again.";
    exit;
}
 
// Show confirmation page with JS-based friendly redirect back to homepage after a few seconds
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Booking Confirmed</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body{font-family:Inter, Arial, sans-serif;background:linear-gradient(180deg,#f8fbff,#ffffff);display:flex;align-items:center;justify-content:center;height:100vh;margin:0}
    .box{background:white;padding:26px;border-radius:12px;box-shadow:0 10px 40px rgba(9,30,66,0.08);max-width:520px;text-align:center}
    .id{font-weight:800;color:#0b6efd;font-size:20px}
    .info{color:#374151;margin-top:10px}
    button{margin-top:14px;padding:10px 14px;border-radius:10px;border:0;background:#eef3ff;cursor:pointer}
  </style>
</head>
<body>
  <div class="box">
    <div style="font-size:22px;font-weight:800">Booking Confirmed ✅</div>
    <div class="id">Booking #<?=htmlspecialchars($booking_id)?></div>
    <div class="info">
      <p>Thanks <?=htmlspecialchars($customer_name)?> — your booking for car ID <?=htmlspecialchars($car_id)?> is saved.</p>
      <p><strong>Total:</strong> $<?=number_format($total,2)?> • <?=htmlspecialchars($start_date)?> → <?=htmlspecialchars($end_date)?></p>
    </div>
    <div>
      <button onclick="window.location.href='index.php'">Back to Home</button>
    </div>
  </div>
</body>
</html>
 
