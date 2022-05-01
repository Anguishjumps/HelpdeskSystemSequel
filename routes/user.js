const express = require('express')
const { route } = require('express/lib/application')
const router = express.Router()
let resultObject = {result:[]}

router.use(logger)

router.get('/', (req, res) => {
    res.render("user/main", resultObject)
})

router.post('/searched', (req, res) => {
// doing a db search on inputted text object
    //grab search term
    let searchTerm = req.body.searchbar
    console.log("search term: "+ searchTerm)
    //init db query with search term
    //pretend we got a db, make results object
    //list results in charlotte's js in main or partial
    //get results and make a results object
    let resultObject = {result:[{issue: "printer", resolution: "restart"}, {issue: "computer", resolution: "off and on again"}]}
    res.render("user/main", resultObject) // FIX
})

router.get('/new', (req, res) => {
    res.render("user/new")
})

router.get('/history', (req, res) => {
    res.render("user/history")
})

router.get("/contact", (req, res) => {
    res.render("user/contact")
})

router.get("/active-issues", (req, res) => {
    con.connect(function(err) {
        if (err) throw err;
        con.query("SELECT ID, Date, mainTag, secondaryTag, tertiaryTag, \
        ticketDescription, ticketPriority, solutionID, resolvedTimestamp, \
        ticketState, assignedSpecialistID, resolvedDescription FROM Ticket WHERE userID = 1;", function(err, result, fields) {
            if (err) throw err;
            result = JSON.stringify(result)
            result = JSON.parse(result)    
            res.render("user/active-issues", { tickets: result })

        });
    });    
})

router.post("/active-issues", (req, res) => {
    res.redirect(`/user/active-issues/`+ req.body.cardno)
  })

router.get("/active-issues/:cardno", (req, res) => {
    res.render("user/card-details")
})

function logger(req, res, next) {
    console.log(req.originalUrl)
    next()
}


module.exports = router

/*login
usermain
userspec
user hist

secialistmain
specialsit analyst
*/

