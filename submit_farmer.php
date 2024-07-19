<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fcm";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $address = $conn->real_escape_string($_POST['address']);
    $farm_size = $conn->real_escape_string($_POST['farm_size']);
    $crop_types = $conn->real_escape_string($_POST['crop_types']);
    $farming_experience = $conn->real_escape_string($_POST['farming_experience']);
    $farming_methods = $conn->real_escape_string($_POST['farming_methods']);
    $certifications = $conn->real_escape_string($_POST['certifications']);
    $availability = $conn->real_escape_string($_POST['availability']);
    $preferred_payment = $conn->real_escape_string($_POST['preferred_payment']);

    // File upload handling
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["legal_documents"]["name"]);
    $uploadOk = 1;
    $documentFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check file size
    if ($_FILES["legal_documents"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($documentFileType != "pdf" && $documentFileType != "doc" && $documentFileType != "docx") {
        echo "Sorry, only PDF, DOC, DOCX files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES["legal_documents"]["tmp_name"], $target_file)) {
            // Insert form data into the database
            $stmt = $conn->prepare("INSERT INTO farmers (name, email, phone, address, farm_size, crop_types, farming_experience, farming_methods, certifications, availability, preferred_payment, legal_documents_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssssssss", $name, $email, $phone, $address, $farm_size, $crop_types, $farming_experience, $farming_methods, $certifications, $availability, $preferred_payment, $target_file);

            if ($stmt->execute()) {
                echo "Registration successful!";
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
} else {
    echo "Error: Form was not submitted.";
}

$conn->close();
?>
