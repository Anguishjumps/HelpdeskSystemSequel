const express = require('express')
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
    res.render("user/active-issues")
})

router.post("/active-issues", (req, res) => {
    console.log(req.body.id)
    res.redirect(`/user/new/`)
    console.log("bungo")
  })

router.post("/", (req, res) => {
    const isValid = true
    if (isValid) {
        console.log("ayo")
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

