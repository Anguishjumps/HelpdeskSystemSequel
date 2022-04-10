// var mysql = require('mysql');
// var conn = mysql.createConnection({
//     host: "localhost",
//     user: "teamb029",
//     password: "pXdBPQK4cL",
//     database: "teamb029",
// });

// conn.connect(function(err) {
//     if (err) throw err;
//     console.log("Connected!");

//     connection.query('SELECT * FROM ticket', (err, rows) => {
//         connection.release()

//         if (!err) {
//             res.send(rows)
//         } else {
//             console.log(err)
//         }
//     })
// })
// module.exports = conn;

const express = require('express')
const app = express()
const data = require('./database')

app.use(express.static("public"))
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

app.listen(3306)

// npm run devStart