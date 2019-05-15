// '[⊙,.●]'

const knex = require('./knexdb')
const AFApi = require ('./afapi')
const { createEmailMessage } = require('./templates')
const { moreThanOneWeekHasPassed } = require('./utils')

/** VVV LÄGG IN I .env VVV **/
var api_key = 'a34b561a0520a8dec2d069497e30e73c-b3780ee5-815b9f89';
var domain = 'sandboxaa2a7a4ef5fe46cf9a853b33b76a68fa.mailgun.org';
var mailgun = require('mailgun-js')( {apiKey: api_key, domain: domain} );
/** ^^^ LÄGG IN I .env ^^^ **/

require('dotenv').config()

var contactResultCompID = async function(id){
  const resultCompanyID = await knex('marketing_contacts')
    .select('id', 'name', 'email', 'company_id', 'company_name',
            'status', 'last_seen', 'declined_date')
    .where({ status: 1, company_id: id })
    .whereNull('declined_date')
    .andWhere('mail_sent', '<', 3)
    //.where('last_seen', '>=', knex.raw(`now() - (?*'1 HOUR'::INTERVAL)`, [168]))
    .where('last_seen', '<', new Date() )
    
  if(resultCompanyID.length){
    await Promise.all(
      resultCompanyID
        .filter(contact => moreThanOneWeekHasPassed(contact.last_seen))
        .map(async contact => { 
          //TOKEN KEY GENERATOR
          let tokenString;
          try {
            tokenString = await knex('marketing_contact_tokens')
              .insert({ marketing_contact_id:contact.id, 
                  company_id: contact.company_id })
              .returning('token');
          }catch (err) {
            console.log('Problem with setting token', err)
          }//TOKEN KEY CREATOR END

          // handels the template value for the email
          let company = await knex('companies')
            .where('id', id)
            // .select('select_option')
            .first();

          console.log(resultCompanyID)
          console.log({company, companyName: contact.company_name })
          //MAILGUN 
          var data = {
            from: 'Leo leo@workbuster.se' ,
            to: 'leo@workbuster.se' ,
            subject: 'Hello ' + contact.name ,
            //text: 'Testing some Mailgun awesomeness!' ,
            html: createEmailMessage(tokenString, company.select_option)
          };
          mailgun.messages().send(data, function (error, body) {
            if (error) {
              console.log('xxxxxxxxxxxxxxxxxxxxxxxx')
              console.log('Ett fel inträffade', error)
            }
            console.log(body)
          });
          console.log("======== Message sent ========");
          //MAILGUN END

          await knex('marketing_contacts')
            .increment('mail_sent', 1)
            .update('last_mail_sent', new Date())
            .where('id', contact.id)

          await knex('sent_emails')
          .insert({
            template_id: company.select_option,
            marketing_contact_id:contact.id, 
            company_id: contact.company_id })
          
        })//.map END
    )//promise END
  }//if END
}//FUNCTION END

module.exports = {
  contactResultCompID
}