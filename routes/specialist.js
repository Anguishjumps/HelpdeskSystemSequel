const express = require('express')
const router = express.Router()

router
    .get('/', (req, res) => {
        res.send("Specialist Main")
    })
    .get('/analyst', (req, res) => {
        res.send("Specialist Analyst")
    })  

module.exports = router