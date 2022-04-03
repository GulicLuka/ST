const addPersonBtn = document.getElementById("addPersonBtn");
const addNewPersonDiv = document.getElementById("add-new-person");
const shade = document.getElementById("shade");
const dodajBtn = document.getElementById("dodaj");
const cancelBtn = document.getElementById("cancel");

const vseBtn = document.getElementById("vsiBtn");
const priljubljeniBtn = document.getElementById("priljubljeniBtn");
const sortBtn = document.getElementById("abcBtn");

const searchBar = document.getElementById("searchBar");
const searchBtn = document.getElementById("searchButton");

const paragrafWarning = document.getElementById("userWarning");

var arrPeople = new Array();
var personID = 0;

function toggle_addPerson_display(){
    if(addNewPersonDiv.style.display == "flex" && shade.style.display == "block"){
        shade.style.display = "none";
        addNewPersonDiv.style.display = "none";
    } else{
        shade.style.display = "block";
        addNewPersonDiv.style.display = "flex";
    }
}

function loadDataFromLocalStorage() {
    if(localStorage.getItem("people") != "") {
        arrPeople = JSON.parse(localStorage.getItem("people"));
    }

    let maxID = 0;
    if(arrPeople != null){
        for(let person of arrPeople){
            if(person.ime != "" && (person.mail != "" || person.telst != "")){
                DOMaddPerson(person);
                if(person.id > maxID){
                    maxID = person.id;
                }
            }
        }
        personID = maxID + 1;
    }
 
    if(arrPeople == null){
        const person = {
            id: personID,
            ime: "",
            priimek: "",
            telst: "",
            mail: "",
            liked: false
        };
        arrPeople = [person];
    }
}

function addPerson() {
    const ime = document.getElementById("ime").value;
    const priimek = document.getElementById("priimek").value;
    const telst = document.getElementById("telst").value;
    const mail = document.getElementById("mail").value;
    if(ime.trim() != "" && priimek.trim() != "" && telst.trim() != "" && mail.trim() != "" && mail.includes("@") && /^(?=.*\d)[\d ]+$/.test(telst.trim()) && /^[0-9]{3}[-\s\.]{0,1}[0-9]{3}[-\s\.]{0,1}[0-9]{3}$/.test(telst.trim())) {
        let dodajBool = true;
        for(let contact of arrPeople){
            if (contact.ime == ime.trim() && contact.priimek == priimek.trim()){
                dodajBool = false;
                break;
            }
        }
        if(dodajBool){
            const person = {
                id: personID,
                ime: ime,
                priimek: priimek,
                telst: telst,
                mail: mail,
                liked: false
            };
    
            arrPeople.push(person);
            DOMaddPerson(person)
            personID++;
            let parsedPeople = JSON.stringify(arrPeople);
            localStorage.setItem("people", parsedPeople);
            paragrafWarning.style.visibility = "hidden";
            document.getElementById("ime").value = "";
            document.getElementById("priimek").value = "";
            document.getElementById("telst").value = "";
            document.getElementById("mail").value = "";
            toggle_addPerson_display();
        } else {
            paragrafWarning.style.visibility = "visible";
            paragrafWarning.innerText = "Uporabnik že obstaja!";
            document.getElementById("ime").value = "";
            document.getElementById("priimek").value = "";
        }

    } else {
        arrWarnings = [];

        if(ime.trim() != ""){
            document.getElementById("ime").value = ime.trim();
        } else {
            document.getElementById("ime").value = "";
            arrWarnings.push("polje ime prazno");
        }
        if(priimek.trim() != ""){
            document.getElementById("priimek").value = priimek.trim();
        } else {
            document.getElementById("priimek").value = "";
            arrWarnings.push("polje priimek prazno");
        }
        if(telst.trim() != "" && /^(?=.*\d)[\d ]+$/.test(telst.trim()) && /^[0-9]{3}[-\s\.]{0,1}[0-9]{3}[-\s\.]{0,1}[0-9]{3}$/.test(telst.trim())){
            document.getElementById("telst").value = telst.trim();
        } else {
            if(telst.trim() == ""){
                arrWarnings.push("Polje telefonska številka prazno");
            } else if (!/^(?=.*\d)[\d ]+$/.test(telst.trim())){
                arrWarnings.push("Napačen vnos v polje telefonska številka");
            } else {
                arrWarnings.push("Napačen format telefonske številke");
            }
            document.getElementById("telst").value = "";
        }
        if(mail.trim() != "" && mail.includes("@")){
            document.getElementById("mail").value = mail.trim();
        } else {
            if(mail.trim() == ""){
                arrWarnings.push("Polje e-pošta prazno");
            } else {
                arrWarnings.push("Napačen vnos v polje e-pošta");
            }
            document.getElementById("mail").value = "";
        }

        let warningMessage = "";
        for(let warning of arrWarnings){
            warningMessage += warning + "\n";
        }
        paragrafWarning.style.visibility = "visible";
        paragrafWarning.innerText = warningMessage;
    }
}

function DOMaddPerson(person) {
    const contactsContainer = document.getElementById("contacts-container");

    //glavni div
    const personDiv = document.createElement("div");
    personDiv.id = person.id;
    personDiv.className = "contact";
    contactsContainer.appendChild(personDiv);

    //main div
    const mainDiv = document.createElement("div");
    mainDiv.className= "mainDiv";
    personDiv.appendChild(mainDiv);

    //div z vsebino ime, priimek
    const contactContent = document.createElement("div");
    contactContent.className = "contactContent";
    mainDiv.appendChild(contactContent);

    const mainImePriimek = document.createElement("span");
    mainImePriimek.innerHTML = `${person["ime"]} ${person["priimek"]}`;
    mainImePriimek.id = `span${person["id"]}`;
    contactContent.appendChild(mainImePriimek);

    //input za spreminjanje imena in priimka
    const imeInput = document.createElement("input");
    imeInput.type = "text";
    imeInput.value = person["ime"];
    imeInput.id = `imeInput${person["id"]}`;
    imeInput.style.display = "none";

    const priimekInput = document.createElement("input");
    priimekInput.type = "text";
    priimekInput.value = person["priimek"];
    priimekInput.id = `priimekInput${person["id"]}`;
    priimekInput.style.display = "none";

    contactContent.appendChild(imeInput);
    contactContent.appendChild(priimekInput);

    //div z remove, edit, like
    const contactButtons = document.createElement("div");
    contactButtons.className = "buttons";
    mainDiv.appendChild(contactButtons);

    const removeContactBtn = document.createElement("button");
    removeContactBtn.id = person.id;
    removeContactBtn.className = "removeContactBtn";

    const likeContactBtn = document.createElement("button");
    likeContactBtn.id = person.id;
    likeContactBtn.className = "likeContactBtn";
    if(person.liked){
        likeContactBtn.style.color = "#e8fc0d";
    }
    else {
        likeContactBtn.style.color = "#dbdee7";
    }

    const editContactBtn = document.createElement("button");
    editContactBtn.id = person.id;
    editContactBtn.className = "editContactBtn";

    removeContactBtn.onclick = DOMremovePerson;
    likeContactBtn.onclick = likeContact;
    editContactBtn.onclick = editContact;

    contactButtons.appendChild(editContactBtn);
    contactButtons.appendChild(likeContactBtn);
    contactButtons.appendChild(removeContactBtn);
    
    //info div
    const infoDiv = document.createElement("div");
    infoDiv.className = "infoDiv";
    personDiv.appendChild(infoDiv);

    const div1 = document.createElement("div");
    div1.style.display = "flex";
    div1.style.flexDirection = "row";
    div1.id = `div1_${person.id}`;

    const div2 = document.createElement("div");
    div2.style.display = "flex";
    div2.style.flexDirection = "row";
    div2.id = `div2_${person.id}`;

    const telspan = document.createElement("span");
    telspan.innerHTML = "Telefonska številka:&nbsp;";
    telspan.className = "hedaerSpan";

    const mailspan = document.createElement("span");
    mailspan.innerHTML = "E-pošta:&nbsp;";
    mailspan.className = "hedaerSpan";

    const tel = document.createElement("span");
    tel.innerHTML = person["telst"];
    tel.id = `telspan${person.id}`;

    const mail = document.createElement("span");
    mail.innerHTML = person["mail"];
    mail.id = `mailspan${person.id}`;

    //info div input
    const telInput = document.createElement("input");
    telInput.type = "tel";
    telInput.value = person["telst"];
    telInput.id = `telInput${person["id"]}`;
    telInput.style.display = "none";
    telInput.pattern = "([0-9]{3} [0-9]{3} [0-9]{3})|([0-9]{2} [0-9]{3} [0-9]{2} [0-9]{2})";

    const mailInput = document.createElement("input");
    mailInput.type = "mail";
    mailInput.value = person["mail"];
    mailInput.id = `mailInput${person["id"]}`;
    mailInput.style.display = "none";

    div1.appendChild(telspan);
    div1.appendChild(telInput);
    div1.appendChild(tel);

    div2.appendChild(mailspan);
    div2.appendChild(mailInput);
    div2.appendChild(mail);

    //info div buttons
    const saveBtn = document.createElement("button");
    saveBtn.innerText = "Shrani spremembe";
    saveBtn.className = "saveBtn";
    saveBtn.id = `saveBtn${person.id}`;
    saveBtn.style.display = "none";
    saveBtn.style.marginLeft = "0.5em";

    const cancelBtn = document.createElement("button");
    cancelBtn.innerText = "X";
    cancelBtn.className = "cancelBtn";
    cancelBtn.id = `cancelBtn${person.id}`;
    cancelBtn.style.display = "none";
    
    const buttonsContent = document.createElement("div");
    cancelBtn.style.display = "none";
    buttonsContent.className = "buttonsContent";

    saveBtn.onclick = saveChanges;
    cancelBtn.onclick = dontSaveChanges;

    infoDiv.appendChild(div1);
    infoDiv.appendChild(div2);
    buttonsContent.appendChild(saveBtn);
    buttonsContent.appendChild(cancelBtn);
    infoDiv.appendChild(buttonsContent);
}

function dontSaveChanges() {
    let geteditID = this.id;
    let editID = geteditID.substring("cancelBtn".length);
    const imeInput = document.getElementById(`imeInput${editID}`);
    const priimekInput = document.getElementById(`priimekInput${editID}`);
    const span = document.getElementById(`span${editID}`);

    const saveBtn = document.getElementById(`saveBtn${editID}`);
    const cancelBtn = document.getElementById(`cancelBtn${editID}`);

    const telInput = document.getElementById(`telInput${editID}`);
    const mailInput = document.getElementById(`mailInput${editID}`);

    const telspan = document.getElementById(`telspan${editID}`);
    const mailspan = document.getElementById(`mailspan${editID}`);

    telInput.style.display = "none";
    mailInput.style.display = "none";

    saveBtn.style.display = "none";
    cancelBtn.style.display = "none";

    span.style.display = "inline-block";
    imeInput.style.display = "none";
    priimekInput.style.display = "none";

    telspan.style.display = "inline-block";
    mailspan.style.display = "inline-block";
}

function saveChanges() {
    let geteditID = this.id;
    let editID = geteditID.substring("saveBtn".length);
    let selectedID;
    for(let i=0; i < arrPeople.length; i++){
        if(editID == arrPeople[i].id){
            selectedID = i;
            break;
        }
    }

    const imeInput = document.getElementById(`imeInput${editID}`);
    const priimekInput = document.getElementById(`priimekInput${editID}`);
    const span = document.getElementById(`span${editID}`);

    const saveBtn = document.getElementById(`saveBtn${editID}`);
    const cancelBtn = document.getElementById(`cancelBtn${editID}`);

    const telInput = document.getElementById(`telInput${editID}`);
    const mailInput = document.getElementById(`mailInput${editID}`);

    const telspan = document.getElementById(`telspan${editID}`);
    const mailspan = document.getElementById(`mailspan${editID}`);

    if(selectedID != null){
        if(imeInput.value.trim() != ""){
            arrPeople[selectedID].ime = imeInput.value.trim();
        }
        if(priimekInput.value.trim() != "") {
            arrPeople[selectedID].priimek = priimekInput.value.trim();
        }
        if(telInput.value.trim() != "" && /^(?=.*\d)[\d ]+$/.test(telInput.value.trim()) && /^[0-9]{3}[-\s\.]{0,1}[0-9]{3}[-\s\.]{0,1}[0-9]{3}$/.test(telst.trim())){
            arrPeople[selectedID].telst = telInput.value;
        }
        if(mailInput.value.trim() != "" && mailInput.value.includes("@")){
            arrPeople[selectedID].mail = mailInput.value;
        }
        span.innerHTML = `${arrPeople[selectedID].ime} ${arrPeople[selectedID].priimek}`;

        telspan.innerHTML = `${arrPeople[selectedID].telst}`;
        mailspan.innerHTML = `${arrPeople[selectedID].mail}`;

        span.style.display = "inline-block";
        imeInput.style.display = "none";
        priimekInput.style.display = "none";
        saveBtn.style.display = "none";
        cancelBtn.style.display = "none";
        mailInput.style.display = "none";
        telInput.style.display = "none";
        telspan.style.display = "inline-block";
        mailspan.style.display = "inline-block";
    }
    let parsedPeople = JSON.stringify(arrPeople);
    localStorage.setItem("people", parsedPeople);
}

function editContact() {
    let editID = this.id;
    let changeI;
    for(let i=0; i < arrPeople.length; i++){
        if(editID == arrPeople[i].id){
            changeI = i;
            break;
        }
    }

    const imeInput = document.getElementById(`imeInput${editID}`);
    const priimekInput = document.getElementById(`priimekInput${editID}`);
    const span = document.getElementById(`span${editID}`);

    const telInput = document.getElementById(`telInput${editID}`);
    const mailInput = document.getElementById(`mailInput${editID}`);

    const telspan = document.getElementById(`telspan${editID}`);
    const mailspan = document.getElementById(`mailspan${editID}`);

    imeInput.value = arrPeople[changeI]["ime"];
    priimekInput.value = arrPeople[changeI]["priimek"];

    telInput.value = arrPeople[changeI]["telst"];
    mailInput.value = arrPeople[changeI]["mail"];

    const saveBtn = document.getElementById(`saveBtn${editID}`);
    const cancelBtn = document.getElementById(`cancelBtn${editID}`);

    saveBtn.style.display = "inline-block";
    cancelBtn.style.display = "inline-block";

    imeInput.style.display = "inline-block";
    priimekInput.style.display = "inline-block";
    span.style.display = "none";

    telInput.style.display = "inline-block";
    mailInput.style.display = "inline-block";

    telspan.style.display = "none";
    mailspan.style.display = "none";
}

function likeContact() {
    for(let i=0; i < arrPeople.length; i++){
        if(this.id == arrPeople[i].id){
            arrPeople[i].liked = !arrPeople[i].liked;
            if(arrPeople[i].liked){
                this.style.color = "#e8fc0d";
                this.addEventListener("mouseover", () => {
                    this.style.color = "#c1d10d"
                });
        
                this.addEventListener("mouseout", () => {
                    this.style.color = "#e8fc0d"
                });
            }
            else {
                this.style.color = "#dbdee7";
                this.addEventListener("mouseover", () => {
                    this.style.color = "#a8abb3"
                });
        
                this.addEventListener("mouseout", () => {
                    this.style.color = "#dbdee7"
                });
            }
        }
    }
    let parsedPeople = JSON.stringify(arrPeople);
    localStorage.setItem("people", parsedPeople);
}

function DOMremovePerson(event) {
    let deleteID = this.id;
    for(let i=0; i < arrPeople.length; i++){
        if(arrPeople[i].id == deleteID){
            arrPeople.splice(i,1);
        }
    }
    document.getElementById(`${this.id}`).remove();
    let parsedPeople = JSON.stringify(arrPeople);
    localStorage.setItem("people", parsedPeople);
}

function prikaziVse() {
    const parent = document.getElementById("contacts-container");
    while(parent.firstChild) {
        parent.firstChild.remove();
    }
    loadDataFromLocalStorage();
}

function prikaziPrilljubljne() {
    const parent = document.getElementById("contacts-container");
    while(parent.firstChild) {
        parent.firstChild.remove();
    }

    if(arrPeople != null){
        for(let person of arrPeople){
            if(person.ime != "" && (person.mail != "" || person.telst != "") && person.liked){
                DOMaddPerson(person);
            }
        }
    }
}

function prikaziPoAbecedi() {
    const parent = document.getElementById("contacts-container");
    while(parent.firstChild) {
        parent.firstChild.remove();
    }

    let stringArray = localStorage.getItem("people");
    let array = JSON.parse(stringArray); 
    if(array != null){
        array.sort((a, b) => (a.ime > b.ime) ? 1 : (a.ime === b.ime) ? ((a.priimek > b.priimek) ? 1 : -1) : -1);
        for(let person of array){
            if(person.ime != "" && (person.mail != "" || person.telst != "")){
                DOMaddPerson(person);
            }
        }
    }
}

function prikaziIskanje() {
    const parent = document.getElementById("contacts-container");
    while(parent.firstChild) {
        parent.firstChild.remove();
    }

    let vrednost = searchBar.value;
    if(vrednost == "") {
         prikaziVse();
         return;
    }
    let vrednsotiArr = vrednost.split(" ");

    let stringArray = localStorage.getItem("people");
    let array = JSON.parse(stringArray); 

    let arrDodanig = new Array();
    let addBool = false;

    if(array != null){
        array.sort((a, b) => (a.ime > b.ime) ? 1 : (a.ime === b.ime) ? ((a.priimek > b.priimek) ? 1 : -1) : -1);
        for(let vrednost of vrednsotiArr){
            if(vrednost != ""){
                for(let person of array){
                    if(person.ime.toLowerCase().includes(vrednost.toLowerCase()) || person.priimek.toLowerCase().includes(vrednost.toLowerCase())){
                        addBool = true;
                        if(arrDodanig.length > 0){
                            for(let added of arrDodanig){
                                if(added.id == person.id) {
                                    addBool = false;
                                    break;
                                }
                            }
                            if(addBool) {
                                DOMaddPerson(person);
                                arrDodanig.push(person);
                            }
                        } else {
                            DOMaddPerson(person);
                            arrDodanig.push(person);
                        }
                    }
                }
            }
        }
    }
}

document.addEventListener("DOMContentLoaded", () => {

    loadDataFromLocalStorage();
    dodajBtn.addEventListener("click", () => {
        addPerson();
    });
    
    cancelBtn.addEventListener("click", () =>{
        document.getElementById("ime").value = "";
        document.getElementById("priimek").value = "";
        document.getElementById("telst").value = "";
        document.getElementById("mail").value = "";
        toggle_addPerson_display();
    });
    
    shade.addEventListener("click", () =>{
        document.getElementById("ime").value = "";
        document.getElementById("priimek").value = "";
        document.getElementById("telst").value = "";
        document.getElementById("mail").value = "";
        toggle_addPerson_display();
    });
    
    addPersonBtn.addEventListener("click", () => {
        toggle_addPerson_display();
    });

    vseBtn.addEventListener("click", () => {
        prikaziVse();
    });

    priljubljeniBtn.addEventListener("click", () => {
        prikaziPrilljubljne();
    });

    sortBtn.addEventListener("click", () => {
        prikaziPoAbecedi();
    });

    searchBar.addEventListener("change", () => {
        prikaziIskanje();
    });

    searchBtn.addEventListener("click", () => {
        prikaziIskanje();
    });
});