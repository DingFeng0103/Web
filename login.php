<?php
require_once('pdo.php');
session_start();

$salt='XyZzy12*_';
$stored_hash=hash('md5',$salt.'php123');
if ( isset($_POST['cancel'] ) ) {
    // Redirect the browser to game.php
    header("Location: index.php");
    return;
}
if(isset($_POST['email'])&&isset($_POST['pass'])){
    $target_hash=hash('md5',$salt.$_POST['pass']);
    $stmt=$pdo->prepare('SELECT user_id,name FROM users WHERE email=:em AND password =:pw');
    $stmt->execute(array(
      ':em'=>$_POST['email'],
      ':pw'=>$target_hash
    ));
    $row=$stmt->fetch(PDO::FETCH_ASSOC);
    if($row!==false){
      $_SESSION['name']=$row['name'];
      $_SESSION['user_id']=$row['user_id'];
      header("Location:index.php");
      return;
    }else{
      $_SESSION['error']="Incorrect password";
      error_log("Login fail ".$_POST['email']." $target_hash");
    }
    header("Location:login.php");
    return;

}


?>

<html>
<head>
  <?php require_once "bootstrap.php"; ?>
  <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

<!-- Custom styles for this template -->
<link href="starter-template.css" rel="stylesheet">
  <title>d4b2aeb2</title>

</head>
<body>
  <div class="container">
  <h1>Please Log In</h1>
  <?php
  if(isset($_SESSION["error"])){
    echo '<p style="color:red">'.htmlentities($_SESSION['error']).'</p>';
    unset($_SESSION['error']);
  }
  ?>
  <form method="POST">
    <label for="who"> User name: </label>
    <input type="text" name="email" id="email"> </input><br>
    <label for="pass">Password: </label>
    <input type="password" name="pass" id="pass"></input><br>
    <input type="submit" value="Log In" onclick="return doValidate();"></input>
    <input type="submit" name="cancel" value="Cancel"></input>

  </form>
  <p>For a password hint, view source and find a password hint in the HTML comments. But I can tell you ,it is 'php123'</p>
  <script>
  function doValidate(){

    console.log('Validating...');
    try{
      addr=document.getElementById('email').value;
      pw=document.getElementById('pass').value;
      console.log("Validating addr="+addr+" pw="+pw);
      if(addr==null || addr=="" ||pw==null ||pw==""){
        alert("Both fields must be filled out");
        return false;
      }
      if(addr.indexOf('@')==-1){
        alert("Invalid email address");
        return false;
      }
      return true;
    }catch(e){
      alert("error");
      return false;
    }
    return false;
  }

  </script>
</div>
</body>

</html>
