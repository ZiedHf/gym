
<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname="gym";
//$server_path = 'http://192.168.1.29/';
//$image_path='http://192.168.1.29/priyal/lakum/gym_master/webroot/upload/';
$server_path = 'http://127.0.0.1/';
$image_path='http://127.0.0.1/gym/webroot/upload/';
$image_pa=$image_path;
$conn = new mysqli($servername,$username, $password,$dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>