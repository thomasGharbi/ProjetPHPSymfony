import ConfirmIdentity from './confirm_identity.js';

let all_buttons = document.body.querySelectorAll(
    'button[data-target="#confirm_identity_password_modal"]');


let button_to_modify = document.getElementById('button-modify-company');

let button_to_delete = document.getElementById('button-delete-company');

let button_to_listen = null;

new ConfirmIdentity({
    controller_url: "/confirm-identity",
    element_to_listen: all_buttons,
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


button_to_modify.addEventListener('click', () => {

    button_to_listen = button_to_modify;

});

button_to_delete.addEventListener('click', () => {

    button_to_listen = button_to_delete;



});


addEventListener('submitEvent', (e) => {

    if (e.detail.identity === 'password_is_confirmed') {

        button_to_listen.click();


    }

});

addEventListener('submitEvent', (e) => {

    if (e.detail.identity === 'identity_is_verified') {
        console.log('here')
        button_to_modify.type = 'submit';
        button_to_delete.addEventListener('click', myfunction);

        function myfunction() {

            if (confirm('êtes-vous sûr de vouloir supprimer cette entreprise ?') === true) {
                button_to_delete.removeEventListener('click', myfunction)
                button_to_delete.type = 'submit';
                button_to_delete.click();

            }
        }


    }


});



























