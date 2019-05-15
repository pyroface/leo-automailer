<?php

function pagination($sql, $results_per_page){
  global $db;

  $removeSqlLimit = explode('LIMIT', $sql);

  $allCompanies = $db
    ->query($removeSqlLimit[0])
    ->fetchAll(PDO::FETCH_OBJ);

  $this_page = "index.php";
  //$sql = "SELECT COUNT(id) AS total FROM companies";

  $total_pages = ceil(count($allCompanies) / $results_per_page); // calculate total pages with results
  # Original PHP code by Chirp Internet: www.chirp.com.au
  # Please acknowledge use of this code by including this header.

  if(!isset($_GET['page']) || !$page = intval($_GET['page'])) {
    $page = 1;
  }

  $linkextra = [];
  if(isset($_GET['search']) && $search = $_GET['search']) {
    $linkextra[] = "search=" . urlencode($search);
  }
  if(isset($_GET['order']) && $order = $_GET['order']) {
    $linkextra[] = "order=" . urlencode($order);
  }

  if(isset($_GET['sort']) && $sort = $_GET['sort']) {
    $linkextra[] = "sort=" . urlencode($sort);
  }

  $linkextra = implode("&amp;", $linkextra);
  if($linkextra) {
    $linkextra .= "&amp;";
  }

  // build array containing links to all pages
  $tmp = [];
  for($p=1, $i=0; $i < $total_pages; $p++, $i++) {
    if($page == $p) {
      // current page shown as bold, no link
      $tmp[] = "<b>{$p}</b>";
    } else {
      //the rest of the page links
      $tmp[] = "<a href=\"{$this_page}?{$linkextra}page={$p}\">{$p}</a>";
    }
  }
  // thin out the links (optional)
  for($i = count($tmp) - 3; $i > 1; $i--) {
    if(abs($page - $i - 1) > 2) {
      unset($tmp[$i]);
    }
  }
  $output = "";
  // display page navigation iff data covers more than one page
  if(count($tmp) > 1) {
    $output .= "<p>";
    if($page > 1) {
      // display 'Prev' link
      $output .= "<a href=\"{$this_page}?{$linkextra}page=" . ($page - 1) . "\">&laquo; Prev</a> | ";
    } else {
      $output .= "Page ";
    }
    $lastlink = 0;

    foreach($tmp as $i => $link) {
      if($i > $lastlink + 1) {
        $output .= " ... "; // where one or more links have been omitted
      } elseif($i) {
        $output .= " | ";
      }
      $output .= $link;
      $lastlink = $i;
    }
    if($page <= $lastlink) {
      // display 'Next' link
      $output .= " | <a href=\"{$this_page}?{$linkextra}page=" . ($page + 1) . "\">Next &raquo;</a>";
    }
    $output .= "</p>\n\n";
  }

  return $output;
}