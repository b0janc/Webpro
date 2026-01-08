<?php
$conn = mysqli_connect("localhost", "root", "", "db_kiosk");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $rating = (int) $_POST['rating'];
    $pesan = mysqli_real_escape_string($conn, $_POST['pesan']);

    $query = "INSERT INTO feedback (customer_name, rating, comment) VALUES ('$nama', '$rating', '$pesan')";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Terima kasih atas ulasan Anda!'); window.location='feedback.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>