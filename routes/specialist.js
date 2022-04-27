const express = require('express')
const router = express.Router()
var mysql = require('mysql');

var con = mysql.createConnection({
    host: "localhost",
    user: "teamb029",
    password: "pXdBPQK4cL",
    database: "teamb029",
    multipleStatements: true
});


router
    .get('/', (req, res) => {
        con.connect(function(err) {
            if (err) throw err;
            con.query("SELECT * from Ticket;Select * from TagTable", function(err, result, fields) {
                if (err) throw err;
                res.render('specialist', { data: result })

            });
        });

    })
    .get('/analyst', (req, res) => {
        res.send("Specialist Analyst")
    })


module.exports = router