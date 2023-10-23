
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
import Notification from 'core/notification';

/**
 * Defines REGIONS
 */
const REGIONS = {
    MSG: '[data-region="coodle-dates"]',
};

/**
 * Defines dataset Actions
 */
const ACTIONS = {
    ADD: 'local-coodle-add-date',
};

var title;

/**
 * Gets called from mustache template.
 *
 */
export const init = (userid) => {
    const clientid = userid;
    document.querySelector(REGIONS.MSG).addEventListener('click', function(e) {
        let target = e.target;
        if (!target.closest('button')) {
            return;
        }
        switch (target.closest('button').dataset.action) {
            case ACTIONS.ADD:
                e.stopPropagation();
                title = getString('add_calendar_entry', 'local_coodle');
                openForm(e, clientid);
                break;
            default:
                break;
        }
    });
};

/**
 * Opens the Modal to edit questions.
 * @param {*} event the click event
 * @param {*} userid
 */
 const openForm = (event, userid) => {

    let button = event.target.closest('button');

    const modalForm = new ModalForm({
        // Name of the class where form is defined (must extend \core_form\dynamic_form):
        formClass: "local_coodle\\form\\add_calendar_form",
        // Add as many arguments as you need, they will be passed to the form:
        args: {
            'clientid': userid,
        },
        // Pass any configuration settings to the modal dialogue, for example, the title:
        modalConfig: {title: title},

        saveButtonText: getString('add_calendar_entry', 'local_coodle'),
        // DOM element that should get the focus after the modal dialogue is closed:
        returnFocus: button
    });

    // Listen to events if you want to execute something on form submit.
    // Event detail will contain everything the process() function returned:

    modalForm.addEventListener(modalForm.events.FORM_SUBMITTED, (e) => {
        const response = e.detail;

        if (response.error) {
            Notification.alert(getString('add_calendar_entry_notification', 'local_coodle'), getString('add_calendar_entry_error', 'local_coodle'));
            } else {
            Notification.alert(getString('add_calendar_entry_notification', 'local_coodle'), getString('add_calendar_entry_added', 'local_coodle'));
            window.location.reload();
        }
        // eslint-disable-next-line no-console
        console.log('Response of the modal: ', response);
    });

    // Show the form.
    modalForm.show().then(() => {

        return;
    }).catch(e => {
        // eslint-disable-next-line no-console
        console.log(e);
    });
};
