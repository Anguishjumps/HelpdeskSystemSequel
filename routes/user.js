const express = require('express')
const router = express.Router()

router.use(logger)

router.get('/', (req, res) => {
    res.render("user/main")
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