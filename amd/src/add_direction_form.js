
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
 * Modal Form add directon
 * @package    local_coodle
 * @copyright  Wunderbyte GmbH <info@wunderbyte.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import ModalForm from 'core_form/modalform';
import {get_string as getString} from 'core/str';

const SELECTORS = {
    LINK: '.card-direction',
};

/**
 * Defines dataset Actions
 */
const ACTIONS = {
    ADD_DIRECTION: 'local-coodle-add-direction',
};

/**
 * Gets called from mustache template.
 *
 */
export const init = () => {
    document.querySelector(SELECTORS.LINK).addEventListener('click', function(e) {
        let target = e.target;
        if (!target.dataset.action) {
            return;
        }
        switch (target.dataset.action) {
            case ACTIONS.ADD_DIRECTION:
                openForm(e);
                break;
            default:
                break;
        }
    });
};

/**
 * Opens the Modal to edit questions.
 * @param {*} event the click event
 */
 const openForm = event => {

    let button = event.target;
    const modalForm = new ModalForm({

        // Name of the class where form is defined (must extend \core_form\dynamic_form):
        formClass: "local_coodle\\form\\add_direction_form",
        // Add as many arguments as you need, they will be passed to the form:
        args: {
            'clientid': button.dataset.clientid
        },
        // Pass any configuration settings to the modal dialogue, for example, the title:
        modalConfig: {title: getString('add_direction', 'local_coodle')},

        saveButtonText: getString('add_direction', 'local_coodle'),
        // DOM element that should get the focus after the modal dialogue is closed:
        returnFocus: button
    });

    // Listen to events if you want to execute something on form submit.
    // Event detail will contain everything the process() function returned:

    modalForm.addEventListener(modalForm.events.FORM_SUBMITTED, (e) => {
        const response = e.detail;
        // eslint-disable-next-line no-console
        console.log('Response of the modal: ', response);
        window.location.reload();
    });

    // Show the form.
    modalForm.show().then(() => {

        return;
    }).catch(e => {
        // eslint-disable-next-line no-console
        console.log(e);
    });
};
