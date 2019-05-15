<?php

require '../partials/search.php';
require '../functions.php';
require '../models/Company.php';
require '../models/MarketingContact.php';



$db = require('../services/db.php');

//query to display company name
$sql = "SELECT * FROM companies WHERE id = :id";
$sth = $db->prepare($sql);
$sth->setFetchMode(PDO::FETCH_INTO, new Company);
$sth->execute([ 'id' => $_GET['id'] ]);
$company = $sth->fetch();

//Update the status value in database when submitted with checked checkboxes

//query for table display
//$sqlqwe = "SELECT * FROM marketing_contacts WHERE company_id = $1";
$sqlqwe = "SELECT mc.id, mc.name, mc.email, mc.updated_at, mc.status, mc.telephone, count(*) 
  as ads_count FROM marketing_contacts mc 
  join marketing_ad ma on ma.marketing_contact_id = mc.id
WHERE mc.company_id = :id 
group by mc.id, mc.name, mc.email, mc.updated_at, mc.status, mc.telephone
order by count(*) desc, updated_at desc";

$sth = $db ->prepare($sqlqwe);
$sth->execute([ 'id' => $_GET['id'] ]);
$contacts =  $sth ->fetchAll(PDO::FETCH_CLASS, 'MarketingContact');
$companyId = $_GET['id'];

renderView('company', compact('contacts','company', 'companyId') );