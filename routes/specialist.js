const express = require('express')
const router = express.Router()
var mysql = require('mysql');


var pool = mysql.createPool({
    connectionLimit: 10,
    host: "localhost",
    user: "teamb029",
    password: "pXdBPQK4cL",
    database: "teamb029",
    multipleStatements: true
});


router
    .get('/', (req, res) => {
        pool.getConnection(function(err, connection) {
            if (err) {
                return cb(err);
            }
            connection.query("SELECT * from Ticket;Select * from TagTable;", (err, result) => {
                connection.release();

                if (!err) {
                    res.render('specialist', { data: result })
                } else {
                    console.log(err)
                }
            });
        });

    })
    .get('/analyst', (req, res) => {
        res.send("Specialist Analyst")
    })


router
    .post('/getDetails', (req, res) => {

        pool.getConnection(function(err, connection) {
            if (err) {
                return cb(err);
            }
            connection.query("SELECT * from Ticket;", (err, result) => {
                connection.release();

                if (!err) { res.redirect(req.get('referer')); } else {
                    console.log(err)
                }
            });
        });
    })

router
    .post('/updateTicketState', (req, res) => {

        pool.getConnection(function(err, connection) {
            if (err) {
                return cb(err);
            }
            connection.query("UPDATE Ticket SET ticketState = '" + req.body.TicketState + "' WHERE ID =" + req.body.ID + ";", (err, result) => {
                connection.release();

                if (!err) {
                    res.redirect(req.get('referer'));
                } else {
                    console.log(err)
                }
            });
        });
    })


router
    .post('/setTicketResolvedDate', (req, res) => {

        pool.getConnection(function(err, connection) {
            if (err) {
                return cb(err);
            }
            connection.query("UPDATE Ticket SET resolvedTimestamp = CURRENT_TIMESTAMP WHERE ID =" + req.body.ID + ";", (err, result) => {
                connection.release();

                if (!err) {
                    res.redirect(req.get('referer'));
                } else {
                    console.log(err)
                }
            });
        });
    })


router
    .post('/removeTicketResolvedDate', (req, res) => {

        pool.getConnection(function(err, connection) {
            if (err) {
                return cb(err);
            }
            connection.query("UPDATE Ticket SET resolvedTimestamp = NULL WHERE ID =" + req.body.ID + ";", (err, result) => {
                connection.release();

                if (!err) {
                    res.redirect(req.get('referer'));
                } else {
                    console.log(err)
                }
            });
        });
    })

router
    .post('/updateTicketType', (req, res) => {

        pool.getConnection(function(err, connection) {
            if (err) {
                return cb(err);
            }
            connection.query("UPDATE Ticket SET mainTag = " + req.body.Maintag + " WHERE ID =" + req.body.ID + ";", (err, result) => {
                connection.release();

                if (!err) {
                    res.redirect(req.get('referer'));
                } else {
                    console.log(err)
                }
            });
        });
    })

router
    .post('/show', (req, res) => {

        pool.getConnection(function(err, connection) {
            if (err) {
                return cb(err);
            }
            connection.query("SELECT * FROM Ticket;Select * from TagTable;", (err, result) => {
                connection.release();
                // SELECT * FROM Ticket where ID =" + req.bodyID + ";"
                if (!err) {
                    res.render('specialist', { data: result })
                } else {
                    console.log(err)
                }
            });
        });
    })

module.exports = router