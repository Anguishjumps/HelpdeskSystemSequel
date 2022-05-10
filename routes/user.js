const express = require('express')
const router = express.Router()

router.use(logger)

router.get('/', (req, res) => {
    res.render("user/main")
})

router.get('/contact', (req, res) => {
    res.render("user/contact")
})

router.get('/history', (req, res) => {
    res.render("user/history")
})

router.get("/new", (req, res) => {
    res.render("user/new")
  })
  
router.post("/", (req, res) => {
    const isValid = true
    if (isValid) {
        users.push({ firstName: req.body.firstName })
        res.redirect(`/user/${users.length - 1}`)
    } else {
        console.log("Error")
        res.render("user/new", { firstName: req.body.firstName })
    }
})

router
.route("/:id")
    .get((req, res) => {
        console.log(req.user)
        res.send(`Get User With ID ${req.params.id}`)
    })
    .put((req, res) => {
        res.send(`Update User With ID ${req.params.id}`)
    })
    .delete((req, res) => {
        res.send(`Delete User With ID ${req.params.id}`)
})

const users = [{ name: "Kyle" }, { name: "Sally" }]
    router.param("id", (req, res, next, id) => {
    req.user = users[id]
    next()
})

function logger(req, res, next) {
    console.log(req.originalUrl)
    next()
}

module.exports = router


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

  
/*login
usermain
userspec
user hist

secialistmain
specialsit analyst
*/
