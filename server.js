const express = require('express')
const app = express()
const data = require('./database')

app.use(express.static(__dirname + '/public'));
app.use(express.urlencoded({ extended: true }))

app.set('view engine', 'ejs')

app.get('/', (req, res) => {
    console.log(data)
    res.render("index", { text: 'Test 44' })

})

const userRouter = require('./routes/user')
app.use('/users', userRouter)

const specRouter = require('./routes/specialist')
app.use('/specialist', specRouter)

app.listen(3000)

// npm run devStart


// var sql = require("mssql");

// // config for your database
// var config = {
//     username: 'teamb029',
//     password: 'pXdBPQK4cL',
//     server: 'localhost',
//     database: 'teamb029'
// };

// // connect to your database
// sql.connect(config, function(err) {

//     if (err) console.log(err);

//     // create Request object
//     var request = new sql.Request();

//     // query to the database and get the records
//     request.query('select * from Ticket', function(err, recordset) {

//         if (err) console.log(err)

//         // send records as a response
//         res.send(recordset);

//     });
// });