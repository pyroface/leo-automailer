<!--
ACCEPT OFFER BUTTON
ACCEPT OFFER BUTTON
ACCEPT OFFER BUTTON
-->
<?php 

require '../functions.php';
$db = require('../services/db.php');

$pjong = "SELECT marketing_contact_id, company_id 
          FROM marketing_contact_tokens 
          WHERE token = :token";

$sth = $db ->prepare($pjong);
$sth->execute([ 'token' => $_GET['token'] ]);
$company = $sth->fetch(PDO::FETCH_OBJ);

$mlg = "UPDATE companies
        SET status = 'prospect'
        WHERE id = :companyID"; // gör så att den riktar till company id och inte contact id

$order = $db
  ->prepare($mlg)
  ->execute([
    'companyID' => $company->company_id
  ]);

renderView('accept-confirmation');

