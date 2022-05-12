const express = require('express')
const app = express();
// const bcrypt = require('bcrypt');

var mysql = require('mysql');


var pool = mysql.createPool({
    connectionLimit: 10,
    host: "localhost",
    user: "teamb029",
    password: "pXdBPQK4cL",
    database: "teamb029",
    multipleStatements: true
});

app.use(express.static("public"));
app.use(express.urlencoded({ extended: true }))
app.use(express.json())

app.set('view engine', 'ejs')

app.get('/', (req, res) => {

    console.log("Here")
    res.render("login", { text: 'Test Text' })

})


app.post('/loginCheck', (req, res) => {
            const hashPassword = async(password, saltRounds = 10) => {
                try {
                    // Generate a salt
                    const salt = await bcrypt.genSalt(saltRounds);

                    // Hash password
                    return await bcrypt.hash(password, salt);
                } catch (error) {
                    console.log(error);
                }

                // Return null if error
                return null;
            };


            (async() => {
                    const hash = await hashPassword(req.body.password);
                    // $2b$10$5ysgXZUJi7MkJWhEhFcZTObGe18G1G.0rnXkewEtXq6ebVx1qpjYW

                    pool.getConnection(function(err, connection) {
                        if (err) {
                            return cb(err);
                        }
                        connection.query("UPDATE personnelTable SET passwordHash = " + hash + " WHERE ID =" + req.body.ID + ";", (err, hash) => {
                            connection.release();
                            // TODO: store hash in a database
                        })();

                        (async() => {
                            pool.getConnection(function(err, connection) {
                                if (err) {
                                    return cb(err);
                                }
                                connection.query("SELECT passwordHash from personnelTable;", (err, hash) => {
                                    connection.release();


                                    // Check if password is correct
                                    const isValidPass = await comparePassword(req.body.password, hash);

                                    // Print validation status
                                    console.log(`Password is ${!isValidPass ? 'not' : ''} valid!`);
                                    // => Password is valid!    
                                });
                            });


                        })();

                    })

                    const userRouter = require('./routes/user')
                    app.use('/user', userRouter)

                    const specRouter = require('./routes/specialist')
                    app.use('/specialist', specRouter)


                    app.listen(5029)