var fetch = require('isomorphic-fetch')
//Fetchar Arbetsförmedlingens API
function AFApi (url) {
  return fetch('http://api.arbetsformedlingen.se/af/v0' + url,
  {
      headers: {
          Accept: "application/json",
          'Accept-Language': "sv"
          //måste göra så att den tar emot datan på svenska
      }
  }).then(res => res.json())
}//AFApi function END

module.exports = AFApi














