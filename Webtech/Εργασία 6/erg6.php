<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sports_store";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Σφάλμα σύνδεσης: " . $conn->connect_error);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    function cleanInput($data) {
        return htmlspecialchars(trim($data));
    }


    $gender = cleanInput($_POST['gender']);
    $firstName = cleanInput($_POST['firstName']);
    $lastName = cleanInput($_POST['lastName']);
    $birthDate = cleanInput($_POST['birthDate']);
    $category = cleanInput($_POST['category']);
    $size = cleanInput($_POST['size']);
    $color = cleanInput($_POST['color']);
    $quantity = cleanInput($_POST['quantity']);
    $address = cleanInput($_POST['address']);
    $city = cleanInput($_POST['city']);
    $zip = cleanInput($_POST['zip']);
    $phone = cleanInput($_POST['phone']);
    $email = cleanInput($_POST['email']);
    $password = cleanInput($_POST['password']);
    $confirmPassword = cleanInput($_POST['confirmPassword']);
    $terms = isset($_POST['terms']) ? 1 : 0;

    $errors = [];


    if (empty($gender)) $errors[] = "Το πεδίο 'Φύλο' είναι υποχρεωτικό.";
    if (empty($firstName) || !preg_match('/^\p{Greek}+$/u', $firstName)) {
        $errors[] = "Το πεδίο 'Όνομα' είναι υποχρεωτικό και πρέπει να περιέχει μόνο ελληνικά γράμματα.";
    }
    if (empty($lastName) || strlen($lastName) > 50) {
        $errors[] = "Το πεδίο 'Επώνυμο' είναι υποχρεωτικό και πρέπει να έχει έως 50 χαρακτήρες.";
    }
    if (empty($birthDate) || (time() - strtotime($birthDate)) < 16 * 365 * 24 * 60 * 60) {
        $errors[] = "Πρέπει να είστε άνω των 16 ετών.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Το email δεν είναι έγκυρο.";
    }
    if (empty($password) || !preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/', $password)) {
        $errors[] = "Το συνθηματικό πρέπει να περιέχει τουλάχιστον 8 χαρακτήρες, ένα κεφαλαίο γράμμα, έναν αριθμό και ένα ειδικό χαρακτήρα.";
    }
    if ($password !== $confirmPassword) {
        $errors[] = "Τα συνθηματικά δεν ταιριάζουν.";
    }
    if (!$terms) {
        $errors[] = "Πρέπει να αποδεχτείτε τους όρους και προϋποθέσεις.";
    }


    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
        exit;
    }


    $stmt = $conn->prepare("INSERT INTO orders (gender, first_name, last_name, birth_date, category, size, color, quantity, address, city, zip, phone, email, password, terms) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $stmt->bind_param("ssssssssssssssi", $gender, $firstName, $lastName, $birthDate, $category, $size, $color, $quantity, $address, $city, $zip, $phone, $email, $hashedPassword, $terms);

    if ($stmt->execute()) {
        echo "<p style='color:green;'>Η παραγγελία σας αποθηκεύτηκε με επιτυχία.</p>";
    } else {
        echo "<p style='color:red;'>Σφάλμα κατά την αποθήκευση της παραγγελίας: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

$conn->close();
?>

