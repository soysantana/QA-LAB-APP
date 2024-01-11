<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>
    <?php if (!empty($page_title)) echo remove_junk($page_title);
    elseif(!empty($user))
    echo ucfirst($user['name']);
    else echo "Home";?>
  </title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="/app/assets/img/favicon.ico" rel="icon">
  <link href="/app/assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Recursos Variados -->
  <script src="https://cdn.jsdelivr.net/npm/echarts-stat@1.2.0/dist/ecStat.min.js"></script>
  <script src="/app/assets/vendor/echarts/echarts.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

  <!-- Vendor CSS Files -->
  <link href="/app/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="/app/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="/app/assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="/app/assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="/app/assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="/app/assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="/app/assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="/app/assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="overlay" id="overlay"></div>
<div class="loader-container">
<span class="loader"></span>
</div>