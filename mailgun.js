
//## MAILGUN ##
//MAILER LOOP
for(var i = 0; i < contactNames.length ; i++){
  //MAILGUN
  var data = {
  from: 'leo.ebenezer@student.kyh.se',
  to: 'leo.ebenezer@student.kyh.se',
  //DONT DELETE
  //from: 'Leo leo@workbuster.se' ,
  //to: 'leo@workbuster.se' ,
  //DONT DELETE
  subject: 'Hello ' + contactNames[i],
  text: 'Testing some Mailgun awesomeness!' ,
  html:  `<html> <b>First Time!</b> </html>` 
  };
  //mailgun.messages().send(data);
  console.log("======== Message sent ========");
  //MAILGUN END

  //sätter in ett värde i mail_sent columnen i marketing_contacts
  //går vidare i template serien på select_option
  await knex('marketing_contacts')
    .increment('mail_sent', 1)
    //.increment('select_option', 1)
    .where('id', newContacts[i].id)
}
//MAILER LOOP END


//SE OM EN VECKA HAR GÅTT SEN SENASTE EMAIL
const marketingContacts = await knex('marketing_contacts')
.select('last_seen', 'email', 'name', 'id', 'mail_sent', 'declined_date', 'company_name')
await Promise.all(
marketingContacts
  .filter(contact => 
    !contact.declined_date
    && moreThanOneWeekHasPassed(contact.last_seen)
    && contact.mail_sent < 3
  )
  .map(async contact => {
    if (!contact) return console.log('Inga kontakter hittades')
  
    console.log("---------------------------------");
    console.log(`More than One week have passed for: ${contact.name} \n`, contact);

    //TOKEN KEY GENERATOR
    let tokenString;
    try {
      tokenString = await knex('marketing_contact_tokens')
        .insert({ marketing_contact_id:contact.id }).returning('token');
    } catch (err) {
      console.log('Problem with setting token', err)
    }//TOKEN KEY CREATOR END

    // handels the template value for the email
    let company = await knex('companies')
      .where('company_name', contact.company_name)
      .select('select_option')
      .first();
    
    //handels the status value for the customer
    let companyStatus = await knex('companies')
      .where('company_name', contact.company_name)
      .select('status')
      .first();
    console.log(companyStatus)
    

    //=== MAILGUN ===
    //MAIL CONTENT
    var data = {
      from: 'Leo leo@workbuster.se' ,
      to: 'leo@workbuster.se' ,
      subject: 'Hello ' + contact.name ,
      text: 'Testing some Mailgun awesomeness!' ,
      html: createEmailMessage(tokenString, company.select_option) //prova byt ut till ${option värdet}
      // (tokenString, template_values)
    };
    //MAIL CONTENT END
    try {
      //send(template1) hämtar en template från templates.js
      //byt ut till .send(data) för default
      await mailgun.messages().send(data);
    }catch (err) {
      console.log('Couldnt send email')
    }
    try {
      const dbResult = await knex('marketing_contacts')
        .where('id', parseInt(contact.id))
        //Ta bort update Date
        .update({
          mail_sent: knex.raw('mail_sent + 1'),
          date: new Date()
        })
    } catch (err) {
      console.log(err)
    }//===MAILGUN END===
    console.log("### A new Message has been sent ###");
  })
)