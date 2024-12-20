// <?php
//     $conn = mysqli_connect("185.209.75.32", "grvco_meghana", "Meghana@2024", "grvco_wpmeghadb");
    
//     if($con = false) {
//         die("Connection Error". mysqli_connect_error());
//     }
    
// ?>


<!--// $conn = mysqli_connect("server_address", "username", "password", "database_name");-->

<?php
$servername = "185.209.75.32"; // Replace with your server name
$username = "grvco_meghana"; // Replace with your database username
$password = "Meghana@2024"; // Replace with your database password
$dbname = "grvco_wpmeghadb"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
