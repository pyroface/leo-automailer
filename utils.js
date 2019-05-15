//tar in epostadressen från kontaktinformationen och kör .split på den
function emailSplitter (email) {
  const result = email.split("@")
  //efter .split så delar den upp strängen i en array så för att få ut rätt del
  //så måste man indexera korrekt. 
  //t.ex sven.larsson@email.com blir ['sven larsson', 'email.com']
  //i det här fallet så använder man [1]
  if (result[1].includes('gmail.com') || result[1].includes('hotmail.com')) {
      return null
  }
  return result[1]
}//function emailSplitter END


function moreThanOneWeekHasPassed (date) {
  //tar fram hur många dagar det har gått
  var diff = (new Date() - date) / (1000 * 60 * 60 * 24);
  //console.log('Dagar sen senaste mail ', Math.abs(Math.round(diff)))
  //om det har gått över 7 dagar
  return Math.abs(Math.round(diff)) >= 7;
}

module.exports = { emailSplitter, moreThanOneWeekHasPassed }