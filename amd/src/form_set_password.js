
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/*
 * Modal Form user create
 * @package    local_coodle
 * @copyright  Wunderbyte GmbH <info@wunderbyte.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


import ModalForm from 'core_form/modalform';
import {get_string as getString} from 'core/str';

const SELECTORS = {
    SETPASSWORD: '[data-action="coodle-set-password"]',
};

/**
 * Gets called from mustache template.
 *
 */
export const init = () => {

    // Find all container.
    const containers = document.querySelectorAll(SELECTORS.SETPASSWORD);

    containers.forEach(element => {
        if (!element.dataset.initialized) {
            element.addEventListener('click', openForm);
            element.dataset.initialized = true;
        } else {
            // Just to make sure during development that this is not called to often.
            // eslint-disable-next-line no-console
            console.log('unnecessary call of init');
        }
    });
};

/**
 * Opens the Modal to edit questions.
 * @param {*} event the click event
 */
 const openForm = event =>{
    let button = event.target.closest('button');
    event.stopPropagation();

    const modalForm = new ModalForm({
        // Name of the class where form is defined (must extend \core_form\dynamic_form):
        formClass: "local_coodle\\form\\set_password",
        // Add as many arguments as you need, they will be passed to the form:
        args: {
            'id': button.dataset.id,
        },
        modalConfig: {title: getString('set_password', 'local_coodle')},
        returnFocus: button
    });

    // Listen to events if you want to execute something on form submit.
    // Event detail will contain everything the process() function returned:
    modalForm.addEventListener(modalForm.events.FORM_SUBMITTED, (e) => {
        const response = e.detail;
        // eslint-disable-next-line no-console
        console.log('Response of the modal: ', response);
        document.querySelector('.usercard').classList.remove('d-none');
        document.getElementById('thepassword').innerText = response.password;
    });

    // Show the form.
    modalForm.show().then(() => {

        return;
    }).catch(e => {
        // eslint-disable-next-line no-console
        console.log(e);
    });
};
