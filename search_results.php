<?php
// search_results.php
require_once 'db.php';
 
// sanitize & gather GET params
$pickup = isset($_GET['pickup']) ? $mysqli->real_escape_string($_GET['pickup']) : '';
$start = isset($_GET['start']) ? $_GET['start'] : '';
$end = isset($_GET['end']) ? $_GET['end'] : '';
$type = isset($_GET['type']) && $_GET['type'] !== '' ? $_GET['type'] : null;
$fuel = isset($_GET['fuel']) && $_GET['fuel'] !== '' ? $_GET['fuel'] : null;
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
 
$query = "SELECT id, title, brand, type, seats, fuel_type, price_per_day, image_url, rating, available_from, available_to FROM cars WHERE 1=1";
$params = [];
$types = "";
 
// availability filter if dates provided
if ($start && $end) {
    $query .= " AND available_from <= ? AND available_to >= ?";
    $params[] = $start; $params[] = $end;
    $types .= "ss";
}
if ($type) { $query .= " AND type = ?"; $params[] = $type; $types .= "s"; }
if ($fuel) { $query .= " AND fuel_type = ?"; $params[] = $fuel; $types .= "s"; }
 
// sorting
if ($sort === 'price_asc') $query .= " ORDER BY price_per_day ASC";
elseif ($sort === 'price_desc') $query .= " ORDER BY price_per_day DESC";
else $query .= " ORDER BY rating DESC, price_per_day ASC";
 
$stmt = $mysqli->prepare($query);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$res = $stmt->get_result();
$cars = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Search Results — RentACar Clone</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    /* INTERNAL CSS: clean listing */
    body{font-family:Inter, Arial, sans-serif;margin:0;background:#f8fafc;color:#0f172a}
    header{padding:18px 22px;background:white;display:flex;justify-content:space-between;align-items:center;box-shadow:0 4px 18px rgba(11,35,82,0.04)}
    .container{max-width:1100px;margin:20px auto;padding:0 14px}
    .listing{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:16px}
    .car{background:white;border-radius:12px;padding:12px;box-shadow:0 8px 30px rgba(9,30,66,0.04);display:flex;gap:12px;align-items:center}
    .car img{width:180px;height:110px;object-fit:cover;border-radius:8px}
    .meta h4{margin:0 0 6px}
    .meta p{margin:4px 0;color:#6b7280}
    .book-btn{padding:8px 12px;border-radius:8px;border:0;background:linear-gradient(90deg,#0b6efd,#0b9bff);color:#fff;cursor:pointer}
    .info-top{display:flex;justify-content:space-between;align-items:center}
  </style>
</head>
<body>
<header>
  <div style="font-weight:800;color:#0b6efd">RentACar Clone</div>
  <div><button onclick="window.location.href='index.php'" style="padding:8px 10px;border-radius:8px;border:0;background:#eef3ff;cursor:pointer">New search</button></div>
</header>
 
<div class="container">
  <h2>Available cars</h2>
  <div style="color:#374151;margin-bottom:12px">Pickup: <?= htmlspecialchars($pickup) ?> • <?= htmlspecialchars($start) ?> → <?= htmlspecialchars($end) ?></div>
 
  <div class="listing">
    <?php if (!$cars): ?>
      <div style="grid-column:1/-1;padding:30px;background:#fff;border-radius:10px;text-align:center">No cars found for your search. Try different dates or filters.</div>
    <?php endif; ?>
 
    <?php foreach($cars as $c): ?>
      <div class="car">
        <img src="<?=htmlspecialchars($c['image_url'])?>" alt="">
        <div style="flex:1">
          <div class="info-top">
            <div>
              <h4><?=htmlspecialchars($c['title'])?></h4>
              <div style="color:#6b7280;font-size:13px"><?=htmlspecialchars($c['brand'])?> · <?=htmlspecialchars($c['type'])?> · ⭐ <?=htmlspecialchars($c['rating'])?></div>
              <p style="margin-top:8px"><?=htmlspecialchars($c['seats'])?> seats · <?=htmlspecialchars($c['fuel_type'])?></p>
            </div>
            <div style="text-align:right">
              <div style="font-weight:800;color:#0b6efd">$<?=number_format($c['price_per_day'],2)?>/day</div>
              <div style="margin-top:10px">
                <button class="book-btn" onclick="goCar(<?= $c['id'] ?>)">View & Book</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
 
<script>
  function goCar(id){
    // pass through query params (so user sees same dates)
    const qs = location.search || '';
    window.location.href = 'car.php?id=' + id + qs;
  }
</script>
</body>
</html>
 
