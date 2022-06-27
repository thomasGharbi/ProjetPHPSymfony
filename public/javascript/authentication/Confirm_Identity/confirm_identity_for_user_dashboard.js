import ConfirmIdentity from './confirm_identity.js';

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
    document.getElementById('modify_password').setAttribute('href', "Modification_mot_de_passe");
    if (e.detail.identity === 'password_is_confirmed') {
        document.getElementById('button-modal').click();
    }else if(e.detail.identity === 'identity_is_verified') {
        document.body.querySelector('button[id="button-modal"]').type = 'submit';
    }

});


