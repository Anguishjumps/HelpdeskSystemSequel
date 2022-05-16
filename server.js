const express = require('express')
const app = express()
const mysql = require('mysql')
const session = require('express-session')
const MySQLStore = require('express-mysql-session')(session);
const bcrypt = require("bcrypt");

app.use(express.static("public"))
app.use(express.urlencoded({ extended: true }))
app.use(express.json())

app.set('view engine', 'ejs')

var options = {
    host: "sci-project.lboro.ac.uk",
    user: "teamb029",
    password: "pXdBPQK4cL",
    database: "teamb029",
}

const sessionConnection = mysql.createConnection(options);
const sessionStore = new MySQLStore({
    expiration: 10800000,
    createDatabaseTable: true,
    schema:{
        tableName: 'session_tb',
        columnNames: {
            session_id: 'session_id',
            expires: 'expires',
            data: 'data'
        }
    }
},sessionConnection)

const comparePassword = async (password, hash) => {
    try {
        // Compare password
        return await bcrypt.compare(password, hash);
    } catch (error) {
        console.log(error);
    }

    // Return false if error
    return false;
};

app.use(session ({
    secret: "lostartofkeepingasecret",
    store: sessionStore,
    resave: false,
    saveUninitialized: true,
    cookie: {
        maxAge: 36000000,
        httpOnly: false,
        secure: false
      },
}))

app.get('/', (req, res) => {
    res.render("login")
})

app.post('/', (req, res) => {
    var username = req.body.username
    var password = req.body.password
    var con = mysql.createConnection(options);
    con.connect(function(err) {
        if (err) throw err;
        con.query("SELECT ID as userID, (SELECT deptName FROM DeptTable WHERE PersonnelTable.deptNo = DeptTable.ID) AS userType, passwordHash FROM `PersonnelTable` WHERE userName = '"+username+"';", function (err, result, fields) {
            try {
                if(err || result[0].passwordHash == null) {
                    res.render("login", {username: req.body.username})
                }
                else {
                    var _ = (async () => {
                        var validity =  await comparePassword(password, result[0].passwordHash)
                        if(!validity) {
                            res.render("login", {username: req.body.username})
                        }
                        else {
                            var userID = result[0].userID
                            var userType = result[0].userType
                            req.session.userID = userID
                            console.log("here")
                            console.log(userID)
                            console.log("ended")
                            console.log(req.session)
                            if(userType === "Specialist") {
                                const specRouter = require('./routes/specialist')
                                app.use('/specialist', specRouter)
                                res.redirect('/specialist/')
                            }
                            else {
                                const userRouter = require('./routes/user')
                                app.use('/user', userRouter)
                                res.redirect('/user/')
                            }                        
                        }
    
                    })();
                }
            } 
            catch {
                res.render("login", {username: req.body.username})
            }
        });
      });
})

app.post('/logout', (req, res) => {
    console.log(req.session.userID)
    req.session.destroy(function(err){
        if(!err) {
            res.redirect('/')
        }
    })
})

app.use((req, res, next) => {
    console.log(req.session);
    console.log(req.session.userID)
    next()
})



app.listen(5029)
