const express = require('express')
const router = express.Router()
let resultObject = {data:[]}
var mysql = require('mysql');

var con = mysql.createConnection({
    host: "sci-project.lboro.ac.uk",
    user: "teamb029",
    password: "pXdBPQK4cL",
    database: "teamb029",
    multipleStatements: true
});

router.use(logger)

router.get('/', (req, res) => {
        con.connect(function(err) {
            if (err) throw err;
            con.query("SELECT DISTINCT TagTable.tagName FROM Ticket LEFT JOIN `TagTable` ON  Ticket.mainTag = TagTable.ID;", function(err, result, fields) {
                if (err) throw err;
                res.render('user/main', { data: result, source: "initialEntry" })

            });
        });

})

router.post('/searched', (req, res) => {
// doing a db search on inputted text object
    //grab search term
    let searchTerm = req.body.searchbar
    let resultObject
    if (searchTerm == "printer" || "printer jam") {
        console.log("search term: "+ searchTerm)
        //init db query with search term
    
        //pretend we got a db, make results object
        //list results in charlotte's js in main or partial
        //get results and make a results object
        resultObject = {data:[{issue: "printer", resolution: "restart"}, {issue: "computer", resolution: "off and on again"}]}
    }
    if (searchTerm == "bottle") {
        console.log("search term: "+ searchTerm)
        resultObject = {data:[{issue: "broken bottle", resolution: "kick"}, {issue: "hello", resolution: "goodbye"}]}
    }
    res.render("user/main", {resultObject,  source: "searchEntry"} ) // FIX
})

router.post('/maintag', (req, res) => {
    console.log("LOOK AT ME")
        let problemCategory = req.body.problemCategory
        let myQuery = `SELECT Ticket.ticketDescription, Ticket.resolvedDescription FROM TagTable INNER JOIN Ticket ON TagTable.ID = Ticket.mainTag WHERE tagName = "`+problemCategory+`" AND ticketState = "RESOLVED"`
        console.log("problem category: " + problemCategory)
        // con.connect(function(err) {
        //     if (err) throw err;
            con.query(myQuery, function(err, result, fields) {
                if (err) throw err;
                console.log("result: " + Object.values(result).map(el=>console.log(el)))
                res.render('user/main', { data: result, source: "maintagEntry" })
            });
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