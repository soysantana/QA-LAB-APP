<?php
$user = current_user();

$Search = $_GET['id'];
if (isset($_POST['update'])) {
    $req_fields = array(
        'SampleName',
        'Standard',
        'Technician',
        'DateTesting'
    );
    validate_fields($req_fields);

    if (empty($errors)) {
        $ProjectName = $db->escape($_POST['ProjectName']);
        $Client = $db->escape($_POST['Client']);
        $ProjectNumber = $db->escape($_POST['ProjectNumber']);
        $Structure = $db->escape($_POST['Structure']);
        $Area = $db->escape($_POST['Area']);
        $Source = $db->escape($_POST['Source']);
        $CollectionDate = $db->escape($_POST['CollectionDate']);
        $SampleID = $db->escape($_POST['SampleName']);
        $SampleNumber = $db->escape($_POST['SampleNumber']);
        $DepthFrom = $db->escape($_POST['DepthFrom']);
        $DepthTo = $db->escape($_POST['DepthTo']);
        $MType = $db->escape($_POST['MType']);
        $SType = $db->escape($_POST['SType']);
        $North = $db->escape($_POST['North']);
        $East = $db->escape($_POST['East']);
        $Elev = $db->escape($_POST['Elev']);
        $SampleBy = $db->escape($_POST['SampleBy']);
        // ohters
        $Standard = $db->escape($_POST['Standard']);
        $Technician = $db->escape($_POST['Technician']);
        $DateTesting = $db->escape($_POST['DateTesting']);
        $Comments = $db->escape($_POST['Comments']);
        $FieldComment = $db->escape($_POST['FieldComment']);
        $PMethods = $db->escape($_POST['PMethods']);
        $SMethods = $db->escape($_POST['SMethods']);
        $TestMethod = $db->escape($_POST['TestMethod']);
        $ModifiedBy = $user['name'];
        $ModifiedDate = make_date();
        $TestType = "SCT";

        $natMc = $db->escape($_POST['natMc']);
        $optimunMc = $db->escape($_POST['optimunMc']);
        $Time = $db->escape($_POST['Time']);
        $TimeSet = $db->escape($_POST['TimeSet']);
        $initialHeight = $db->escape($_POST['initialHeight']);
        $FinalHeight = $db->escape($_POST['FinalHeight']);
        $Average = $db->escape($_POST['Average']);
        $testResult = $db->escape($_POST['testResult']);

        $combinedCollapsed = '';
        if (isset($_POST['Collapsed']) && is_array($_POST['Collapsed'])) {
            $data = [];
            foreach ($_POST['Collapsed'] as $val) {
                $v = trim($val);
                $data[] = ($v === '') ? 'null' : $db->escape($v);
            }
            $combinedCollapsed = implode(',', $data);
        }

        function buildCSV($name, $total, $db)
        {
            $data = [];
            for ($i = 1; $i <= $total; $i++) {
                $key = $name . $i;
                $val = isset($_POST[$key]) ? trim($_POST[$key]) : '';
                $data[] = ($val === '') ? 'null' : $db->escape($val);
            }
            return implode(',', $data);
        }


        $combinedRadius = buildCSV('Radius', 5, $db);
        $combinedAngle = buildCSV('Angle', 5, $db);

        $query = "UPDATE sand_castle_test SET ";
        $query .= "Project_Name = '{$ProjectName}',";
        $query .= "Client = '{$Client}', ";
        $query .= "Project_Number = '{$ProjectNumber}', ";
        $query .= "Structure = '{$Structure}', ";
        $query .= "Area = '{$Area}', ";
        $query .= "Source = '{$Source}', ";
        $query .= "Sample_Date = '{$CollectionDate}', ";
        $query .= "Sample_ID = '{$SampleID}', ";
        $query .= "Sample_Number = '{$SampleNumber}', ";
        $query .= "Depth_From = '{$DepthFrom}', ";
        $query .= "Depth_To = '{$DepthTo}', ";
        $query .= "Material_Type = '{$MType}', ";
        $query .= "Sample_Type = '{$SType}', ";
        $query .= "North = '{$North}', ";
        $query .= "East = '{$East}', ";
        $query .= "Elev = '{$Elev}', ";
        $query .= "Sample_By = '{$SampleBy}', ";
        $query .= "Standard = '{$Standard}', ";
        $query .= "Technician = '{$Technician}', ";
        $query .= "Test_Start_Date = '{$DateTesting}', ";
        $query .= "Comments = '{$Comments}', ";
        $query .= "FieldComment = '{$FieldComment}', ";
        $query .= "Preparation_Method = '{$PMethods}', ";
        $query .= "Split_Method = '{$SMethods}', ";
        $query .= "Methods = '{$TestMethod}', ";
        $query .= "Modified_Date = '{$ModifiedDate}', ";
        $query .= "Modified_By = '{$ModifiedBy}', ";
        $query .= "Test_Type = '{$TestType}', ";
        $query .= "natMc = '{$natMc}', ";
        $query .= "optimunMc = '{$optimunMc}', ";
        $query .= "Time = '{$Time}', ";
        $query .= "Collapsed = '{$combinedCollapsed}', ";
        $query .= "TimeSet = '{$TimeSet}', ";
        $query .= "initialHeight = '{$initialHeight}', ";
        $query .= "FinalHeight = '{$FinalHeight}', ";
        $query .= "Radius = '{$combinedRadius}', ";
        $query .= "Angle = '{$combinedAngle}', ";
        $query .= "Average = '{$Average}', ";
        $query .= "testResult = '{$testResult}'";
        $query .= "WHERE id = '{$Search}'";

        $result = $db->query($query);

        if ($result && $db->affected_rows() === 1) {
            $session->msg('s', 'La muestra ha sido actualizada');
            redirect('/reviews/sand-castle-test.php?id=' . $Search, false);
        } else {
            $session->msg('w', 'No se hicieron cambios');
            redirect('/reviews/sand-castle-test.php?id=' . $Search, false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('/reviews/sand-castle-test.php?id=' . $Search, false);
    }
}
