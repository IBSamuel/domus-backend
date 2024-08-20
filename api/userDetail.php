<?php
// Allow CORS (Cross-Origin Resource Sharing)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Database connection
include_once '../config/db.php';

$database = new Database();
$db = $database->getConnection();

// Get POST data
$data = json_decode(file_get_contents("php://input"));

// Check if data is received properly
if (!empty($data->email) && !empty($data->role)) {
    $email = $data->email;
    $role = $data->role;

    // Prepare query based on the role
    if ($role == 'agent') {
        $query = "SELECT * FROM `agents` WHERE `email` = ?";
    } else {
        $query = "SELECT * FROM `users` WHERE `email` = ?";
    }

    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $details = $result->fetch_assoc();

        // Unset the password field if it exists
        if (isset($details['password'])) {
            unset($details['password']);
        }

        echo json_encode(array(
            "status" => true,
            "message" => "Details fetched successfully.",
            "data" => $details
        ));
    } else {
        echo json_encode(array("status" => false, "message" => "No details found for the provided email and role."));
    }

    $stmt->close();
} else {
    echo json_encode(array("status" => false, "message" => "Incomplete data. Please provide both email and role."));
}
?>
