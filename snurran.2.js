const AFApi = require ('./afapi')
const knex = require('./knexdb')
const { emailSplitter } = require('./utils')
require('dotenv').config()

async function getJobs() {

  //Lagrar unika namn i en array
  let contactNames = [];
  //Lagrar kontakter till mail_sent
  let newContacts = [];

  // får fram antalet sidor om man kör:
  // wang.matchningslista.antal_sidor
  let wang = await AFApi('/platsannonser/matchning?lanid=1')

  //Loopar igenom flera sidor sedan skickar vidare till
  //loopen som letar efter annonser
  for (let page = 1; page <= wang.matchningslista.antal_sidor; page++){
    //Loopar genom en sida efter jobbannonser
    // i const res så läggs let page värde på i slutet av url:en
    let res;
    try {
       res = await AFApi('/platsannonser/matchning?lanid=1&sida=' + page)
    }catch(err){
      console.error(err)
      continue
    }

    for (const job of res.matchningslista.matchningdata){
      const result = await knex('marketing_ad').where('annonsid', job.annonsid).count().first()
      //const result1 = await knex('marketing_contacts').select('date').first()
      
      //checkar om annonsen redan finns i databasen
      if(result.count !== '0'){
        console.log('Annonsen finns redan i databasen.')
        continue
      } //if END

      let jobDetails
      let retry
      //Varnar om dom inte har en rekryteringssida
      for (retry = 0; retry < 3; retry++) {
        try {
          jobDetails = await AFApi('/platsannonser/' + job.annonsid)
          if(jobDetails.platsannons){
            break
          } else {
            console.error('Hittar inte annonsdetaljerna.')
          }
        } catch (err) {
          console.error(err)
          console.error(`Retry ${retry}...`)
        }
      }
      if (retry === 3) {
        console.error('Ger upp på denna annons, funkar inte')
        continue
      }
      
      const kontaktpersoner = jobDetails.platsannons.arbetsplats.kontaktpersonlista.kontaktpersondata
      const annons = jobDetails.platsannons.annons

      //checkar så att det finns en kontakt person i annonsen
      if(!kontaktpersoner){
        continue
      }//if END

      //checkar så att annonsen har en epostadress
      //den samlar objectet på index 0 så därför måste man passera 0.epostadress/telefonnummer/namn
      if(!kontaktpersoner[0].epostadress){
        continue
      }//if END
      
      //Delar ut data till alla olika kolumner
      //columner i marketing_ad
      const ad = {
        data: jobDetails, 
        annonsid: job.annonsid,
        url: annons.platsannonsUrl,
        title: annons.annonsrubrik
      }
      //columner för companies table
      const company = {
        //email columnen i databasen
        company_domain: emailSplitter(jobDetails.platsannons.arbetsplats.kontaktpersonlista.kontaktpersondata[0].epostadress),
        company_name: jobDetails.platsannons.arbetsplats.arbetsplatsnamn, 
        current_provider: jobDetails.platsannons.ansokan.webbplats
      }
      //columner för marketing_contacs table
      const contactPerson = {
        name: kontaktpersoner[0].namn,
        email: kontaktpersoner[0].epostadress,
        telephone: kontaktpersoner[0].telefonnummer,
        company_name: jobDetails.platsannons.arbetsplats.arbetsplatsnamn,
      }
      //skapar unikt ID för företaget i companies table
      let companies = await knex('companies')
        .select('id')
        .where('company_name', company.company_name)
      if(!companies[0]){
        const result = await knex('companies').insert(company).returning('id')
        companies[0] = { id: result[0] }
      }
      ad.company_id = companies[0].id

      //Finns ingen kontaktperson
      //tar inte med om det inte finns något namn
      if(!contactPerson.email && !contactPerson.telephone || 
        !contactPerson.name && !contactPerson.telephone ||
        !contactPerson.name && !contactPerson.email ) {
        console.log('Skapade ingen kontaktperson, uppgifter saknas')
        continue
      }
      //Inga dubbletter av emails i marketing_contacts
      let contacts = await knex('marketing_contacts')
        .select('*')
        .where('email', contactPerson.email)
      if(!contacts[0]){
        const result = await knex('marketing_contacts')
          .insert({ ...contactPerson, company_id: companies[0].id }).returning('*')
        contacts[0] = { id: result[0].id }

        //Denna stoppa in namn i contactNames arrayen
        //Så att det inte blir flera av samma mottagare
        contactNames.push(contactPerson.name)
        newContacts.push(result[0])
        console.log('Sparade ', contactPerson.name, 'i databasen')
      }else{
      // //update last_seen
      let updateContactLastSeen = await knex('marketing_contacts')
          .update('last_seen', new Date())
          .where('id', contacts[0].id)
          .toString()
        console.log(updateContactLastSeen)
      }
      // ////////////////////////////////////
      //lägg till current_provider template
      if(!company.current_provider){
        continue
      }
      //för att skapa nya templates. Skapa bara ett nytt objekt
      //med namnet på deras provider och template: ska ha värdet
      //från templates.js arrayen
      const providers = [
        {name: 'teamtailor', template: 2 },
        {name: 'reachmee', template: 3 },
      ];

      const provider = providers.find( p => company.current_provider.includes(p.name) );
      if(provider){
        console.log(provider.name + ' exists' )
        const bjugg = await knex('companies')
          .update('select_option', provider.template )
          .where('id', companies[0].id)
        console.log('!!!!!! teamtailor or lernia found !!!!!!!');
        console.log("Updated id:" + companies[0].id + " with teamtailor template")
      }
      // ////////////////////////////////////

      ad.marketing_contact_id = contacts[0].id
      await knex('marketing_ad').insert(ad)
        
    }//annons for loop END
    console.log("!! Page " + page + " END !!")

    for(var i = 0; i < contactNames.length ; i++){
      console.log("======== User added ========");
    }
    //tömmer arraysen
    function empty() { contactNames = []; newContacts = []; }
    empty();
  }//page for loop END

}//getJobs function END
//Ta bort nedstående för att få databasen att ta in data non-stop
getJobs().catch((err) => {
  console.error(err)
}).finally(async () => {
  await knex.destroy()
})