?php
// db.php
$DB_HOST = 'localhost';
$DB_NAME = 'db01fhrenybgrn';
$DB_USER = 'uyhezup6l0hgf';
$DB_PASS = 'pr634bpk3knb';
 
$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    die("DB Connect failed: " . $mysqli->connect_error);
}
$mysqli->set_charset("utf8mb4");
