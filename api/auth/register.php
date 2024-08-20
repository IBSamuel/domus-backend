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
if (
    !empty($data->fullName) &&
    !empty($data->username) &&
    !empty($data->email) &&
    !empty($data->password) &&
    !empty($data->role)
) {
    $fullName = $data->fullName;
    $username = $data->username;
    $email = $data->email;
    $password = password_hash($data->password, PASSWORD_BCRYPT); // Encrypt password
    $image = !empty($data->image) ? $data->image : null;
    $role = $data->role;
    $location = $data->location;
    $table = $role =="agent"?'agents':'users';

    // Check if the username or email already exists
    $checkQuery = "SELECT COUNT(*) FROM $table WHERE `username` = ? OR `email` = ?";
    $stmt = $db->prepare($checkQuery);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        echo json_encode(array("status" => false, "message" => "Username or email already exists."));
        exit();
    }

    if ($role == 'agent') {
        // Check if required fields for agent are present
        if (empty($data->phoneNumber)) {
            echo json_encode(array("status" => false, "message" => "Phone number is required for agents."));
            exit();
        }
        $phoneNumber = $data->phoneNumber;

        // Prepare query for agents
        $query = "INSERT INTO `agents` (fullName, username, email, phoneNumber, password, image, location, role) VALUES (?, ?, ?, ?, ?, ?,                                                                                                                                                                                               ?, ?)";

        $stmt = $db->prepare($query);
        $stmt->bind_param("ssssssss", $fullName, $username, $email, $phoneNumber, $password, $image, $location, $role);
    } else {
        // Prepare query for users
        $query = "INSERT INTO `users` (fullName, username, email, password, image, role) VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $db->prepare($query);
        $stmt->bind_param("ssssss", $fullName, $username, $email, $password, $image, $role);
    }

    // Execute query
    if ($stmt->execute()) {
        echo json_encode(array("status" => true, "message" => "Registration successful."));
    } else {
        echo json_encode(array("status" => false, "message" => "Unable to register. Please try again."));
    }

    $stmt->close();
} else {
    echo json_encode(array("status" => false, "message" => "Incomplete data. Please provide all required fields."));
}
?>
