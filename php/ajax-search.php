<?php
require_once('../config/load.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ID = $db->escape($_POST['ID']);
    $Number = $db->escape($_POST['Number']);
    
    $moisture_query = "SELECT * FROM moisture_oven WHERE Sample_ID = '{$ID}' AND Sample_Number = '{$Number}'";
    $specific_gravity_query = "SELECT * FROM specific_gravity WHERE Sample_ID = '{$ID}' AND Sample_Number = '{$Number}'";
    
    $moisture_result = $db->query($moisture_query);
    $specific_gravity_result = $db->query($specific_gravity_query);
    
    if ($moisture_result && $moisture_record = $moisture_result->fetch_assoc()) {
        $mc_value = $moisture_record['Moisture_Content_Porce'];
        $sg_value = ($specific_gravity_result && $sg_record = $specific_gravity_result->fetch_assoc()) ? $sg_record['Specific_Gravity_Soil_Solid'] : null;
        
        echo json_encode([
            'success' => true,
            'message' => '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-1"></i> I found it!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>',
            'mc_value' => $mc_value,
            'sg_value' => $sg_value,
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-octagon me-1"></i> No results found.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>',
        ]);
    }
}
?>