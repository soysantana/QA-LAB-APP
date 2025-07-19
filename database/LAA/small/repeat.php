<?php
$user = current_user();

if (isset($_POST["repeat"])) {
    $Search = $_GET["id"];

    if (!empty($Search)) {
        $search_data = find_by_sql(
            "SELECT * FROM los_angeles_abrasion_small WHERE id = '{$Search}' LIMIT 1"
        );

        if ($search_data) {
            $ID = $search_data[0]["id"];
            $SampleID = $search_data[0]["Sample_ID"];
            $SampleNumber = $search_data[0]["Sample_Number"];
            $TestType = $search_data[0]["Test_Type"];

            $existing_record = find_by_sql(
                "SELECT * FROM test_repeat WHERE Sample_ID = '{$SampleID}' AND Sample_Number = '{$SampleNumber}' 
                AND Test_Type = '{$TestType}' AND Tracking = '{$ID}' LIMIT 1"
            );

            if (!$existing_record) {
                $id = uuid();
                $RegistedDate = make_date();
                $RegisterBy = $user["name"];

                $sql = "INSERT INTO test_repeat (
                    id,
                    Sample_ID,
                    Sample_Number,
                    Start_Date,
                    Register_By,
                    Test_Type,
                    Tracking,
                    Status
                )
                VALUES (
                    '$id',
                    '$SampleID',
                    '$SampleNumber',
                    '$RegistedDate',
                    '$RegisterBy',
                    '$TestType',
                    '$ID',
                    'Repeat'
                )";

                if ($db->query($sql)) {
                    $session->msg("s", "ensayo enviado para repetir");
                    redirect("/pages/essay-review.php", false);
                } else {
                }
            } else {
                $session->msg("w", "Este ensayo ya está en repetición.");
                redirect("../reviews/LAA-Small.php?id=" . $Search, false);
            }
        } else {
        }
    } else {
    }
}
