<?php
  require_once('../config/load.php');
  if (!$session->isUserLoggedIn(true)) { redirect('index.php', false);}
?>

<?php
 // Auto suggetion
    $html = '';
   if(isset($_POST['product_name']) && strlen($_POST['product_name']))
   {
     $products = find_product_by_title($_POST['product_name']);
     if($products){
        foreach ($products as $product):
           $html .= "<li class=\"list-group-item\">";
           $html .= $product['Sample_ID'] . '-' . $product['Sample_Number'];
           $html .= "</li>";
         endforeach;
      } else {

        $html .= '<li onClick="fill(\''.addslashes($product['Sample_ID']).'\')" class="list-group-item">';
        $html .= 'No encontrado';
        $html .= "</li>";

      }

      echo json_encode($html);
   }
 ?>
 <?php
 // find all product
  if(isset($_POST['p_name']) && strlen($_POST['p_name']))
  {
    $product_title = remove_junk($db->escape($_POST['p_name']));
    if($results = find_all_product_info_by_title($product_title)){
        foreach ($results as $result) {

          $html .= '<div class="col-lg-12">';

          $html .= '<div class="card">';
          $html .= '<div class="card-body">';
          $html .= '<h5 class="card-title">Sample Information</h5>';

          $html .= '<div class="row g-3">';

          $html .= '<div class="col-md-4">';
          $html .= '<label for="ProjectName" class="form-label">Project Name</label>';
          $html .= '<input type="text" class="form-control" name="ProjectName" id="ProjectName" value="' . $result['Project_Name'] . '">';
          $html .= '</div>';

          $html .= '<div class="col-md-4">';
          $html .= '<label for="Client" class="form-label">Client</label>';
          $html .= '<input type="text" class="form-control" name="Client" id="Client" value="' . $result['Client'] . '">';
          $html .= '</div>';

          $html .= '<div class="col-md-4">';
          $html .= '<label for="ProjectNumber" class="form-label">Project Number</label>';
          $html .= '<input type="text" class="form-control" name="ProjectNumber" id="ProjectNumber" value="' . $result['Project_Number'] . '">';
          $html .= '</div>';

          $html .= '<div class="col-md-4">';
          $html .= '<label for="Structure" class="form-label">Structure</label>';
          $html .= '<input type="text" class="form-control" name="Structure" id="Structure" value="' . $result['Structure'] . '">';
          $html .= '</div>';

          $html .= '<div class="col-md-4">';
          $html .= '<label for="Area" class="form-label">Work Area</label>';
          $html .= '<input type="text" class="form-control" name="Area" id="Area" value="' . $result['Area'] . '">';
          $html .= '</div>';

          $html .= '<div class="col-md-4">';
          $html .= '<label for="Source" class="form-label">Borrow Source</label>';
          $html .= '<input type="text" class="form-control" name="Source" id="Source" value="' . $result['Source'] . '">';
          $html .= '</div>';

          $html .= '<div class="col-md-4">';
          $html .= '<label for="MType" class="form-label">Material Type</label>';
          $html .= '<input type="text" class="form-control" name="MType" id="MType" value="' . $result['Material_Type'] . '">';
          $html .= '</div>';

          $html .= '<div class="col-md-4">';
          $html .= '<label for="SType" class="form-label">Sample Type</label>';
          $html .= '<input type="text" class="form-control" name="SType" id="SType" value="' . $result['Sample_Type'] . '">';
          $html .= '</div>';

          $html .= '<div class="col-md-4">';
          $html .= '<label for="SampleName" class="form-label">Sample Name</label>';
          $html .= '<input type="text" class="form-control" name="SampleName" id="SampleName" value="' . $result['Sample_ID'] . '">';
          $html .= '</div>';

          $html .= '<div class="col-md-4">';
          $html .= '<label for="SampleNumber" class="form-label">Sample Number</label>';
          $html .= '<input type="text" class="form-control" name="SampleNumber" id="SampleNumber" value="' . $result['Sample_Number'] . '">';
          $html .= '</div>';

          $html .= '<div class="col-md-4">';
          $html .= '<label for="Sample Date" class="form-label">Sample Date</label>';
          $html .= '<input type="text" class="form-control" name="CollectionDate" id="Sample Date" value="' . $result['Sample_Date'] . '">';
          $html .= '</div>';

          $html .= '<div class="col-md-4">';
          $html .= '<label for="SampleBy" class="form-label">Sample By</label>';
          $html .= '<input type="text" class="form-control" name="SampleBy" id="SampleBy" value="' . $result['Sample_By'] . '">';
          $html .= '</div>';

          $html .= '<div class="col-md-4">';
          $html .= '<label for="Depth From" class="form-label">Depth From</label>';
          $html .= '<input type="text" class="form-control" name="DepthFrom" id="Depth From" value="' . $result['Depth_From'] . '">';
          $html .= '</div>';

          $html .= '<div class="col-md-2">';
          $html .= '<label for="Depth To" class="form-label">Depth To</label>';
          $html .= '<input type="text" class="form-control" name="DepthTo" id="Depth To" value="' . $result['Depth_To'] . '">';
          $html .= '</div>';

          $html .= '<div class="col-md-2">';
          $html .= '<label for="North" class="form-label">North</label>';
          $html .= '<input type="text" class="form-control" name="North" id="North" value="' . $result['North'] . ' ">';
          $html .= '</div>';

          $html .= '<div class="col-md-2">';
          $html .= '<label for="East" class="form-label">East</label>';
          $html .= '<input type="text" class="form-control" name="East" id="East" value="' . $result['East'] . '">';
          $html .= '</div>';

          $html .= '<div class="col-md-2">';
          $html .= '<label for="Elevation" class="form-label">Elevation</label>';
          $html .= '<input type="text" class="form-control" name="Elev" id="Elevation" value="' . $result['Elev'] . '">';
          $html .= '</div>';

          $html .= '</div>';

          $html .= '</div>';
          $html .= '</div>';

          $html .= '</div>';

        }
    } else {
        $html .= '<div class="col-md-4">';
        $html .= '<div class="alert alert-warning alert-dismissible fade show" role="alert">';
        $html .= '<i class="bi bi-exclamation-triangle me-1"></i>';
        $html .= 'Oops, Muestra no encontrada en la base de datos!';
        $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        $html .= '</div>';
        $html .= '</div>';
        
    }

    echo json_encode($html);
  }
 ?>


