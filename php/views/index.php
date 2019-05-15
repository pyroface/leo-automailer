<?php include '../views/layouts/header.php' ?>

<div class="row card-holder">
  <div class="col-sm">
    <div class="card">
      <div class="container">
        <h1 class="display-4"></h1>
        <p > Ny företag idag: <span class="companiesCount lead"> 
          <strong><?= $newCompanies->count ?></span></strong>
        </p>
      </div>
    </div>
  </div>
  <div class="col-sm">
    <div class="card">
      <div class="container">
        <h1 class="display-4"></h1>
        <p> Mail som har skickats idag:
        <span class="companiesCount lead">
          <strong><?= $mailSentToday->sum ?></strong>
        </span>
        </p>
      </div>
    </div>
  </div>
  <div class="col-sm">
    <div class="card">
      <div class="container">
        <h1 class="display-4"></h1>
        <p> Obehandlade:
        <span class="companiesCount lead">
          <strong><?= $unmanaged->count ?></strong>
        </span>
        </p>
      </div>
    </div>
  </div>
</div>
<br />
 
<div class="card chart">
  <canvas id="myChart" width="400" height="75"></canvas>
</div>
<script type="text/javascript">
  var ctx = document.getElementById('myChart').getContext('2d');
  var myChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: [<?php echo $statisticsDays ?>],
      datasets: [{
        label: 'Email skickade senaste veckan',
        data: [<?php echo $statisticsDate ?>],
        backgroundColor: [
          'rgba(255, 99, 132, 0.2)',
          'rgba(54, 162, 235, 0.2)',
          'rgba(255, 206, 86, 0.2)',
          'rgba(75, 192, 192, 0.2)',
          'rgba(153, 102, 255, 0.2)',
          'rgba(255, 159, 64, 0.2)'
        ],
        borderColor: [
          'rgba(255, 99, 132, 1)',
          'rgba(54, 162, 235, 1)',
          'rgba(255, 206, 86, 1)',
          'rgba(75, 192, 192, 1)',
          'rgba(153, 102, 255, 1)',
          'rgba(255, 159, 64, 1)'
        ],
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        yAxes: [{
          ticks: {
            beginAtZero: true
          }
        }]
      }
    }
  });
</script>

<br />

<form method="GET" class="form">
  <div class="form-group">
    <?php foreach($statuses as $status): ?>
    <input type="checkbox" name="<?= $status ?>" <?=isset($_GET[$status]) ? "checked" : '' ?> >
    <?= $status ?> &nbsp;
    <?php endforeach?>
  </div>
  <div class="input-group">
    <input class="form-control" name="search" type="text" placeholder="Sök på företagsnamn här">
    <button class="input-group-text" type="submit">Sök</button>
  </div>
</form>
<br />

<?php if ($search) : ?>Visar resultat för "
<?= $search ?>"
<?php endif ?>
<?php if (count($companies) > 0) : ?>

<form action='save-companies.php' method='post' name='myform' id='myform'>
  <table class="table">

    <tr>
      <?php foreach ($tableColumns as $key => $val) : ?>
      <th class='headerRow active'>
        <?= $val ?><a href='?order=<?=$key?>&sort=<?= $sort === 'desc' ? 'asc' : 'desc' ?>' >&nbsp&darr;&uarr;</a></th>
      <?php endforeach ?>
    </tr>
    <?php foreach($companies as $company) : ?>

    <tr>
      <td>
        <?= $company->company_id ?>
      </td>
      <td><a href="<?= $company->link() ?>">
          <?= $company->company_name ?> </a></td>
      <td><a href="//<?= $company->company_domain ?>">
          <?= $company->company_domain ?>
      </td>
      <td>
        <select class='form-control-sm' name='status[<?=$company->company_id?>]'>
          <?php foreach($statusOptions as $option) : ?>
          <option value="<?=$option?>" <?=$company->status === (string)$option ? 'selected' : '' ?> >
            <?=$option?>
          </option>
          <?php endforeach ?>
        </select>
      </td>

      <td>
        <select class='form-control-sm' name='template[<?=$company->company_id?>]'>
          <?php foreach($templates as $value => $name) : ?>
          <option value="<?=$value?>"
            <?= $company->select_option === $value ? 'selected' : '' ?> >
            <?=$name?>
          </option>
          <?php endforeach?>
        </select>
      </td>

      <td class='centerColumn'>
        <?= $company->total_contacts ?>
      </td>
      <td class='centerColumn'>
        <a href="<?= $company->link() ?>">
          <span class="badge badge-pill badge-<?= $company->recipients === 0 ? 'warning' : 'success' ?>">
            <?= $company->recipients ?>
          </span>
        </a>
      </td>
      <td>
        <?= $company->createdAt() ?>
      </td>
    </tr>
    <?php endforeach ?>
  </table>

  <div class="float-right">
    <input class="btn btn-primary btn-sm" type='submit' name='submit' value='Spara ändringar' />
  </div>
</form>

<?php else : echo "0 Results"; ?>
<?php endif ?>

<?= pagination($sql, $resultsPerPage) ?>

<?php require '../views/layouts/footer.php' ?>