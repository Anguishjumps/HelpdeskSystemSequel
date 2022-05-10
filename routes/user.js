const express = require('express')
const router = express.Router()
let resultObject = { data: [] }
var mysql = require('mysql');

var pool = mysql.createPool({
    connectionLimit: 10,
    host: "sci-project.lboro.ac.uk",
    user: "teamb029",
    password: "pXdBPQK4cL",
    database: "teamb029",
    multipleStatements: true
});

router.get('/', (req, res) => {
    pool.getConnection(function (err, connection) {
        if (err) {
            return cb(err);
        }
        connection.query("SELECT DISTINCT TagTable.tagName FROM Ticket LEFT JOIN `TagTable` ON  Ticket.mainTag = TagTable.ID;", function (err, result) {
            connection.release()
            if (!err) {
                res.render('user/main', { data: result, point: "initialEntry" })
            } else {
                console.log(err)
            }
        });
    });
})

router.post('/initiatenewticket', (req, res) => {

    pool.getConnection(function (err, connection) {

        if (err) {
            return cb(err);
        }
        console.log("Initiating new ticket form...")

        //making connection for drop downs

        // S
        // SELECT * FROM `TagReleTable` ORDER BY `mainTagID` ASC
        //SELECT * FROM `TagTable`

        let mainTagQuery = "SELECT DISTINCT TagTable.tagName, TagTable.ID FROM Ticket LEFT JOIN `TagTable` ON  Ticket.mainTag = TagTable.ID;"
        
        connection.query(mainTagQuery, function (err, result, fields) {
            if (err) throw err;

            console.log("result: " + Object.values(result).map(el => console.log(el)))
        })
    })
    //res.render('user/main', { data: result, point: "newTicketEntry" })
    var mainTagObject = {
        1: {
            3: [8, "Images", "Tables", "Lists"],
            "4": ["9", "Margins", "Backgrounds", "Float"],
            "5": ["9", "Operators", "Functions", "Conditions"]
        },
        2: {
            "6": ["Variables", "Strings", "Arrays"],
            "7": ["SELECT", "UPDATE", "DELETE"]
        }
    }
    res.render('user/main', { mainTagObject: mainTagObject, point: "newTicketEntry" })
    //to capture search term if needed too
})


router.post('/searched', (req, res) => {
    pool.getConnection(function (err, connection) {

        if (err) {
            return cb(err);
        }
        let searchTerm = req.body.searchbar
        let myQuery = `SELECT ticketDescription, resolvedDescription FROM Ticket WHERE ticketState = "RESOLVED" AND ((ticketDescription  LIKE '%` + searchTerm.toLowerCase() + `%' OR resolvedDescription LIKE '%` + searchTerm.toLowerCase() + `%') OR (ticketDescription  LIKE '%` + searchTerm + `%' OR resolvedDescription LIKE '%` + searchTerm + `%'))`
        let resultObject

        connection.query(myQuery, function (err, result, fields) {
            connection.release()
            if (!err) {
                res.render('user/main', { data: result, point: "searchEntry" })
            } else {
                console.log(err)
            }
        });
    });
})

router.post('/maintag', (req, res) => {
    pool.getConnection(function (err, connection) {
        if (err) {
            return cb(err);
        }
        console.log("LOOK AT ME")
        let problemCategory = req.body.problemCategory
        let myQuery = `SELECT Ticket.ticketDescription, Ticket.resolvedDescription FROM TagTable INNER JOIN Ticket ON TagTable.ID = Ticket.mainTag WHERE tagName = "` + problemCategory + `" AND ticketState = "RESOLVED"`
        //order by date
        console.log("problem category: " + problemCategory)
        // con.connect(function(err) {
        //     if (err) throw err;
        connection.query(myQuery, function (err, result, fields) {
            connection.release()
            if (!err) {
                res.render('user/main', { data: result, point: "maintagEntry" })
            } else {
                console.log(err)
            }
            console.log("result: " + Object.values(result).map(el => console.log(el)))

        });
    });
})

router.post('/processNewTicket', (req, res) => {


    pool.getConnection(function (err, connection) {

        if (err) {
            return cb(err);
        }
        console.log(req.body)
        let userId = req.body.userId
        let problemDescription = req.body.problemDescription
        let mainTag = req.body.mainTag
        let secondaryTag = req.body.secondaryTag
        let tertiaryTag = req.body.tertiaryTag

        let myQuery = "INSERT INTO Ticket(mainTag, secondaryTag, tertiaryTag, userID, ticketDescription, ticketPriority, ticketState) VALUES(" + mainTag + ", " + secondaryTag + ", " + tertiaryTag + ", " + userId + ", '" + problemDescription + "', " + 3 + ", 'TODO')"

        connection.query(myQuery, function (err, result, fields) {
            connection.release()
            if (!err) {
                res.redirect('/user')
            } else {
                console.log(err)
            }
            // console.log("result: " + Object.values(result).map(el => console.log(el)))
        });
    });

})

router.post('/initiatenewticket', (req, res) => {
    pool.getConnection(function (err, connection) {
        if (err) {
            return cb(err);
        }
        console.log("Initiating new ticket form...")

        //making connection for drop downs

        var subjectObject = {
            1: {
                3: [8, "Images", "Tables", "Lists"],
                "4": ["9", "Margins", "Backgrounds", "Float"],
                "5": ["9", "Operators", "Functions", "Conditions"]
            },
            2: {
                "6": ["Variables", "Strings", "Arrays"],
                "7": ["SELECT", "UPDATE", "DELETE"]
            }
        }
        res.render('user/main', { data: subjectObject, point: "newTicketEntry" })
        //to capture search term if needed too

        let myQuery = "SELECT * FROM TagTable;"
        connection.query(myQuery, function (err, result, fields) {
            if (err) throw err;
            console.log("result: " + Object.values(result).map(el => console.log(el)))
            //res.render('user/main', { data: result, point: "newTicketEntry" })
        });
    });
})


router.post('/searched', (req, res) => {

    pool.getConnection(function (err, connection) {
        if (err) {
            return cb(err);
        }

        let searchTerm = req.body.searchbar
        let myQuery = `SELECT ticketDescription, resolvedDescription FROM Ticket WHERE ticketState = "RESOLVED" AND ((ticketDescription  LIKE '%` + searchTerm.toLowerCase() + `%' OR resolvedDescription LIKE '%` + searchTerm.toLowerCase() + `%') OR (ticketDescription  LIKE '%` + searchTerm + `%' OR resolvedDescription LIKE '%` + searchTerm + `%'))`
        let resultObject
        connection.query(myQuery, function (err, result, fields) {
            connection.release()
            if (!err) {
                res.render('user/main', { data: result, point: "searchEntry" })

            } else {
                console.log(err)
            }
        });
    });

})

router.get('/history', (req, res) => {
    pool.getConnection(function(err, connection) {
        if (err) {
            return cb(err);
        }
        connection.query("SELECT ID, Date, mainTag, secondaryTag, tertiaryTag, \
        ticketDescription, ticketPriority, solutionID, resolvedTimestamp, \
        ticketState, assignedSpecialistID, resolvedDescription FROM Ticket WHERE userID = 1;", function(err, result) {
            connection.release()
            if (!err) {
                result = JSON.stringify(result)
                result = JSON.parse(result)
                res.render("user/history", { tickets: result })
            } else {
                console.log(err)
            }

router.post('/maintag', (req, res) => {
    pool.getConnection(function (err, connection) {
        if (err) {
            return cb(err);
        }
        console.log("LOOK AT ME")
        let problemCategory = req.body.problemCategory
        let myQuery = `SELECT Ticket.ticketDescription, Ticket.resolvedDescription FROM TagTable INNER JOIN Ticket ON TagTable.ID = Ticket.mainTag WHERE tagName = "` + problemCategory + `" AND ticketState = "RESOLVED"`
        //order by date
        console.log("problem category: " + problemCategory)
        // con.connect(function(err) {
        //     if (err) throw err;
        connection.query(myQuery, function (err, result, fields) {
            connection.release()
            if (!err) {
                res.render('user/main', { data: result, point: "maintagEntry" })
            } else {
                console.log(err)
            }
            console.log("result: " + Object.values(result).map(el => console.log(el)))

        });
    });

})

router
    .get('/contact', (req, res) => {

        pool.getConnection(function (err, connection) {

            if (err) {
                return cb(err);
            }
            connection.query("SELECT PersonnelTable.fullName, TagTable.tagName, PersonnelTable.phoneNo \
         from Specialist join PersonnelTable on Specialist.specialistID = PersonnelTable.ID \
         join TagTable on Specialist.tagID=TagTable.ID;", (err, result) => {
                connection.release();

                if (!err) {
                    res.render('user/contact', { specialists: result })
                } else {
                    console.log(err)
                }
            });
        });
    });


router.get("/active-issues", (req, res) => {
    pool.getConnection(function(err, connection) {
        if (err) {
            return cb(err);
        }
        connection.query("SELECT ID, Date, (SELECT tagName FROM TagTable WHERE Ticket.mainTag = TagTable.ID) AS mainTag,\
         (SELECT tagName FROM TagTable WHERE Ticket.secondaryTag = TagTable.ID) AS secondaryTag,\
          (SELECT tagName FROM TagTable WHERE Ticket.tertiaryTag = TagTable.ID) AS tertiaryTag,\
           ticketDescription, ticketPriority, solutionID, resolvedTimestamp, ticketState,\
            assignedSpecialistID, resolvedDescription FROM Ticket WHERE userID = 1;", function (err, result) {
            connection.release()
            if (!err) {
                result = JSON.stringify(result)
                result = JSON.parse(result)
                res.render("user/active-issues", { tickets: result })
            } else {
                console.log(err)
            }
        });
    });
})

router.post("/active-issues", (req, res) => {
    res.redirect(`/user/active-issues/` + req.body.cardno)
})

router.get("/active-issues/:cardno", (req, res) => {
    res.render("user/card-details")
})

router.post('/processNewTicket', (req, res) => {
    pool.getConnection(function (err, connection) {
        if (err) {
            return cb(err);
        }
        console.log(req.body)
        let userId = req.body.userId
        let problemDescription = req.body.problemDescription
        let mainTag = req.body.mainTag
        let secondaryTag = req.body.secondaryTag
        let tertiaryTag = req.body.tertiaryTag

        let myQuery = "INSERT INTO Ticket(mainTag, secondaryTag, tertiaryTag, userID, ticketDescription, ticketPriority, ticketState) VALUES(" + mainTag + ", " + secondaryTag + ", " + tertiaryTag + ", " + userId + ", '" + problemDescription + "', " + 3 + ", 'TODO')"

        // connection.connect(function(err) {
        //     if (err) throw err;
        connection.query(myQuery, function (err, result, fields) {
            connection.release()
            if (!err) {
                res.render('user/main', { data: result })
            } else {
                console.log(err)
            }
            console.log("result: " + Object.values(result).map(el => console.log(el)))
            // res.render('user/main', { data: result, point: "maintagEntry" })
        });
    });
})

router.get('/new', (req, res) => {
    res.render("user/new")
})

router.get('/history', (req, res) => {
    pool.getConnection(function (err, connection) {
        if (err) {
            return cb(err);
        }
        connection.query("SELECT ID, Date, (SELECT tagName FROM TagTable WHERE Ticket.mainTag = TagTable.ID) AS mainTag,\
        (SELECT tagName FROM TagTable WHERE Ticket.secondaryTag = TagTable.ID) AS secondaryTag,\
        (SELECT tagName FROM TagTable WHERE Ticket.tertiaryTag = TagTable.ID) AS tertiaryTag,\
        ticketDescription, ticketPriority, solutionID, resolvedTimestamp, \
        ticketState, (SELECT fullName FROM PersonnelTable WHERE Ticket.assignedSpecialistID = PersonnelTable.ID) AS assignedSpecialistName, assignedSpecialistID, \
	resolvedDescription FROM Ticket WHERE userID = 1;", function (err, result) {
            connection.release()
            if (!err) {
                result = JSON.stringify(result)
                result = JSON.parse(result)
                res.render("user/history", { tickets: result })
            } else {
                console.log(err)
            }
        });
    });

})

router
    .get('/contact', (req, res) => {
        pool.getConnection(function (err, connection) {
            if (err) {
                return cb(err);
            }

            myQuery="SELECT PersonnelTable.fullName, TagTable.tagName, PersonnelTable.phoneNo \
                from Specialist join PersonnelTable on Specialist.specialistID = PersonnelTable.ID \
                join TagTable on Specialist.tagID=TagTable.ID;";

            if(req.body.sortBy=="Name"){
                myQuery="SELECT PersonnelTable.fullName, TagTable.tagName, PersonnelTable.phoneNo from Specialist \
                join PersonnelTable on Specialist.specialistID = PersonnelTable.ID join TagTable on Specialist.tagID=TagTable.ID\
                 ORDER by PersonnelTable.fullName";
            } else if(req.body.sortBy=="Specialism"){
                myQuery="SELECT PersonnelTable.fullName, TagTable.tagName, PersonnelTable.phoneNo from Specialist \
                join PersonnelTable on Specialist.specialistID = PersonnelTable.ID join TagTable on Specialist.tagID=TagTable.ID\
                 ORDER by TagTable.tagName";
            } else if(req.body.sortBy=="--Choose--"){
                myQuery="SELECT PersonnelTable.fullName, TagTable.tagName, PersonnelTable.phoneNo \
                from Specialist join PersonnelTable on Specialist.specialistID = PersonnelTable.ID \
                join TagTable on Specialist.tagID=TagTable.ID;";
            }
            connection.query(myQuery, function (err, result) {

                if (!err) {
                    res.render("user/contact", { specialists: result })
                } else {
                    console.log(err)
                }
            });
        });
    });

router.post('/getJson', (req, res) => {
    pool.getConnection(function (err, connection) {
        if (err) {
            return cb(err);
        }

        if(req.body.sortBy=="Name"){
            let myQueryS="SELECT PersonnelTable.fullName, TagTable.tagName, PersonnelTable.phoneNo from Specialist \
            join PersonnelTable on Specialist.specialistID = PersonnelTable.ID join TagTable on Specialist.tagID=TagTable.ID\
             ORDER by PersonnelTable.fullName";
        } else if(req.body.sortBy=="Specialism"){
            let myQueryS="SELECT PersonnelTable.fullName, TagTable.tagName, PersonnelTable.phoneNo from Specialist \
            join PersonnelTable on Specialist.specialistID = PersonnelTable.ID join TagTable on Specialist.tagID=TagTable.ID\
             ORDER by TagTable.tagName";
        }

        connection.query(myQuery, function (err, result) {

            if (!err) {
                res.render("user/contact", { specialists: result })
            } else {
                console.log(err)
            }
        });
        //let result=req.body.sortBy;
        //console.log(req.body.sortBy);

        
                //res.redirect("user/contact", { specialists: result })
                //res.redirect("/user/contact")
            
        });

    });

  



router.get("/active-issues", (req, res) => {
    pool.getConnection(function (err, connection) {
        if (err) {
            return cb(err);
        }
        connection.query("SELECT ID, Date, (SELECT tagName FROM TagTable WHERE Ticket.mainTag = TagTable.ID) AS mainTag,\
         (SELECT tagName FROM TagTable WHERE Ticket.secondaryTag = TagTable.ID) AS secondaryTag,\
          (SELECT tagName FROM TagTable WHERE Ticket.tertiaryTag = TagTable.ID) AS tertiaryTag,\
           ticketDescription, ticketPriority, solutionID, resolvedTimestamp, ticketState,\
            assignedSpecialistID, resolvedDescription FROM Ticket WHERE userID = 1;", function (err, result) {
            connection.release()
            if (!err) {
                result = JSON.stringify(result)
                result = JSON.parse(result)
                res.render("user/active-issues", { tickets: result })
            } else {
                console.log(err)
            }
        });
    });
})
router.post("/active-issues", (req, res) => {
    res.redirect(`/user/active-issues/` + req.body.cardno)
})



router.get("/active-issues", (req, res) => {
    pool.getConnection(function (err, connection) {
        if (err) {
            return cb(err);
        }
        connection.query("SELECT ID, Date, (SELECT tagName FROM TagTable WHERE Ticket.mainTag = TagTable.ID) AS mainTag,\
         (SELECT tagName FROM TagTable WHERE Ticket.secondaryTag = TagTable.ID) AS secondaryTag,\
          (SELECT tagName FROM TagTable WHERE Ticket.tertiaryTag = TagTable.ID) AS tertiaryTag,\
           ticketDescription, ticketPriority, solutionID, resolvedTimestamp, ticketState,\
            assignedSpecialistID, resolvedDescription FROM Ticket WHERE userID = 1;", function (err, result) {
            connection.release()
            if (!err) {
                result = JSON.stringify(result)
                result = JSON.parse(result)
                res.render("user/active-issues", { tickets: result })
            } else {
                console.log(err)
            }
        });
    });
})
router.post("/active-issues", (req, res) => {
    res.redirect(`/user/active-issues/` + req.body.cardno)
})

router.get("/active-issues/:cardno", (req, res) => {
    res.render("user/card-details")
})

module.exports = router


