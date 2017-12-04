
<?php
require_once('pdo.php');
function validatePos() {
  for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['year'.$i]) ) continue;
    if ( ! isset($_POST['desc'.$i]) ) continue;
    $year = $_POST['year'.$i];
    $desc = $_POST['desc'.$i];
    if ( strlen($year) == 0 || strlen($desc) == 0 ) {
      return "All fields are required";
    }
    if ( ! is_numeric($year) ) {
      return "year must be numeric";
    }
  }
  for($i=1; $i<=9; $i++){
    if ( ! isset($_POST['edu_year'.$i]) ) continue;
    if ( ! isset($_POST['edu_school'.$i]) ) continue;
    $edu_year = $_POST['edu_year'.$i];
    $edu_school = $_POST['edu_school'.$i];
    if ( strlen($edu_year) == 0 || strlen($edu_school) == 0 ) {
      return "All fields are required";
    }
    if ( ! is_numeric($edu_year) ) {
      return "year must be numeric";
    }
  }
  return true;
};

function validateInst($Inst_name,$pdo){
  $stmt = $pdo->prepare('SELECT * FROM institution WHERE name=:Inst');
  $stmt->execute(array( ':Inst' => $Inst_name));
  $row=$stmt->fetch(PDO::FETCH_ASSOC);
  if($row){
    return $row['institution_id'];
  }else{
    return false;
  }



}

?>
<script>



function removePos() {
    $('#position'+countPos).remove();
    countPos--;
}
function removeEdu() {
    $('#edu'+countEdu).remove();
    countEdu--;
}
</script>
