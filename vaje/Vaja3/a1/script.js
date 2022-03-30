"use strict";

function domRemoveParticipant(event) {
    // TODO
    //console.log(this);
    let name = $(this).children('td:first').text();
    //console.log(name);

    if(window.confirm(`Are you sure you want to delete ${name}?`)){
        let index = this.rowIndex;
        document.getElementById("participant-table").deleteRow(index);
    }

}

function domAddParticipant(participant) {
    // TODO
    const table = document.getElementById("participant-table");
    //console.log(table);

    const tr = document.createElement("tr");
    table.appendChild(tr);

    tr.ondblclick = domRemoveParticipant;

    for(let attr in participant){
        const td = document.createElement("td");
        td.innerText = participant[attr];
        tr.appendChild(td);
    }

    /* na dolgo za vsak td posebi
    const td1 = document.createElement("td");
    td1.innerText = participant.first;
    tr.appendChild(td1);

    const td2 = document.createElement("td");
    td2.innerText = participant.last;
    tr.appendChild(td2);

    const td3 = document.createElement("td");
    td3.innerText = participant.role;
    tr.appendChild(td3);
    */
}

function addParticipant(event) {
    // TODO: Get values
    const first = document.getElementById("first").value;
    const last = document.getElementById("last").value;
    const role = document.getElementById("role").value;
    
    // TODO: Set input fields to empty values
    document.getElementById("first").value = "";
    document.getElementById("last").value = "";
    
    // Create participant object
    const participant = {
        first: first,
        last: last,
        role: role
    };

    // const participant = { first, last, role };   <- isto kot zgoraj na drugi nacin
    //console.log(participant);

    // Add participant to the HTML
    domAddParticipant(participant);

    // Move cursor to the first name input field
    document.getElementById("first").focus();
}

document.addEventListener("DOMContentLoaded", () => {
    // This function is run after the page contents have been loaded
    // Put your initialization code here
    document.getElementById("addButton").onclick = addParticipant;
})

// The jQuery way of doing it
$(document).ready(() => {
    // Alternatively, you can use jQuery to achieve the same result
});
