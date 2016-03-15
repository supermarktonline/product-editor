/**
 * Created by david on 3/15/16.
 */

var autosaveTimeout;

/*
Trigger the autosave loop
 */
function startAutosave() {
    autosaveTimeout = setTimeout(function(){

        saveCurrentProduct($('#last_state').attr("data-last_state"));

        startAutosave();
    }, 10000);
}

function stopAutosave() {
    clearTimeout(autosaveTimeout);
}