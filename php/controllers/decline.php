<?php

require '../functions.php';

$db = require('../services/db.php');

$tokenSQL = "SELECT marketing_contact_id, company_id FROM marketing_contact_tokens WHERE token= :token";

$sth = $db->prepare($tokenSQL);
$sth->execute([ 'token' => $_GET['token'] ]);

$contact = $sth->fetch(PDO::FETCH_OBJ);

$declineSQL = "
UPDATE marketing_contacts SET declined_date=NOW() WHERE id= :id ";

$declineRequest = $db
->prepare($declineSQL)
->execute([ 'id' => $contact->marketing_contact_id ]);

if (!$declineRequest) {
        throw new Exception('Failed to mark as declined');
}

renderView('decline-confirmation');