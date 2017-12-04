<?php
require_once('pdo.php');
require_once('utility.php');
session_start();
if(!isset($_SESSION['profile_id'])){
  $_SESSION['profile_id']=$_GET['profile_id'];
}


if(isset($_POST['cancel'])){
  unset($_SESSION['error']);
  unset($_SESSION['profile_id']);
  header("Location:index.php");
  return ;
}
$stmt = $pdo->prepare('SELECT name FROM users WHERE user_id=:id');
$stmt->execute(array(':id'=>$_SESSION['user_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$name=$row['name'];

$stmt=$pdo->prepare("SELECT * FROM profile WHERE profile_id= :xyz");
$stmt->execute(array(":xyz"=>$_SESSION['profile_id']));
$row=$stmt->fetch(PDO::FETCH_ASSOC);
if(!isset($_SESSION['user_id'])||$row===false||$row['user_id']!==$_SESSION['user_id']){
  $_SESSION['error']="Access denied, you don't have the right to modify this profile";
  header("Location:index.php");
  return;
}
$fn=htmlentities($row['first_name']);
$ln=htmlentities($row['last_name']);
$em=htmlentities($row['email']);
$hl=htmlentities($row['headline']);
$su=htmlentities($row['summary']);
$profile_id=$row['profile_id'];
$stmtPos=$pdo->prepare("SELECT * FROM position WHERE profile_id=:xyz ORDER BY rank");
$stmtPos->execute(array(":xyz"=>$profile_id));
$stmtEdu=$pdo->prepare("SELECT * FROM education WHERE profile_id=:xyz ORDER BY rank");
$stmtEdu->execute(array(":xyz"=>$profile_id));
if(isset($_POST['edit'])){
  if(isset($_POST['first_name'])&&isset($_POST['last_name'])&&isset($_POST['email'])&&isset($_POST['headline'])&&isset($_POST['summary'])){
    if(strlen($_POST['first_name'])<1||strlen($_POST['last_name'])<1||strlen($_POST['email'])<1||strlen($_POST['headline'])<1||strlen($_POST['summary'])<1){
      $_SESSION['error']="All fields are required";
    }
    else if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
      $_SESSION['error']="Email address must contain @";
    }else if(($_SESSION['error']=validatePos())===true){
      unset($_SESSION['error']);
      $_SESSION['success']="Profile updated";
      $stmt=$pdo->prepare('UPDATE profile SET first_name=:fn,last_name=:ln,email=:em,headline=:hl,summary=:su WHERE profile_id=:zip');
      $stmt->execute(array(
        ':fn'=>$_POST['first_name'],
        ':ln'=>$_POST['last_name'],
        ':em' => $_POST['email'],
        ':hl' => $_POST['headline'],
        ':su' =>$_POST['summary'],
        ':zip' => $_POST['profile_id']
      ));
      $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id=:pid');
      $stmt->execute(array( ':pid' => $_REQUEST['profile_id']));
      $rank = 1;
      for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['year'.$i]) ) continue;
        if ( ! isset($_POST['desc'.$i]) ) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];
        $stmt=$pdo->prepare("INSERT INTO position (profile_id,rank,year,description) VALUES (:pid,:rank,:year,:desc)");
        $stmt->execute(array(":pid"=>$profile_id,"rank"=>$rank,"year"=>$year,":desc"=>$desc));
        $rank++;
      }
      $stmt = $pdo->prepare('DELETE FROM education WHERE profile_id=:pid');
      $stmt->execute(array( ':pid' => $_REQUEST['profile_id']));

      $rank = 1;
      for($i=1;$i<=9;$i++){
        if ( ! isset($_POST['edu_year'.$i]) ) continue;
        if ( ! isset($_POST['edu_school'.$i]) ) continue;
        $edu_year=$_POST['edu_year'.$i];
        $edu_school=$_POST['edu_school'.$i];
        $inst_id=validateInst($edu_school,$pdo);
        if($inst_id===false){
          $stmt=$pdo->prepare("INSERT INTO institution (name) VALUES (:edu_name)");
          $stmt->execute(array(":edu_name"=>$edu_school));
          $inst_id=$pdo->lastInsertId();
        }
        $stmt=$pdo->prepare("INSERT INTO education (profile_id,rank,year,institution_id) VALUES (:pid,:rank,:year,:inst)");
        $stmt->execute(array(":pid"=>$profile_id,"rank"=>$rank,"year"=>$edu_year,":inst"=>$inst_id));
        $rank++;
      }


      header("Location:index.php");
      return;
    }
    header("Location:edit.php?profile_id=".$_POST['profile_id']);
    return;
  }

}
 ?>
<html>
<head>
  <?php require_once "bootstrap.php"; ?>

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

  <!-- Optional theme -->
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
  <!-- Custom styles for this template -->
  <link href="starter-template.css" rel="stylesheet">
  <title>d4b2aeb2</title>

</head>
<body>
  <div class="container">
<h1>Editing Profile for <? print(htmlentities($name)) ?></h1>
<?php
if(isset($_SESSION['error'])){
  echo '<p style="color:red">'.htmlentities($_SESSION['error']).'</p>';
  unset($_SESSION['error']);
}
?>
<form method="POST">
  <p>
<label>First Name: </label>
<input type="text" name="first_name" value="<?=$fn?>" size="60"></input><br>
</p>
<p>
<label>Last Name: </label>
<input type="text" name="last_name" value="<?=$ln?>" size="60"></input><br>
</p>
  <p>
<label>Email: </label>
<input type="text" name="email" value="<?=$em?>" size="30"></input><br>
</p>
  <p>
<label>Headline: </label><br>
<input type="text" name="headline" value="<?=$hl?>" size="80"></input><br>
</p>
<p>
<label>Summary: </label><br>
<textarea name="summary" rows="8" cols="80"><?=$su?></textarea>
</p>
<p>
Education: <input type="submit" id="addEdu" value="+">
<div id="edu_fields">
  <?php
    for($i=1;$i<=$stmtEdu->rowCount();$i++){
      $rowEdu=$stmtEdu->fetch(PDO::FETCH_ASSOC);
      $inst_id=$rowEdu['institution_id'];
      $stmtInst_name=$pdo->query("SELECT name FROM institution WHERE institution_id=$inst_id");
      $stmtInst_name=$stmtInst_name->fetch(PDO::FETCH_ASSOC)['name'];
      echo('<div id="edu'.$i.'">');
      echo('<p>Year: <input type="text" name="edu_year'.$i.'" value="'.$rowEdu['year'].'"/>');
      echo('<input type="button" value="-" onclick="removeEdu();return false;"></p>');
      echo('<p>School: <input type="text" size="80" name="edu_school'.$i.'" class="school" value="'.$stmtInst_name.'" /></p></div>');
    }
  ?>
</div>
</p>
<p>
Position: <input type="submit" id="addPos" value="+">
<div id="position_fields">
  <?php
    for($i=1;$i<=$stmtPos->rowCount();$i++){
      $rowPos=$stmtPos->fetch(PDO::FETCH_ASSOC);
      echo('<div id="position'.$i.'">');
      echo('<p>Year: <input type="text" name="year'.$i.'" value="'.$rowPos['year'].'"/>');
      echo('<input type="button" value="-" onclick="removePos();return false;"></p>');
      echo('<textarea name="desc'.$i.'" rows="8" cols="80">'.$rowPos['description'].'</textarea></div>');
    }
  ?>

</div>
</p>
<input type="hidden" name='profile_id' value="<?= $profile_id?>"> </input>
<input type="submit"  name="edit" value="Save"></input>
<input type="submit" name="cancel" value="Cancel"></input>
</form>
<script>


countPos = <?echo $stmtPos->rowCount();?>;
countEdu= <?echo $stmtEdu->rowCount();?>;
// http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
$(document).ready(function(){
    window.console && console.log('Document ready called');
    $('#addPos').click(function(event){
        // http://api.jquery.com/event.preventdefault/
        event.preventDefault();
        if ( countPos >= 9 ) {
            alert("Maximum of nine position entries exceeded");
            return;
        }
        countPos++;
        window.console && console.log("Adding position "+countPos);
        $('#position_fields').append(
            '<div id="position'+countPos+'"> \
            <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
            <input type="button" value="-" \
                onclick="removePos();return false;"></p> \
            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
            </div>');
    });
    $('#addEdu').click(function(event){
        event.preventDefault();
        if ( countEdu >= 9 ) {
            alert("Maximum of nine education entries exceeded");
            return;
        }
        countEdu++;
        window.console && console.log("Adding education "+countEdu);

        $('#edu_fields').append(
            '<div id="edu'+countEdu+'"> \
            <p>Year: <input type="text" name="edu_year'+countEdu+'" value="" /> \
            <input type="button" value="-" onclick="removeEdu();return false;"><br>\
            <p>School: <input type="text" size="80" name="edu_school'+countEdu+'" class="school" value="" />\
            </p></div>'
        );

        $('.school').autocomplete({
            source: "school.php"
        });

    });
});
</script>
</div>
</body>

</html>
