import ConfirmIdentity from './confirm_identity.js';


let button_to_delete = document.getElementById('button-delete-user');

let delete_user = false;


new ConfirmIdentity({
    controller_url: "/confirm-identity",
    element_to_listen: document.body.querySelectorAll('button[id="button-modal"]'),
    fetch_options: {
        body: null,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'confirm-password': 'true'

        },
        method: 'POST'
    },

});


addEventListener('submitEvent', (e) => {


    if (e.detail.identity === 'password_is_confirmed') {
        if (delete_user){
            button_to_delete.click();
        }else{
            document.getElementById('button-modal').click();
        }

    }else if(e.detail.identity === 'identity_is_verified') {

        document.body.querySelector('button[id="button-modal"]').type = 'submit';
    }

});

button_to_delete.addEventListener('click', deleteUser);

function deleteUser() {

    console.log(8)
    if (document.body.querySelector('button[id="button-modal"]').type === 'submit') {
        if (confirm('êtes-vous sûr de vouloir supprimer cet utilisateur ?') === true) {

            button_to_delete.type = 'submit';
        }
    }else{
        delete_user = true
        document.getElementById('button-modal').click();

    }
}
