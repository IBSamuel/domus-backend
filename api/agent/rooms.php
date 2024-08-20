<?php
// Allow CORS (Cross-Origin Resource Sharing)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Database connection
include_once '../../config/db.php';

$database = new Database();
$db = $database->getConnection();
if (isset($_REQUEST['agentId'])) {
    $id = $_REQUEST['agentId'];
    $query = "SELECT * FROM rooms WHERE agentId = $id";

}else{
    // Prepare the query to fetch all rooms
    $query = "SELECT * FROM rooms";

}
$stmt = $db->prepare($query);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $rooms = array();

    while ($row = $result->fetch_assoc()) {
        // Convert comma-separated strings back into arrays for amenities, securityFeatures, utilitiesIncluded, and viewingTimes
        $row['amenities'] = explode(',', $row['amenities']);
        $row['securityFeatures'] = explode(',', $row['securityFeatures']);
        $row['utilitiesIncluded'] = explode(',', $row['utilitiesIncluded']);
        $row['viewingTimes'] = explode(',', $row['viewingTimes']);

        // Add room to the rooms array
        $rooms[] = $row;
    }

    // Return rooms as JSON response
    echo json_encode(array("status" => true, "rooms" => $rooms));
} else {
    echo json_encode(array("status" => false, "message" => "No rooms found."));
}

$stmt->close();
?>
