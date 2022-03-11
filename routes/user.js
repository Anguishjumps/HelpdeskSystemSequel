const express = require('express')
const router = express.Router()

router.get('/', (req, res) => {
    res.send("User Main")
})

router.get('/specialist-contact', (req, res) => {
    res.send("Specialist Contact")
})

router.get('/history', (req, res) => {
    res.send("User History")
})

module.exports = router
/*login
usermain
userspec
user hist

secialistmain
specialsit analyst
*/