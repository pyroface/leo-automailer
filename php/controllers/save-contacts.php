<?php
$db = require '../services/db.php';
//SUBMIT button if-statement
if(isset($_POST['submit']) ){
  
$sqlqwe = "SELECT mc.id, mc.name, mc.email, mc.last_seen, mc.status, mc.telephone, count(*) 
as ads_count FROM marketing_contacts mc 
join marketing_ad ma on ma.marketing_contact_id = mc.id
WHERE mc.company_id = :id 
group by mc.id, mc.name, mc.email, mc.last_seen, mc.status, mc.telephone
order by count(*) desc, last_seen desc";

$sth = $db ->prepare($sqlqwe);
$sth->execute([ 'id' => $_POST['company_id'] ]);
$contacts =  $sth ->fetchAll(PDO::FETCH_OBJ);

//Update the status value in database when submitted with checked checkboxes
foreach ($contacts as $contact) {
  $value = isset($_POST['send'][$contact->id]) ? 1 : null;
  
  $updateSQL = "UPDATE marketing_contacts 
  SET status = :status
  WHERE id = :id ";

  $updateRequest =  $db
    ->prepare($updateSQL)
    ->execute([
      'status' => $value,
      'id' => $contact->id
    ]);
}
}

$sec = "0";
header("Refresh: $sec; company.php?id={$_POST['company_id']}");