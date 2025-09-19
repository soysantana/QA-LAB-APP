<?php
$packageId = $_GET['package_id'] ?? '';

if (isset($_POST['update-requisition'])) {

    if (empty($errors)) {
        $ProjectName    = $db->escape($_POST['ProjectName']);
        $Client         = $db->escape($_POST['Client']);
        $ProjectNumber  = $db->escape($_POST['ProjectNumber']);
        $PackageID      = $db->escape($_POST['PackageID']);
        $Structure      = $db->escape($_POST['Structure']);
        $CollectionDate = $db->escape($_POST['CollectionDate']);
        $Cviaje         = $db->escape($_POST['Cviaje']);
        $SampleName         = $db->escape($_POST['SampleName']);
        $SampleBy       = $db->escape($_POST['SampleBy']);
        $ModifiedDate   = make_date();
        $ModifiedBy     = $user['name'];

        $totalAffected = 0;

        // Actualizar datos generales
        $generalUpdate = "
            UPDATE lab_test_requisition_form SET
                Project_Name   = '{$ProjectName}',
                Client         = '{$Client}',
                Project_Number = '{$ProjectNumber}',
                Package_ID     = '{$PackageID}',
                Structure      = '{$Structure}',
                Sample_Date    = '{$CollectionDate}',
                Truck_Count    = '{$Cviaje}',
                Sample_ID      = '{$SampleName}',
                Sample_By      = '{$SampleBy}',
                Modified_Date  = '{$ModifiedDate}',
                Modified_By    = '{$ModifiedBy}'
            WHERE Package_ID = '{$packageId}'
        ";
        $resultGeneral = $db->query($generalUpdate);
        $totalAffected += $db->affected_rows();

        // Recorrer y actualizar las muestras
        $i = 0;
        while (isset($_POST["SampleNumber_{$i}"])) {
            $SampleNumber = $db->escape($_POST["SampleNumber_{$i}"]);
            $Area         = $db->escape($_POST["Area_{$i}"] ?? '');
            $Source       = $db->escape($_POST["Source_{$i}"] ?? '');
            $DepthFrom    = $db->escape($_POST["DepthFrom_{$i}"] ?? '');
            $DepthTo      = $db->escape($_POST["DepthTo_{$i}"] ?? '');
            $Comments     = $db->escape($_POST["Comments_{$i}"] ?? '');
            $MType        = $db->escape($_POST["MType_{$i}"] ?? '');
            $SType        = $db->escape($_POST["SType_{$i}"] ?? '');
            $North        = $db->escape($_POST["North_{$i}"] ?? '');
            $East         = $db->escape($_POST["East_{$i}"] ?? '');
            $Elev         = $db->escape($_POST["Elev_{$i}"] ?? '');
            $TestTypeArr  = $_POST["TestType_{$i}"] ?? [];
            $TestType     = implode(',', $TestTypeArr);

            $query = "
                UPDATE lab_test_requisition_form SET
                    Area          = '{$Area}',
                    Source        = '{$Source}',
                    Depth_From    = '{$DepthFrom}',
                    Depth_To      = '{$DepthTo}',
                    Material_Type = '{$MType}',
                    Sample_Type   = '{$SType}',
                    North         = '{$North}',
                    East          = '{$East}',
                    Elev          = '{$Elev}',
                    Comment       = '{$Comments}',
                    Test_Type     = '{$TestType}',
                    Modified_Date = '{$ModifiedDate}',
                    Modified_By   = '{$ModifiedBy}'
                WHERE Package_ID = '{$packageId}' AND Sample_Number = '{$SampleNumber}'
            ";
            $resultSample = $db->query($query);
            $totalAffected += $db->affected_rows();

            $i++;
        }

        // Revisar si hubo cambios
        if ($totalAffected > 0) {
            $session->msg('s', 'El paquete ha sido actualizado.');
        } else {
            $session->msg('w', 'No se realizaron cambios en el paquete.');
        }

        redirect('/pages/requisition-form-view.php', false);
    } else {
        $session->msg("d", $errors);
        redirect('/pages/requisition-form-view.php', false);
    }
}
