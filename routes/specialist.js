const express = require('express')
const data = require('../database')
const router = express.Router()


var problemType = [];
for (let i = 0; i < data.length; i++) {
    var problem_type = data[i].maintag;
    if (!problemType.includes(problem_type)) {
        problemType.push(problem_type);
    }
}
console.log(problemType);

router
    .get('/', (req, res) => {
        res.render('specialist', { data: { data: data, problemType: problemType } })
    })
    .get('/analyst', (req, res) => {
        res.send("Specialist Analyst")
    })

module.exports = router