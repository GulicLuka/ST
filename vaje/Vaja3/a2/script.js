"use strict";
let participantId = 0;
let allParticipants = new Array();

function loadData() {
    if(localStorage.getItem("item") != ""){
        allParticipants = JSON.parse(localStorage.getItem("item"));
    }
    console.log(allParticipants);

    let maxId = 0;
    if(allParticipants != null){
        for(let el of allParticipants){
            if(el.role != ""){
                domAddParticipant(el);
                if(el.id > maxId)
                    maxId = el.id;
            }
        }
        participantId = maxId + 1;
    }

    if(allParticipants == null){
        const participant = {
            id: participantId,
            first: "",
            last: "",
            role: ""
        };
        allParticipants = [participant];
    }
}

function domRemoveParticipant(event) {
    let name = $(this).children('td:first').text();
    let idOfTr = this.id;

    for(let i = 0; i < allParticipants.length; i++){
        if(allParticipants[i].id == idOfTr){
            allParticipants.splice(i, 1);
        }
    }
    if(window.confirm(`Are you sure you want to delete ${name}?`)){
        let index = this.rowIndex;
        document.getElementById("participant-table").deleteRow(index);
    }
    let parsedArray = JSON.stringify(allParticipants);
    localStorage.setItem("item", parsedArray);
}

function domAddParticipant(participant) {
    const table = document.getElementById("participant-table");

    const tr = document.createElement("tr");
    tr.id = participant.id;
    table.appendChild(tr);

    tr.ondblclick = domRemoveParticipant;

    for(let attr in participant){
        if(attr != "id"){
            const td = document.createElement("td");
            td.innerText = participant[attr];
            tr.appendChild(td);
        }
    }
}

function addParticipant() {
    console.log(allParticipants);

    const first = document.getElementById("first").value;
    const last = document.getElementById("last").value;
    const role = document.getElementById("role").value;
    
    document.getElementById("first").value = "";
    document.getElementById("last").value = "";
    
    const participant = {
        id: participantId,
        first: first,
        last: last,
        role: role
    };

    allParticipants.push(participant);
    let parsedArray = JSON.stringify(allParticipants);
    localStorage.setItem("item", parsedArray);
    participantId++;
    // const participant = { first, last, role };   <- isto kot zgoraj na drugi nacin

    // Add participant to the HTML
    domAddParticipant(participant);

    // Move cursor to the first name input field
    document.getElementById("first").focus();
}

document.addEventListener("DOMContentLoaded", () => {
    // This function is run after the page contents have been loaded
    // Put your initialization code here
    loadData();
    document.getElementById("addButton").onclick = addParticipant;
})


