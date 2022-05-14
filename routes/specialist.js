const express = require('express')
const router = express.Router()
var mysql = require('mysql');
const session = require('express-session')


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
                    res.render('specialist/main', { data: result, session: req.session.userID })
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
                // return cb(err);
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
                    res.render('specialist/main', { data: result, session: req.session.userID })
                } else {
                    console.log(err)
                }
            });
        });
    })


router
    .post('/updateTicket', (req, res) => {

        let newSolID = 0;
        pool.getConnection(function(err, connection) {
            if (err) {
                return cb(err);
            }
            connection.query("SELECT MAX(solutionID) AS NewSolID FROM Ticket;", (err, result) => {
                connection.release();
                // SELECT * FROM Ticket where ID =" + req.bodyID + ";"
                if (!err) {
                    newSolID = result[0].NewSolID + 1;
                } else {
                    console.log(err)
                }
            });
        });
        console.log(newSolID)


        pool.getConnection(function(err, connection) {
            if (err) {
                return cb(err);
            }
            if (req.body.finalSolutionID != 0 && req.body.finalSolutionID != -1) {
                connection.query("UPDATE Ticket SET ticketPriority =" + req.body.ticketPriority + ", ticketState = '" + req.body.ticketState +
                    "' , mainTag = " + req.body.typeID + ",ticketDescription = '" + req.body.ticketDescription + "' ,solutionID = " + req.body.finalSolutionID + " ,resolvedDescription = '" + req.body.solutionDescription +
                    "' WHERE ID =" + req.body.ID + ";", (err, result) => {
                        connection.release();

                        if (!err) { res.redirect(req.get('referer')); } else {
                            console.log(err)
                        }
                    });
            } else if (req.body.finalSolutionID == 0) {
                connection.query("UPDATE Ticket SET ticketPriority =" + req.body.ticketPriority + ", ticketState = '" + req.body.ticketState +
                    "' , mainTag = " + req.body.typeID + ",ticketDescription = '" + req.body.ticketDescription + "' ,solutionID = 0 ,resolvedDescription = NULL WHERE ID =" + req.body.ID + ";", (err, result) => {
                        connection.release();

                        if (!err) { res.redirect(req.get('referer')); } else {
                            console.log(err)
                        }
                    });
            } else {

                connection.query("UPDATE Ticket SET ticketPriority =" + req.body.ticketPriority + ", ticketState = '" + req.body.ticketState +
                    "' , mainTag = " + req.body.typeID + ",ticketDescription = '" + req.body.ticketDescription + "' ,solutionID = " + newSolID + " ,resolvedDescription = '" + req.body.solutionDescription +
                    "' WHERE ID =" + req.body.ID + ";", (err, result) => {
                        connection.release();

                        if (!err) { res.redirect(req.get('referer')); } else {
                            console.log(err)
                        }
                    });
            };
        });
    })


router
    .post('/setSpecialist', (req, res) => {

        pool.getConnection(function(err, connection) {
            if (err) {
                return cb(err);
            }
            connection.query("UPDATE Ticket SET assignedSpecialistID = " + req.session.userID + " WHERE ID =" + req.body.ID + ";", (err, result) => {
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
    .post('/unsetSpecialist', (req, res) => {

        pool.getConnection(function(err, connection) {
            if (err) {
                return cb(err);
            }
            connection.query("UPDATE Ticket SET assignedSpecialistID = NULL WHERE ID =" + req.body.ID + ";", (err, result) => {
                connection.release();

                if (!err) {
                    res.redirect(req.get('referer'));
                } else {
                    console.log(err)
                }
            });
        });
    })


module.exports = router