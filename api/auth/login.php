<?php
// Allow CORS (Cross-Origin Resource Sharing)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Database connection
include_once '../../config/db.php';

$database = new Database();
$db = $database->getConnection();

// Get POST data
$data = json_decode(file_get_contents("php://input"));

// Check if data is received properly
if (!empty($data->email) && !empty($data->password)) {
    $email = $data->email;
    $password = $data->password;

    // Prepare query to fetch user by email
    $query = "SELECT email, password, role FROM `users` WHERE `email` = ? 
              UNION 
              SELECT email, password, role FROM `agents` WHERE `email` = ?";

    $stmt = $db->prepare($query);
    $stmt->bind_param("ss", $email, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($email, $hashedPassword, $role);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashedPassword)) {
            echo json_encode(array(
                "status" => true,
                "message" => "Login successful.",
                "email" => $email,
                "role" => $role
            ));
        } else {
            echo json_encode(array("status" => false, "message" => "Incorrect password."));
        }
    } else {
        echo json_encode(array("status" => false, "message" => "email not found."));
    }

    $stmt->close();
} else {
    echo json_encode(array("status" => false, "message" => "Incomplete data. Please provide both email and password."));
}
?>
