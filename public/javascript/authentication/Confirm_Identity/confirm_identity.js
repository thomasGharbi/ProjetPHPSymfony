export default class ConfirmIdentity {


    /**
     *
     * @param controller_url
     * @param element_to_listen
     * @param button_to_listen
     * @param fetch_options
     * @param type_of_event
     */

    constructor({controller_url, element_to_listen, fetch_options, type_of_event}) {
        this.confirm_identity_close_modal_button = document.body.querySelector(
            'button[id="close_confirm_identity_password_modal"]');
        this.submit_form_or_display_modal_buttons = element_to_listen;
        this.confirm_identity_modal = document.body.querySelector(
            'div[id="confirm_identity_password_modal"]');
        this.confirm_identity_modal_body = document.body.querySelector(
            'div[id="confirm_identity_password_modal_body"]');
        this.controller_url = controller_url;

        this.fetch_options = fetch_options;

        this.init();


    }

    init() {


        this.askIfPasswordVerified();
    }

    async askIfPasswordVerified() {


        const fetch_options = {
            body: null,

            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-requested-With': 'XMLHttpRequest',
                'Confirm-Identity-With-Password': 'true'
            },
            method: 'POST'
        };
        try {
            const response = await fetch(this.controller_url, this.fetch_options);

            const data = await response.json();

            if(data.password_confirmed === 'valide'){


              this.identityIsVerified();


            }
            else{
                this.displayConfirmIdentityModal();
            }


        } catch (error) {
            console.error(error);
        }


    }


    displayConfirmIdentityModal() {


        this.createConfirmPasswordForm();


        this.confirm_identity_modal.addEventListener('show.bs.modal', () => {
            this.confirm_identity_password_input.focus();
        });

        this.submit_form_or_display_modal_buttons.forEach(setDataToggle);

        function setDataToggle(item) {
            item.setAttribute('data-toggle', 'modal');

        }

        this.confirm_identity_modal_form.addEventListener('submit', (event) => this.confirmIdentity(event));


    }

    createConfirmPasswordForm() {
        if (document.body.contains(document.body.querySelector(
            'form[id="confirm_identity_form"]'))) {

            this.confirm_identity_modal_form = document.body.querySelector('form[id="confirm_identity_form"]');
            this.confirm_identity_password_input = document.body.querySelector('input[name="confirm_identity_password_input"]');
            this.confirm_identity_modal_invalid_paragraph = document.body.querySelector('p[id="invalid_password_entered"]');


        } else {
            const form_element = document.createElement('form');
            form_element.id = "confirm_identity_form";
            form_element.method = "POST";

            const fieldset_element = document.createElement('fieldset');

            const label_element = document.createElement('label');
            label_element.htmlFor = "confirm_identity_password_input";
            label_element.textContent = "Mot de passe :";

            const input_element = document.createElement('input');
            input_element.setAttribute('required', 'true');
            input_element.type = "password";
            input_element.name = "confirm_identity_password_input";
            input_element.className = "form-control";

            const paragraph_element = document.createElement('p');
            paragraph_element.id = "invalid_password_entered";
            paragraph_element.className = "text-danger d-none mt-3";
            paragraph_element.innerHTML = "Mot de passe saisi invalide.";

            const button_element = document.createElement('button');
            button_element.type = "submit";
            button_element.className = "btn btn-primary mt-3";
            button_element.textContent = "Confirmer";


            fieldset_element.append(label_element, input_element, paragraph_element);

            form_element.append(fieldset_element, button_element);

            this.confirm_identity_modal_body.append(form_element);

            this.confirm_identity_modal_form = form_element;

            this.confirm_identity_password_input = input_element;

            this.confirm_identity_modal_invalid_paragraph = paragraph_element;

        }
    }

    async confirmIdentity(event) {
        event.preventDefault();

        const password = this.confirm_identity_password_input.value;

        const fetch_options = {
            body: JSON.stringify({password}),

            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-requested-With': 'XMLHttpRequest',
                'Confirm-Identity-With-Password': 'true'
            },
            method: 'POST'
        };

        try {
            const response = await fetch(this.controller_url, fetch_options);

            const {password_confirmed, attempt_confirm_identity, login_route, status_code}
                = await response.json();

            this.password_confirmed = password_confirmed;
            if (attempt_confirm_identity === 3) {

                location.reload();
            }

            if (status_code === 302) {
                window.location.href = login_route;
            }

            if (this.password_confirmed === 'valide') {
                this.passwordIsVerified();

            } else {
                this.confirm_identity_password_input.value = '';
                this.confirm_identity_modal_invalid_paragraph.classList.remove('d-none');
            }
        } catch (error) {
            console.error(error);
        }
    }

    passwordIsVerified() {


        this.submit_form_or_display_modal_buttons.type = 'submit';

        this.submit_form_or_display_modal_buttons.forEach(removeDataToggle);

        function removeDataToggle(item) {
            item.removeAttribute('data-toggle');
            item.type = 'submit';
        }


        this.confirm_identity_close_modal_button.click();


        const submitEvent = new CustomEvent('submitEvent', {
            detail: {
                identity: 'password_is_confirmed'
            }
        });

        dispatchEvent(submitEvent);
    }

    identityIsVerified() {

        this.submit_form_or_display_modal_buttons.forEach(removeDataToggle);

        function removeDataToggle(item) {
            item.removeAttribute('data-toggle');

        }

        const submitEvent = new CustomEvent('submitEvent', {
            detail: {
                identity: 'identity_is_verified'
            }
        });

        dispatchEvent(submitEvent);


    }


}
