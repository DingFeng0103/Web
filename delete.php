<?php
require_once("pdo.php");
session_start();



if(isset($_POST['delete'])&&isset($_POST['profile_id'])){
  $sql="DELETE FROM profile WHERE profile_id= :zip";
  $stmt=$pdo->prepare($sql);
  $stmt->execute(array(':zip'=> $_POST['profile_id']));
  $_SESSION['success']='Record deleted';
  header('Location:index.php');
  return;
}

$stmt=$pdo->prepare("SELECT first_name,last_name,profile_id FROM profile WHERE profile_id= :xyz");
$stmt->execute(array(":xyz"=>$_GET['profile_id']));
$row=$stmt->fetch(PDO::FETCH_ASSOC);
if(!isset($_SESSION['user_id'])){
  $_SESSION['error']="Access denied, you don't have the right to modify this profile";
  header("Location:index.php");
  return;
}
$profile_id=$row['profile_id'];
if(isset($_POST['cancel'])){
  unset($_SESSION['error']);
  unset($_SESSION['profile_id']);
  header("Location:index.php");
  return ;
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
<h1>Deleting: Profile <?=htmlentities($row['first_name'])?></h1>
<p>First Name: <?echo htmlentities($row['first_name'])?></p>
<p>Last Name: <?echo htmlentities($row['last_name'])?></p>
<form method="post">
  <input type="hidden" name="profile_id" value="<?echo($profile_id)?>"></input>
  <input type="submit" value="Delete" name="delete"></input>
  <input type="submit" name="cancel" value="Cancel"></input>
</form>
</div>
<body>
</body>
</html>
