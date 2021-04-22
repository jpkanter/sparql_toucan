const toucan_id_list = new WeakMap();

docReady(function() {
        let buttons = document.querySelectorAll('.tx_sparqltoucan_hidden_btn');
        for( const btn of buttons) {
            let presumedID = btn.id.toString().slice(0, -3) + "form"
            let form = document.querySelector(`#${presumedID}`);
            if( form ) {
                btn.addEventListener("showForm", onclick, false);
                toucan_id_list.set(btn, form);
                console.log("added a listener", btn, form);
            }
        }
        console.log("hidden loader finished")
    }
);

function docReady(fn) {
    // see if DOM is already available
    if (document.readyState === "complete" || document.readyState === "interactive") {
        // call on next available tick
        setTimeout(fn, 1);
    } else {
        document.addEventListener("DOMContentLoaded", fn);
    }
}

function showForm(e) {
    if( toucan_id_list.has(e.target) ) {
        let ourForm = toucan_id_list.get(e.target);
        ourForm.style.display = "block";
        ourForm.style.top = e.clientY+"px"
        ourForm.style.left = e.clientX+"px"
    }
}