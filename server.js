const express = require('express')
const app = express()
const data = require('./database')

app.use(express.static("public"));
app.use(express.urlencoded({ extended: true }))
app.use(express.json())

app.set('view engine', 'ejs')

app.get('/', (req, res) => {

    console.log("Here")
    res.render("login", {text: 'Test Text'})

})

const userRouter = require('./routes/user')
app.use('/user', userRouter)

const specRouter = require('./routes/specialist')
app.use('/specialist', specRouter)

app.listen(3000)