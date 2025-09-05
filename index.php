?php
// index.php
require_once 'db.php';
$today = date('Y-m-d');
// fetch some featured cars
$stmt = $mysqli->prepare("SELECT id, title, brand, price_per_day, image_url, rating FROM cars WHERE available_from <= ? AND available_to >= ? LIMIT 4");
$stmt->bind_param('ss', $today, $today);
$stmt->execute();
$res = $stmt->get_result();
$featured = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>RentACar Clone — Home</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    /* INTERNAL CSS: sleek, modern, card-based */
    :root{--accent:#0b6efd;--muted:#6b7280;--card:#ffffff;}
    *{box-sizing:border-box;font-family:Inter, system-ui, Arial, sans-serif}
    body{margin:0;background:linear-gradient(180deg,#f3f6ff 0%, #ffffff 100%);color:#0f172a}
    header{padding:28px 32px;display:flex;align-items:center;justify-content:space-between}
    .logo{font-weight:800;color:var(--accent);font-size:20px}
    .search-wrap{max-width:1100px;margin:18px auto;padding:22px;background:linear-gradient(90deg,rgba(255,255,255,.9),rgba(250,250,255,.9));border-radius:14px;box-shadow:0 6px 24px rgba(12,34,99,0.08)}
    .search-grid{display:grid;grid-template-columns:1.6fr .9fr .9fr .7fr;gap:12px}
    input, select{padding:14px;border-radius:10px;border:1px solid #e6e9ef;font-size:15px}
    button.search-btn{background:var(--accent);color:#fff;padding:14px;border-radius:10px;border:0;font-weight:700;cursor:pointer}
    .filters{display:flex;gap:10px;justify-content:center;margin:20px 0}
    .cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:18px;padding:26px}
    .card{background:var(--card);border-radius:12px;padding:12px;box-shadow:0 8px 30px rgba(9,30,66,0.06);overflow:hidden}
    .card img{width:100%;height:160px;object-fit:cover;border-radius:8px}
    .card .meta{display:flex;justify-content:space-between;align-items:center;margin-top:10px}
    .price{font-weight:800;color:var(--accent)}
    footer{padding:20px;text-align:center;color:var(--muted)}
    @media(max-width:800px){.search-grid{grid-template-columns:1fr;}}
  </style>
</head>
<body>
<header>
  <div class="logo">RentACar Clone</div>
  <div style="color:var(--muted)">(Demo)</div>
</header>
 
<div class="search-wrap">
  <form id="searchForm" onsubmit="return doSearch(event)">
    <div style="font-weight:700;margin-bottom:8px">Find your ride</div>
    <div class="search-grid">
      <input id="pickup" name="pickup" placeholder="Pickup location (city or airport)" required>
      <input id="start_date" name="start_date" type="date" required>
      <input id="end_date" name="end_date" type="date" required>
      <button class="search-btn" type="submit">Search</button>
    </div>
 
    <div class="filters" style="margin-top:14px">
      <select id="type" name="type"><option value="">Any type</option><option>Hatchback</option><option>Sedan</option><option>SUV</option></select>
      <select id="fuel" name="fuel"><option value="">Any fuel</option><option>Petrol</option><option>Diesel</option><option>Hybrid</option></select>
      <select id="sort" name="sort"><option value="">Sort</option><option value="price_asc">Price: Low → High</option><option value="price_desc">Price: High → Low</option></select>
    </div>
  </form>
</div>
 
<section style="max-width:1100px;margin:18px auto">
  <h3 style="margin:8px 0 12px">Featured cars</h3>
  <div class="cards">
    <?php foreach($featured as $car): ?>
      <div class="card">
        <img src="<?=htmlspecialchars($car['image_url'])?>" alt="<?=htmlspecialchars($car['title'])?>">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:10px">
          <div>
            <div style="font-weight:700"><?=htmlspecialchars($car['title'])?></div>
            <div style="color:var(--muted);font-size:13px"><?=htmlspecialchars($car['brand'])?> · ⭐ <?=htmlspecialchars($car['rating'])?></div>
          </div>
          <div style="text-align:right">
            <div class="price">$<?=number_format($car['price_per_day'],2)?>/day</div>
            <div style="margin-top:8px">
              <button onclick="goToCar(<?= $car['id'] ?>)" style="padding:8px 10px;border-radius:8px;border:0;background:#eef3ff;cursor:pointer">View & Book</button>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>
 
<footer>Made with ❤️ — RentACar Clone demo</footer>
 
<script>
  // JS used only for redirection (no PHP redirect)
  function doSearch(e){
    e.preventDefault();
    const pickup = encodeURIComponent(document.getElementById('pickup').value.trim());
    const start = document.getElementById('start_date').value;
    const end = document.getElementById('end_date').value;
    const type = encodeURIComponent(document.getElementById('type').value);
    const fuel = encodeURIComponent(document.getElementById('fuel').value);
    const sort = encodeURIComponent(document.getElementById('sort').value);
    // build query string
    const q = `?pickup=${pickup}&start=${start}&end=${end}&type=${type}&fuel=${fuel}&sort=${sort}`;
    window.location.href = 'search_results.php' + q;
    return false;
  }
  function goToCar(id){
    // redirect to car detail page
    window.location.href = 'car.php?id=' + id;
  }
</script>
</body>
</html>
 
