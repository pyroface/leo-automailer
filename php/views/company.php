<?php include '../views/layouts/header.php' ?>

<script>
  function postToNode() {
    fetch('http://localhost:3333?id=<?= $companyId ?>')
      .then(res => res.json()).then(console.log)
  }
</script>

<?php echo $companyId ?>
 
<form action='save-contacts.php' method='post' name='myform1' id='myform1'>  
  <input type="hidden" name="company_id" value="<?= $companyId ?>">
  <p class="h2"><?= $company->company_name; ?></p>

  KONTAKT PERSONER
  <table class="table">
  <tr>
    <th >ID </th>
    <th>Namn</th> 
    <th>Telefon</th>
    <th>Email</th>
    <th class="centerColumn">Skicka</th> 
    <th class="centerColumn">Senast sedd</th>
    <th class="centerColumn">Annonser</th>
  </tr>
  
  <?php foreach($contacts as $contact) : ?>
    <tr>
    <td><?=  $contact->id?></td>
    <td><?=  $contact->name?></td>
    <td><?=  $contact->telephone?></td>
    <td><a href="mailto: <?=  $contact->email?> "><?=  $contact->email?></a> </td>
    <td class="centerColumn" >
      <input
        type="checkbox"
        name="send[<?=  $contact->id?>]"
        <?= $contact->status === '1' ? 'checked' : '' ?>
      >
    </td>
    <td class="centerColumn"><?=  $contact->lastSeen() ?></td>
    <td class="centerColumn"><?=  $contact->ads_count?></td>
    </tr>
  <?php endforeach ?>
  </table>

  <div class="float-right">
  <input type='submit' class="btn btn-primary btn-sm" name='submit' value='Spara' />
  <button onclick="postToNode()" type="button" class="btn btn-primary btn-sm">Skicka ut mail</button>
  </div>
</form>

<?php include '../views/layouts/footer.php' ?>