<?php
require '../partials/search.php';
require_once '../partials/pagination.php';
require '../functions.php';
require '../models/Company.php';

$db = require('../services/db.php');

//for the pagination
$resultsPerPage = 20;
$statuses = [ 'prospect', 'customer', 'decline', 'ignore'];
$statusOptions = [null, 'customer', 'ignore', 'prospect', 'decline'];

$order = filter_input(INPUT_GET, 'order', FILTER_SANITIZE_STRING) ?? 'created_at';
$sort = filter_input(INPUT_GET, 'sort', \FILTER_SANITIZE_STRING) ?? 'desc';
$search = filter_input(INPUT_GET, 'search', \FILTER_SANITIZE_STRING);
// Sorting END


//pagination limit on tabel size, shown in browser
if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page = 1; }; 
$start_from = ($page - 1) * $resultsPerPage;
// $sql = "SELECT * FROM companies ORDER BY $order $sort LIMIT {$results_per_page} OFFSET {$start_from}";
  
$sql = "SELECT
	mc.company_id,
	c.company_name,
	c.company_domain,
	c.status,
	c.select_option,
  c.created_at,
	count(*) AS total_contacts,
(
	select count(*)
	from marketing_contacts
	where status IS NOT NULL
	and company_id = mc.company_id
) as recipients
FROM marketing_contacts mc
JOIN companies c ON c.id = mc.company_id
GROUP BY mc.company_id, c.company_name, c.company_domain, c.status, c.select_option, c.created_at
ORDER BY $order $sort
LIMIT :resultsPerPage OFFSET :start_from";

// @todo escapa order!


if (isset($_GET['search']) ){
  $sql = search($sql, $_GET['search']);
}

$sth = $db->prepare($sql);
$sth->execute(compact('start_from', 'resultsPerPage'));
$sth->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Company');
$companies = $sth->fetchAll();

$tableColumns = [
  'company_id' => 'ID',
  'company_name' => 'Namn',
  'company_domain' => 'Domän',
  'status' => 'Status',
  'template' => 'Mailmall',
  'total_contacts' => 'Kontakter',
  'recipients' => 'Mottagare',
  'created_at' => 'Skapad'
];

//Lägg till nya templates här
//Var noga med att dom matchar det som står i "providers" i snurran.2.js
$templates = [
  '1' => 'default',
  '2' => 'teamtailor',
  '3' => 'reachmee' 
];

$compsToday = "SELECT count(*) from companies where created_at >= current_date::date;";
$newCompanies = $db->query($compsToday)->fetch(PDO::FETCH_OBJ);

$totalMailQuery = "SELECT sum(mail_sent)  
  from marketing_contacts
  where last_mail_sent >= current_date::date";
$mailSentToday = $db->query($totalMailQuery)->fetch(PDO::FETCH_OBJ);

$unmanagedQuery = " SELECT count(*) from companies where status IS NULL ";
$unmanaged = $db->query($unmanagedQuery)->fetch(PDO::FETCH_OBJ);

$statisticsDateQuery = "SELECT DATE(created_at) AS day,
  count(*)
FROM sent_emails
WHERE created_at BETWEEN current_date - interval '7 days' and CURRENT_TIMESTAMP
GROUP BY 1
ORDER BY 1";
$statisticsDateOriginal = $db->query($statisticsDateQuery)->fetchAll(PDO::FETCH_OBJ);

$statisticsDate = array_map(function($date){
  return $date->count;
}, $statisticsDateOriginal);
$statisticsDate = join($statisticsDate, ',');

$statisticsDays = array_map(function($date){
  $x = strtotime($date->day);
  return '"' . date('l', $x) . '"';
}, $statisticsDateOriginal);
$statisticsDays = join($statisticsDays, ',');

renderView('index', compact(
  'statusOptions', 'statuses', 'search',
  'companies', 'tableColumns',
  'sql', 'resultsPerPage', 'sort', 'templates', 
  'newCompanies', 'mailSentToday', 'unmanaged', 'statisticsDate', 'statisticsDays'
  )
);