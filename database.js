var mysql = require('mysql');

var conn = mysql.createConnection({
    host: "localhost",
    user: "teamb029",
    password: "pXdBPQK4cL",
    database: "teamb029",


});

conn.connect(function(err) {
    if (err) throw err;
    console.log("Connected!");

    connection.query('SELECT * FROM ticket', (err, rows) => {
        connection.release()

        if (!err) {
            res.send(rows)
        } else {
            console.log(err)
        }
    })
})
module.exports = conn;

// const data = [{
//         id: 1,
//         date: "02/05/2020",
//         maintag: 3,
//         secondarytag: 1,
//         tertiarytag: 17,
//         userid: 43,
//         ticketdescription: "test",
//         ticketpriority: 2,
//         solutionid: 1,
//         resolvedtime: "03/03/2020",
//         ticketstate: "TODO",
//         assignedspecialist: 24,
//         resolveddescription: "test12"
//     },
//     {
//         id: 2,
//         date: "03/06/2020",
//         maintag: 4,
//         secondarytag: 2,
//         tertiarytag: 18,
//         userid: 44,
//         ticketdescription: "test002",
//         ticketpriority: 3,
//         solutionid: 2,
//         resolvedtime: "04/04/2020",
//         ticketstate: "ACTIVE",
//         assignedspecialist: 25,
//         resolveddescription: "test13"
//     },
//     {
//         id: 3,
//         date: "04/07/2020",
//         maintag: 5,
//         secondarytag: 3,
//         tertiarytag: 19,
//         userid: 45,
//         ticketdescription: "test003",
//         ticketpriority: 4,
//         solutionid: 3,
//         resolvedtime: "05/05/2020",
//         ticketstate: "RESOLVED",
//         assignedspecialist: 26,
//         resolveddescription: "test14"
//     }
// ]

// module.exports = data;