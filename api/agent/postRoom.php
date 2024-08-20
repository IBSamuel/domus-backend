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
if (!empty($data->agentId) && !empty($data->bed) && !empty($data->space) && !empty($data->roomType) && !empty($data->rent)) {
    $agentId = $data->agentId;
    $bed = $data->bed;
    $space = $data->space;
    $power_type = $data->power->type;
    $power_duration = $data->power->duration;
    $furnished = $data->furnished;
    $rating = $data->rating;
    $roomType = $data->roomType;
    $rent = $data->rent;
    $petFriendly = $data->petFriendly;
    $smokingAllowed = $data->smokingAllowed;
    $leaseTerm = $data->leaseTerm;
    $availability = $data->availability;
    $amenities = implode(',', $data->amenities);
    $laundry = $data->laundry;
    $cooling = $data->cooling;
    $neighborhood = $data->neighborhood;
    $noiseLevel = $data->noiseLevel;
    $securityFeatures = implode(',', $data->securityFeatures);
    $utilitiesIncluded = implode(',', $data->utilitiesIncluded);
    $viewingTimes = implode(',', $data->viewingTimes);
    $landlordContact = $data->landlordContact;
    $description = $data->description;

    // Insert query
    $query = "INSERT INTO rooms 
              (agentId, bed, space, power_type, power_duration, furnished, rating, roomType, rent, petFriendly, smokingAllowed, leaseTerm, availability, amenities, laundry, cooling, neighborhood, noiseLevel, securityFeatures, utilitiesIncluded, viewingTimes, landlordContact, description) 
              VALUES 
              (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $db->prepare($query);

    // Bind parameters
    $stmt->bind_param(
        "iisssdsdissssssssssssss",
        $agentId, $bed, $space, $power_type, $power_duration, $furnished, $rating, $roomType, $rent, $petFriendly, $smokingAllowed, $leaseTerm, $availability, $amenities, $laundry, $cooling, $neighborhood, $noiseLevel, $securityFeatures, $utilitiesIncluded, $viewingTimes, $landlordContact, $description
    );

    // Execute the query
    if ($stmt->execute()) {
        echo json_encode(array("status" => true, "message" => "Room details added successfully."));
    } else {
        echo json_encode(array("status" => false, "message" => "Unable to add room details."));
    }

    $stmt->close();
} else {
    echo json_encode(array("status" => false, "message" => "Incomplete data. Please provide all required fields."));
}
?>
