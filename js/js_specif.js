function LookupOpen(lookup_url, lookup_name, lookup_options) {
    window.open(lookup_url, lookup_name, lookup_options);
}

function getLookupRetrieve(val, lib) {
    window.opener.document.forms["crud_form"].elements["choix_val"].value=val;
    window.opener.document.forms["crud_form"].elements["choix_lib"].value=lib;
    window.close();
}

function getCrudLookupCallback(id) {
    window.opener.document.forms["crud_origine"].elements["crud_callback_reload"].value='YES';
    window.opener.document.forms["crud_origine"].elements["crud_callback_id"].value=id;
    window.opener.location.reload();
    window.close();
}

	            