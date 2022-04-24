import ConfirmIdentity from './confirm_identity.js';
new ConfirmIdentity({
    controller_url: "/confirm-identity",
    element_to_listen: document.body.querySelector('button[id="button-modal"]'),
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
