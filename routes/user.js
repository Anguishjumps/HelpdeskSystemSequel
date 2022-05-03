const express = require('express')
const router = express.Router()
let resultObject = { data: [] }
var mysql = require('mysql');

var con = mysql.createConnection({
    host: "sci-project.lboro.ac.uk",
    user: "teamb029",
    password: "pXdBPQK4cL",
    database: "teamb029",
    multipleStatements: true
});
con.connect(function (err) {
    if (err) throw err;
})

router.use(logger)

router.get('/', (req, res) => {
    con.query("SELECT DISTINCT TagTable.tagName FROM Ticket LEFT JOIN `TagTable` ON  Ticket.mainTag = TagTable.ID;", function (err, result, fields) {
        if (err) throw err;
        res.render('user/main', { data: result, source: "initialEntry" })

    });

})

router.post('/initiatenewticket', (req, res) => {
    console.log("Initiating new ticket form...")

    //making connection for drop downs
    //to capture search term if needed too

    let myQuery = "SELECT * FROM TagTable;"
    con.query(myQuery, function (err, result, fields) {
        if (err) throw err;
        console.log("result: " + Object.values(result).map(el => console.log(el)))
        res.render('user/main', { data: result, source: "newTicketEntry" })
    });
});
// })

router.post('/searched', (req, res) => {
    let searchTerm = req.body.searchbar
    let myQuery = `SELECT ticketDescription, resolvedDescription FROM Ticket WHERE ticketState = "RESOLVED" AND ((ticketDescription  LIKE '%` + searchTerm.toLowerCase() + `%' OR resolvedDescription LIKE '%` + searchTerm.toLowerCase() + `%') OR (ticketDescription  LIKE '%` + searchTerm + `%' OR resolvedDescription LIKE '%` + searchTerm + `%'))`
    let resultObject
    con.query(myQuery, function (err, result, fields) {
        if (err) throw err;
        result.map(el => console.log(el))
        res.render('user/main', { data: result, source: "searchEntry" })
    });
})
router.post('/maintag', (req, res) => {
    console.log("LOOK AT ME")
    let problemCategory = req.body.problemCategory
    let myQuery = `SELECT Ticket.ticketDescription, Ticket.resolvedDescription FROM TagTable INNER JOIN Ticket ON TagTable.ID = Ticket.mainTag WHERE tagName = "` + problemCategory + `" AND ticketState = "RESOLVED"`
    //order by date
    console.log("problem category: " + problemCategory)
    // con.connect(function(err) {
    //     if (err) throw err;
    con.query(myQuery, function (err, result, fields) {
        if (err) throw err;
        console.log("result: " + Object.values(result).map(el => console.log(el)))
        res.render('user/main', { data: result, source: "maintagEntry" })
    });
    // });
})


router.post('/processNewTicket', (req, res) => {
    console.log(req.body)
    let userId = req.body.userId
    // WRITE THE REST
    let mainTagNumber = req.body.mainTagNumber
    console.log(mainTagNumber)

    // let myQuery = `SELECT Ticket.ticketDescription, Ticket.resolvedDescription FROM TagTable INNER JOIN Ticket ON TagTable.ID = Ticket.mainTag WHERE tagName = "`+problemCategory+`" AND ticketState = "RESOLVED"`
    // // con.connect(function(err) {
    // //     if (err) throw err;
    //     con.query(myQuery, function(err, result, fields) {
    //         if (err) throw err;
    //         console.log("result: " + Object.values(result).map(el=>console.log(el)))
    // res.render('user/main', { data: result, source: "maintagEntry" })
    // });
    // });
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
    res.render("user/active-issues")
})


router.post("/", (req, res) => {
    const isValid = true
    if (isValid) {
        users.push({ firstName: req.body.firstName })
        res.redirect(`/user/${users.length - 1}`)
    } else {
        console.log("Error")
        res.render("user/new", { firstName: req.body.firstName })
    }
})

router
    .route("/:id")
    .get((req, res) => {
        console.log(req.user)
        res.send(`Get User With ID ${req.params.id}`)
    })
    .put((req, res) => {
        res.send(`Update User With ID ${req.params.id}`)
    })
    .delete((req, res) => {
        res.send(`Delete User With ID ${req.params.id}`)
    })

const users = [{ name: "Kyle" }, { name: "Sally" }]
router.param("id", (req, res, next, id) => {
    req.user = users[id]
    next()
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