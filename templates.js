

function createEmailMessage(tokenString, template){
  console.log({tokenString, template})
  const templates = [
    // för att göra nya templates. Göra bara ett nytt index i arrayen
    // och börja knacka html kod. Kom ihåg att updatera i controllers index
    `<html>
      DEFAULT TEMPLATE IIIIIIIIIIIIIII
      <button>
        <a href="http://localhost:8888/accept.php?token=${tokenString}">accept</a>
      </button>
      <button>
        <a href="http://localhost:8888/decline.php?token=${tokenString}">decline</a>
      </button>
    </html>`,

    `<html>
      TEAMTAILOR XXXXXXXXXXXXXXX
      <button>
        <a href="http://localhost:8888/accept.php?token=${tokenString}">accept</a>
      </button>
      <button>
        <a href="http://localhost:8888/decline.php?token=${tokenString}">decline</a>
      </button>
    </html>`,

    `<html>
      REACHME 00000000000000
      <button>
        <a href="http://localhost:8888/accept.php?token=${tokenString}">accept</a>
      </button>
      <button>
        <a href="http://localhost:8888/decline.php?token=${tokenString}">decline</a>
      </button>
    </html>`
  ]
  return templates[template -1]
}

module.exports = {
  createEmailMessage
}