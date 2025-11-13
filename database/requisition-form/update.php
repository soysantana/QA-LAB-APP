<?php
$OldPackageID = $_POST['OldPackageID'] ?? '';
$user = current_user();

if (isset($_POST['update-requisition'])) {
    if (empty($errors)) {
        $ProjectName    = $db->escape($_POST['ProjectName']);
        $Client         = $db->escape($_POST['Client']);
        $ProjectNumber  = $db->escape($_POST['ProjectNumber']);
        $PackageID      = $db->escape($_POST['PackageID']);
        $OldPackageID   = $db->escape($_POST['OldPackageID']);
        $Structure      = $db->escape($_POST['Structure']);
        $CollectionDate = $db->escape($_POST['CollectionDate']);
        $Cviaje         = $db->escape($_POST['Cviaje']);
        $SampleBy       = $db->escape($_POST['SampleBy']);
        $ModifiedDate   = make_date();
        $ModifiedBy     = $user['name'];

        $totalAffected = 0;

        // === 1) Actualizar datos generales del paquete ===
        if (!empty($OldPackageID)) {
            $generalUpdate = "
                UPDATE lab_test_requisition_form SET
                    Project_Name   = '{$ProjectName}',
                    Client         = '{$Client}',
                    Project_Number = '{$ProjectNumber}',
                    Package_ID     = '{$PackageID}',
                    Structure      = '{$Structure}',
                    Sample_Date    = '{$CollectionDate}',
                    Truck_Count    = '{$Cviaje}',
                    Sample_By      = '{$SampleBy}',
                    Modified_Date  = '{$ModifiedDate}',
                    Modified_By    = '{$ModifiedBy}'
                WHERE Package_ID = '{$db->escape($OldPackageID)}'
            ";
            $resultGeneral = $db->query($generalUpdate);
            $totalAffected += $db->affected_rows();
        }

        // === 2) Actualizar muestras individuales + Test_Type ===
        if (!empty($OldPackageID)) {
            $i = 0;
            while (isset($_POST["SampleNumber_{$i}"])) {
                $SampleName       = $db->escape($_POST["SampleName_{$i}"]);
                $SampleNumber     = $db->escape($_POST["SampleNumber_{$i}"]);
                $OldSampleName    = $db->escape($_POST["OldSampleName_{$i}"]);
                $OldSampleNumber  = $db->escape($_POST["OldSampleNumber_{$i}"]);
                $Area             = $db->escape($_POST["Area_{$i}"] ?? '');
                $Source           = $db->escape($_POST["Source_{$i}"] ?? '');
                $DepthFrom        = $db->escape($_POST["DepthFrom_{$i}"] ?? '');
                $DepthTo          = $db->escape($_POST["DepthTo_{$i}"] ?? '');
                $Comments         = $db->escape($_POST["Comments_{$i}"] ?? '');
                $MType            = $db->escape($_POST["MType_{$i}"] ?? '');
                $SType            = $db->escape($_POST["SType_{$i}"] ?? '');
                $North            = $db->escape($_POST["North_{$i}"] ?? '');
                $East             = $db->escape($_POST["East_{$i}"] ?? '');
                $Elev             = $db->escape($_POST["Elev_{$i}"] ?? '');
                $TestTypeArr      = $_POST["TestType_{$i}"] ?? [];
                $TestType         = implode(',', $TestTypeArr);

                // --- 2A) Actualizar la requisición ---
                $query = "
                    UPDATE lab_test_requisition_form SET
                        Sample_ID     = '{$SampleName}',
                        Sample_Number = '{$SampleNumber}',
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
                    WHERE Package_ID   = '{$db->escape($OldPackageID)}'
                      AND Sample_ID     = '{$OldSampleName}' 
                      AND Sample_Number = '{$OldSampleNumber}'
                ";
                $db->query($query);
                $totalAffected += $db->affected_rows();

                // --- 2B) Sincronizar también en test_workflow ---
                // Mantiene los estados (Preparación / Realización / Entrega),
                // solo actualiza Sample_ID y Sample_Number a los nuevos valores.
                $qWorkflow = "
                    UPDATE test_workflow
                    SET 
                        Sample_ID     = '{$SampleName}',
                        Sample_Number = '{$SampleNumber}',
                        Updated_By    = '{$ModifiedBy}',
                        Updated_At    = '{$ModifiedDate}'
                    WHERE Sample_ID     = '{$OldSampleName}'
                      AND Sample_Number = '{$OldSampleNumber}'
                ";
                $db->query($qWorkflow);
                $totalAffected += $db->affected_rows();

                $i++;
            }
        }

        // === 3) Mensaje según cambios ===
        if ($totalAffected > 0) {
            $session->msg('s', !empty($OldPackageID) ? 'El paquete ha sido actualizado.' : 'La muestra ha sido actualizada.');
        } else {
            $session->msg('w', 'No se realizaron cambios.');
        }

        redirect('../pages/requisition-form-view.php', false);
    } else {
        $session->msg("d", $errors);
        redirect('../pages/requisition-form-view.php', false);
    }
}
