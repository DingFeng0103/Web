<?php
require_once('pdo.php');
require_once('utility.php');
session_start();

if(!isset($_SESSION['user_id'])){
    die("ACCESS DENIED");
}
$stmt = $pdo->prepare('SELECT name FROM users WHERE user_id=:id');
$stmt->execute(array(':id'=>$_SESSION['user_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$name=$row['name'];
if(isset($_POST['cancel'])){
  unset($_SESSION['error']);
  header("Location:index.php");
  return ;
}
if(isset($_POST['add'])){
  if(isset($_POST['first_name'])&&isset($_POST['last_name'])&&isset($_POST['email'])&&isset($_POST['headline'])&&isset($_POST['summary'])){
    if(strlen($_POST['first_name'])<1||strlen($_POST['last_name'])<1||strlen($_POST['email'])<1||strlen($_POST['headline'])<1||strlen($_POST['summary'])<1){
      $_SESSION['error']="All fields are required";
    }
    else if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
      $_SESSION['error']="Email address must contain @";
    }else if(($_SESSION['error']=validatePos())===true){
      unset($_SESSION['error']);
      $_SESSION['success']="profile added";
      $stmt=$pdo->prepare('INSERT INTO profile (user_id,first_name,last_name,email,headline,summary) VALUES (:uid,:fn,:ln,:em,:hl,:su)');
      $stmt->execute(array(
        ':uid'=>$_SESSION['user_id'],
        ':fn'=>$_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':hl'=>$_POST['headline'],
        'su'=>$_POST['summary']
      ));

      $profile_id=$pdo->lastInsertId();
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
      $rank=1;
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
    header("Location:add.php");
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
<h1>Adding Profile for <? print(htmlentities($name)) ?></h1>
<?php
if(isset($_SESSION['error'])){
  echo '<p style="color:red">'.htmlentities($_SESSION['error']).'</p>';
  unset($_SESSION['error']);
}else if(isset($_SESSION['success'])){
  unset($_SESSION['success']);
  echo '<p style="color:green">'.htmlentities($_SESSION['success']).'</p>';
}
?>
<form method="POST">
  <p>
First Name:
<input type="text" name="first_name" size="60"></input>
</p>
<p>
Last Name:
<input type="text" name="last_name" size="60"></input><br>
</p>
<p>
Email:
<input type="text" name="email" size="30"></input><br>
</p>
<p>
Headline:<br/>
<input type="text" name="headline" size="80"></input><br>
</p>
<p>
Summary:<br/>
<textarea name="summary" rows="8" cols="80"></textarea>
</p>
<p>
Education: <input type="submit" id="addEdu" value="+">
<div id="edu_fields">
</div>
</p>
<p>
Position: <input type="submit" id="addPos" value="+">
<div id="position_fields">
</div>
</p>
<p>
<input type="submit" name="add" value="Add"></input>
<input type="submit" name="cancel" value="Cancel"></input>
</p>
</form>
<script>
countPos = 0;
countEdu = 0;
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
