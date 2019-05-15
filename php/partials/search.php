<?php
function search($sql, $search){
  global $statuses;

  $whereClause = " WHERE (c.company_name ILIKE '%{$search}%' ";
  
  $parsedSearch = (int)$search;
  
  if ($parsedSearch) {
    $whereClause .= " OR mc.company_id = $parsedSearch";
  }
  $whereClause .= ")";
  
  $filteredStatuses = array_values(array_filter($statuses, function ($status) {
    return isset($_GET[$status]);
  }));
  
  // if ($_GET['prospect'] || $_GET['customer'] || 
  //     $_GET['decline'] || $_GET['ignore'] || $_GET['']) {
  if (count($filteredStatuses) > 0) {
    $whereClause .= " AND (";
  
    foreach($filteredStatuses as $key => $status) {
      if ($key) {
        $whereClause .= " OR ";
      }
      $whereClause .= " c.status = '$status' ";
    }
    $whereClause .= ")";
  }
  
  $groupClause = "GROUP BY";
  
  $parts = explode($groupClause, $sql);
  return implode('', [
    $parts[0],
    "{$whereClause} {$groupClause} ",
    $parts[1]
  ]);  
}  
