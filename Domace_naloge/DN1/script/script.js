const addPersonBtn = document.getElementById("addPersonBtn");
const addNewPersonDiv = document.getElementById("add-new-person");
const shade = document.getElementById("shade");
const dodajBtn = document.getElementById("dodaj");
const cancelBtn = document.getElementById("cancel");


function toggle_addPerson_display(){
    if(addNewPersonDiv.style.display == "flex" && shade.style.display == "block"){
        shade.style.display = "none";
        addNewPersonDiv.style.display = "none";
    } else{
        shade.style.display = "block";
        addNewPersonDiv.style.display = "flex";
    }
}















dodajBtn.addEventListener("click", () => {
    let ime = document.getElementById("ime").value;
    let priimek = document.getElementById("priimek").value;
    let vzdevek = document.getElementById("vzdevek").value;
    let telst = document.getElementById("telst").value;
    let mail = document.getElementById("mail").value;
    console.log(ime, priimek, vzdevek, telst, mail);
    toggle_addPerson_display();
});

cancelBtn.addEventListener("click", () =>{
    document.getElementById("ime").value = "";
    document.getElementById("priimek").value = "";
    document.getElementById("vzdevek").value = "";
    document.getElementById("telst").value = "";
    document.getElementById("mail").value = "";
    toggle_addPerson_display();
});

shade.addEventListener("click", () =>{
    document.getElementById("ime").value = "";
    document.getElementById("priimek").value = "";
    document.getElementById("vzdevek").value = "";
    document.getElementById("telst").value = "";
    document.getElementById("mail").value = "";
    toggle_addPerson_display();
});

addPersonBtn.addEventListener("click", () => {
    toggle_addPerson_display();
});