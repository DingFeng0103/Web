<?php
require_once('pdo.php');
session_start();
if(!isset($profile_id)){
  $profile_id=$_GET['profile_id'];
}
$stmt=$pdo->query("SELECT first_name,last_name,email,headline,summary FROM profile WHERE profile_id=$profile_id");
if($stmt->rowCount()>0){
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  $stmtPos=$pdo->query("SELECT year,description FROM position WHERE profile_id=$profile_id");
  $stmtEdu=$pdo->query("SELECT year,institution_id FROM education WHERE profile_id=$profile_id");


}else{
  die("No profile can be found");
}
 ?>

<html>
<head>
  <?php require_once "bootstrap.php"; ?>

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

  <!-- Optional theme -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

  <!-- Custom styles for this template -->
  <link href="starter-template.css" rel="stylesheet">
  <title>d4b2aeb2</title>

</head>
<body>
  <div class="container">
<h1>Profile information</h1>

  <? echo '<p>First Name: '.htmlentities($row['first_name']).'</p>'?>
  <? echo '<p>Last Name: '.htmlentities($row['last_name']).'</p>'?>
  <? echo '<p>Email: '.htmlentities($row['email']).'</p>'?>
  <p> Headline:<br>
    <?echo htmlentities($row['headline'])?>
  </p>
  <p>
    Summary:<br>
    <?echo htmlentities($row['summary'])?>
  </p>
  <?php
  if($stmtPos->rowCount()>0){
    echo '<p>Positions</p><ul>';
    while($row=$stmtPos->fetch(PDO::FETCH_ASSOC)){
      echo '<li>'.$row['year'].': '.$row['description'].'</li>';
    }
    echo '</ul>';
  }
  if($stmtEdu->rowCount()>0){
    echo '<p>Education</p><ul>';
    while($row=$stmtEdu->fetch(PDO::FETCH_ASSOC)){
      $inst_id=$row['institution_id'];
      $stmtInst_name=$pdo->query("SELECT name FROM institution WHERE institution_id=$inst_id");
      $stmtInst_name=$stmtInst_name->fetch(PDO::FETCH_ASSOC)['name'];
      echo '<li>'.$row['year'].': '.$stmtInst_name.'</li>';
    }
    echo '</ul>';
  }
  ?>




<p>
  <a href="index.php">Done </p>
</p>
</form>
</div>
</body>

</html>
