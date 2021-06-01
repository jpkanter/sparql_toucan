const toucan_id_list = new WeakMap();
let toucan_last_form = null;

tou_docReady(function() {
        let buttons = document.querySelectorAll('.tx_sparqltoucan_hidden_btn');
        for( const btn of buttons) {
            let presumedID = btn.id.toString().slice(0, -3) + "form"
            let form = document.querySelector(`#${presumedID}`);
            if( form ) {
                btn.addEventListener("click", tou_showForm, false);
                toucan_id_list.set(btn, form);
                console.log("added a listener", btn, form);
                //close button, maybe not around
                let presumedID2 = btn.id.toString().slice(0, -3) + "close"
                let form2 = document.querySelector(`#${presumedID2}`);
                if( form2 ) {
                    form2.addEventListener("click", tou_hideForm, false);
                    toucan_id_list.set(form2, form); //clunky, i could make a selector in the hide function, ram is cheap
                }
            }

        }
    }
);

function tou_docReady(fn) {
    // see if DOM is already available
    if (document.readyState === "complete" || document.readyState === "interactive") {
        // call on next available tick
        setTimeout(fn, 1);
    } else {
        document.addEventListener("DOMContentLoaded", fn);
    }
}

function tou_showForm(e) {
    e.preventDefault();
    if( toucan_id_list.has(e.target) ) {
        let ourForm = toucan_id_list.get(e.target);
        ourForm.style.display = "block"; //things need to be visible first
        let w_w = window.innerWidth;
        let w_h = window.innerHeight;
        //this kind of if without brackets is dangerous..but so much cleaner
        let x_pos, y_pos;
        if( ourForm.offsetWidth + e.clientX >= w_w ) x_pos = w_w-ourForm.offsetWidth;
        else x_pos = e.clientX+window.pageXOffset
        if( ourForm.offsetHeight + e.clientY >= w_h ) y_pos = w_h-ourForm.offsetHeight;
        else y_pos = e.clientY+window.pageYOffset;
        console.log(e.clientX, e.clientY, ourForm.offsetWidth, ourForm.offsetHeight, x_pos, y_pos, w_w, w_h);

        ourForm.style.top = y_pos+"px"
        ourForm.style.left = x_pos+"px"
        //i cannot iterate through the weak map to close all others, so this has to do
        if( toucan_last_form && toucan_last_form !== ourForm ) {
            toucan_last_form.style.display = "none";
        }
        toucan_last_form = ourForm;
        //calc position, if less space below make above, make below when space above isnt enough
    }
}

function tou_hideForm(e) {
    e.preventDefault();
    if( toucan_id_list.has(e.target) ) {
        let ourForm = toucan_id_list.get(e.target);
        ourForm.style.display = "none";

    }
}