//function to check form validity
function validateForm(valForm) {
    var pass = valForm.elements["uPassword"];
    var passRep = valForm.elements["uPassRepeat"];
    //reset validity of passRep
    if(passRep != null)
        clearInvalid(passRep);

    if(valForm.checkValidity()) {
        //check passwords
        if(passRep != null)
            if(pass.value !== passRep.value) {
                passRep.setCustomValidity("The passwords do not match.");
                passRep.value = "";
            }
    }
    //puts borders to all invalid inputs after submit
    displayInvalid(valForm);

}
//function to pass form elements to invalidBorder
function displayInvalid(valForm) {
    for(let i=0; i<(valForm.elements.length-1); i++) {
        invalidBorder(valForm.elements[i]);
    }
}
//function trim string inputs
function strTrimmer(curInput) {
    curInput.value = curInput.value.trim();
}
//function to add invalid borders
function invalidBorder(curInput) {
    curInput.classList.add("invalid-input-val");
}
//function to clear custom invalid status of an element
function clearInvalid(inputEl) {
    inputEl.setCustomValidity("");
}