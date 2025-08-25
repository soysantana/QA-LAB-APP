<?php
$user = current_user();

$Search = $_GET['id'];
if (isset($_POST['update_sp'])) {
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
        $ModifiedDate = make_date();
        $ModifiedBy = $user['name'];
        $TestType = "SP";

        $NatMc = $db->escape($_POST['NatMc']);
        $SpecGravity = $db->escape($_POST['SpecGravity']);
        $MaxDryDensity = $db->escape($_POST['MaxDryDensity']);
        $OptimumMoisture = $db->escape($_POST['OptimumMoisture']);
        $CorrectedDryUnitWeigt = $db->escape($_POST['CorrectedDryUnitWeigt']);
        $CorrectedWaterContentFiner = $db->escape($_POST['CorrectedWaterContentFiner']);
        $WcPorce = $db->escape($_POST['WcPorce']);
        $Ydf = $db->escape($_POST['Ydf']);
        $PcPorce = $db->escape($_POST['PcPorce']);
        $PfPorce = $db->escape($_POST['PfPorce']);
        $Gm = $db->escape($_POST['Gm']);
        $Ydt = $db->escape($_POST['Ydt']);
        $YwKnm = $db->escape($_POST['YwKnm']);

        $Graph = $db->escape($_POST['Graph']);
        $Graph64 = str_replace('data:image/png;base64,', '', $Graph);

        $inputValues = array();
        for ($i = 1; $i <= 6; $i++) {
            $inputValues["WetSoilMod" . $i] = $db->escape($_POST["WetSoilMod$i"]);
            $inputValues["WtMold" . $i] = $db->escape($_POST["WtMold$i"]);
            $inputValues["WtSoil" . $i] = $db->escape($_POST["WtSoil$i"]);
            $inputValues["VolMold" . $i] = $db->escape($_POST["VolMold$i"]);
            $inputValues["WetDensity" . $i] = $db->escape($_POST["WetDensity$i"]);
            $inputValues["DryDensity" . $i] = $db->escape($_POST["DryDensity$i"]);
            $inputValues["DensyCorrected" . $i] = $db->escape($_POST["DensyCorrected$i"]);
            $inputValues["Container" . $i] = $db->escape($_POST["Container$i"]);
            $inputValues["WetSoilTare" . $i] = $db->escape($_POST["WetSoilTare$i"]);
            $inputValues["WetDryTare" . $i] = $db->escape($_POST["WetDryTare$i"]);
            $inputValues["WtWater" . $i] = $db->escape($_POST["WtWater$i"]);
            $inputValues["Tare" . $i] = $db->escape($_POST["Tare$i"]);
            $inputValues["DrySoil" . $i] = $db->escape($_POST["DrySoil$i"]);
            $inputValues["MoisturePorce" . $i] = $db->escape($_POST["MoisturePorce$i"]);
            $inputValues["MCcorrected" . $i] = $db->escape($_POST["MCcorrected$i"]);
        }

        $query = "UPDATE standard_proctor SET ";
        foreach ($inputValues as $key => $value) {
            $query .= "$key = '$value', ";
        }
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
        $query .= "Nat_Mc = '{$NatMc}', ";
        $query .= "Spec_Gravity = '{$SpecGravity}', ";
        $query .= "Max_Dry_Density_kgm3 = '{$MaxDryDensity}', ";
        $query .= "Optimun_MC_Porce = '{$OptimumMoisture}', ";
        $query .= "Corrected_Dry_Unit_Weigt = '{$CorrectedDryUnitWeigt}', ";
        $query .= "Corrected_Water_Content_Finer = '{$CorrectedWaterContentFiner}', ";
        $query .= "Wc_Porce = '{$WcPorce}', ";
        $query .= "YDF_Porce = '{$Ydf}', ";
        $query .= "PC_Porce = '{$PcPorce}', ";
        $query .= "PF_Porce = '{$PfPorce}', ";
        $query .= "GM_Porce = '{$Gm}', ";
        $query .= "YDT_Porce = '{$Ydt}', ";
        $query .= "Yw_KnM3 = '{$YwKnm}', ";
        $query .= "Graph = '{$Graph64}' ";
        $query .= "WHERE id = '{$Search}'";

        $result = $db->query($query);

        if ($result && $db->affected_rows() === 1) {
            $session->msg('s', 'La muestra ha sido actualizada');
            redirect('../../reviews/standard-proctor.php?id=' . $Search, false);
        } else {
            $session->msg('w', 'No se realizaron cambios');
            redirect('../../reviews/standard-proctor.php?id=' . $Search, false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../../reviews/standard-proctor.php?id=' . $Search, false);
    }
}
