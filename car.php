<?php
// car.php
require_once 'db.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) { header('Location: index.php'); exit; }
 
$stmt = $mysqli->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$car = $res->fetch_assoc();
$stmt->close();
 
if (!$car) { echo "Car not found"; exit; }
 
// preserve dates from query (if any)
$start = isset($_GET['start']) ? $_GET['start'] : '';
$end = isset($_GET['end']) ? $_GET['end'] : '';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?=htmlspecialchars($car['title'])?> — RentACar Clone</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body{font-family:Inter, Arial, sans-serif;margin:0;background:#fbfdff;color:#0f172a}
    header{padding:14px 18px;background:white;box-shadow:0 6px 20px rgba(11,35,82,0.04);display:flex;justify-content:space-between}
    .wrap{max-width:1000px;margin:18px auto;padding:14px}
    .detail{display:grid;grid-template-columns:1fr 380px;gap:18px}
    .photos img{width:100%;height:360px;object-fit:cover;border-radius:12px}
    .booking{background:white;border-radius:12px;padding:14px;box-shadow:0 8px 30px rgba(9,30,66,0.05)}
    label{display:block;margin-top:10px;font-size:13px;color:#374151}
    input,select{width:100%;padding:10px;border-radius:8px;border:1px solid #e6e9ef;margin-top:6px}
    .total{font-weight:800;font-size:18px;color:#0b6efd;margin-top:10px}
    button.book-now{margin-top:12px;padding:12px;border-radius:10px;border:0;background:linear-gradient(90deg,#0b6efd,#0b9bff);color:white;cursor:pointer;width:100%}
    @media(max-width:900px){ .detail{grid-template-columns:1fr} .photos img{height:220px} }
  </style>
</head>
<body>
<header>
  <div style="font-weight:800;color:#0b6efd">RentACar Clone</div>
  <div><button onclick="window.location.href='index.php'" style="padding:8px 10px;border-radius:8px;border:0;background:#eef3ff;cursor:pointer">Home</button></div>
</header>
 
<div class="wrap">
  <div class="detail">
    <div>
      <div class="photos"><img src="<?=htmlspecialchars($car['image_url'])?>" alt=""></div>
      <h2 style="margin-top:12px"><?=htmlspecialchars($car['title'])?></h2>
      <div style="color:#6b7280"><?=htmlspecialchars($car['brand'])?> · <?=htmlspecialchars($car['type'])?> · ⭐ <?=htmlspecialchars($car['rating'])?></div>
      <p style="margin-top:12px;line-height:1.5;color:#374151"><?=nl2br(htmlspecialchars($car['description']))?></p>
      <ul style="color:#374151;margin-top:10px">
        <li>Seats: <?=htmlspecialchars($car['seats'])?></li>
        <li>Fuel: <?=htmlspecialchars($car['fuel_type'])?></li>
        <li>Available: <?=htmlspecialchars($car['available_from'])?> → <?=htmlspecialchars($car['available_to'])?></li>
      </ul>
    </div>
 
    <div>
      <div class="booking">
        <div style="font-weight:800;font-size:18px">$<?=number_format($car['price_per_day'],2)?> <span style="font-weight:600;color:#6b7280;font-size:13px">/ day</span></div>
 
        <form id="bookingForm" method="post" action="book.php" onsubmit="return prepareBooking(event)">
          <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
          <label>Pickup location</label>
          <input name="pickup_location" required placeholder="City or Airport" value="<?= htmlspecialchars($pickup ?? '') ?>">
 
          <label>Start date</label>
          <input id="start" name="start_date" type="date" required value="<?=htmlspecialchars($start)?>">
 
          <label>End date</label>
          <input id="end" name="end_date" type="date" required value="<?=htmlspecialchars($end)?>">
 
          <label>Your name</label>
          <input name="customer_name" required placeholder="Full name">
 
          <label>Phone</label>
          <input name="customer_phone" required placeholder="Phone number">
 
          <div class="total" id="totalPrice">Total: $0.00</div>
          <button class="book-now" type="submit">Confirm Booking</button>
        </form>
      </div>
    </div>
  </div>
</div>
 
<script>
  const pricePerDay = <?= json_encode((float)$car['price_per_day']) ?>;
  const startInput = document.getElementById('start');
  const endInput = document.getElementById('end');
  const totalEl = document.getElementById('totalPrice');
 
  function calcTotal(){
    const s = startInput.value;
    const e = endInput.value;
    if (!s || !e) { totalEl.textContent = 'Total: $0.00'; return; }
    const sd = new Date(s);
    const ed = new Date(e);
    const diff = (ed - sd) / (1000*60*60*24) + 1; // inclusive days
    if (diff <= 0){ totalEl.textContent = 'Select valid dates'; return; }
    const total = (diff * pricePerDay).toFixed(2);
    totalEl.textContent = `Total: $${total} (${diff} day${diff>1?'s':''})`;
  }
 
  startInput.addEventListener('change', calcTotal);
  endInput.addEventListener('change', calcTotal);
  window.addEventListener('load', calcTotal);
 
  function prepareBooking(e){
    // simple client-side validation & redirect after POST response
    return true; // allow form to submit to book.php which will handle and redirect via JS
  }
</script>
</body>
</html>
 
