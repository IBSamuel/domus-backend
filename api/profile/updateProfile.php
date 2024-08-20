<?php
// Allow CORS (Cross-Origin Resource Sharing)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Database connection
include_once '../../../config/db.php';

$database = new Database();
$db = $database->getConnection();

// Get POST data
$data = json_decode(file_get_contents("php://input"));

// Check if data is received properly
if (!empty($data->email) && !empty($data->role)) {
    $email = $data->email;
    $role = $data->role;

    // Prepare variables for the update
    $fullName = !empty($data->fullName) ? $data->fullName : null;
    $username = !empty($data->username) ? $data->username : null;
    $phoneNumber = !empty($data->phoneNumber) ? $data->phoneNumber : null;
    $image = !empty($data->image) ? $data->image : null;

    // Construct the SQL query based on the role
    if ($role === 'Agent') {
        $query = "UPDATE `agents` SET fullName = ?, username = ?, phoneNumber = ?, image = ? WHERE email = ?";
    } else {
        $query = "UPDATE `users` SET fullName = ?, username = ?, image = ? WHERE email = ?";
    }

    $stmt = $db->prepare($query);

    // Bind parameters based on the role
    if ($role === 'Agent') {
        $stmt->bind_param("sssss", $fullName, $username, $phoneNumber, $image, $email);
    } else {
        $stmt->bind_param("ssss", $fullName, $username, $image, $email);
    }

    // Execute the query
    if ($stmt->execute()) {
        echo json_encode(array("status" => true, "message" => "Profile updated successfully."));
    } else {
        echo json_encode(array("status" => false, "message" => "Unable to update profile. Please try again."));
    }

    $stmt->close();
} else {
    echo json_encode(array("status" => false, "message" => "Incomplete data. Please provide both email and role."));
}
?>
