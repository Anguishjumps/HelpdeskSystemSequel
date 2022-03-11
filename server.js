const express = require('express')
const app = express()

app.use(express.static("public"))
app.use(express.urlencoded({ extended: true }))

app.set('view engine', 'ejs')

app.get('/', (req, res) => {
    console.log("Here")
    res.render("index", {text: 'Test Text'})
})

const userRouter = require('./routes/user')
app.use('/users', userRouter)

const specRouter = require('./routes/specialist')
app.use('/specialist', specRouter)

app.listen(3000)