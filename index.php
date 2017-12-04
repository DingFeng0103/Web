<!DOCTYPE html>
<?php
require_once('pdo.php');
session_start();
?>
<html>
<head>
  <?php require_once "bootstrap.php"; ?>

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

  <!-- Optional theme -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

  <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
  <!-- Custom styles for this template -->
  <link href="starter-template.css" rel="stylesheet">
  <title>d4b2aeb2</title>

</head>
<body>
  <div class="container">
<h1>Welcome to Feng's Resume Registry</h1>
<p>
<?php
if(isset($_SESSION['success'])){
  echo '<p style="color:green">'.htmlentities($_SESSION['success']).'</p>';
  unset($_SESSION['success']);
}
if(isset($_SESSION['error'])){
  echo '<p style="color:red">'.htmlentities($_SESSION['error']).'</p>';
  unset($_SESSION['error']);
}
if(isset($_SESSION['name'])){
  echo '<p><a href="Logout.php">Logout</a></p>';
}else{
  echo '<p><a href="login.php">Please log in</a></p>';

}
$stmt = $pdo->query("SELECT profile_id,first_name,last_name,headline FROM profile ORDER BY profile_id");
if($stmt->rowCount()>0){
  echo '<table border="1">'.'<thead><th>Name</th><th>Headline</th><th>Action</th></thead>';
  echo '<tbody>';
  while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      echo("<tr>");
      echo('<td>'.'<a href="view.php?profile_id='.$row['profile_id'].'">'.$row['first_name'].$row['last_name'].'</a></td><td>'.$row['headline'].'</td>');
      echo('<td><a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a><a href="delete.php?profile_id='.$row['profile_id'].'"> Delete</a></td>');
      echo("</tr>");
  }
  echo '</tbody></table>';
}
if(isset($_SESSION['name'])){
  echo '<p><a href="add.php">Add New Entry</a></p>';
}

?>
</p>

</div>

</body>

</html>
