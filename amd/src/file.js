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
 * Coodle todo Event functions
 * @package    local_coodle
 * @copyright  Wunderbyte GmbH <info@wunderbyte.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';

/**
 * Defines dataset Actions
 */
const ACTIONS = {
    DELETE: 'local-coodle-delete-file',
};

/**
 * Initialize function
 *
 * @param {int} id
 */
export const init = (id) => {
    console.log(id);
    initEventListener(id);
};

/**
 * Add eventlistener for Actions
 * @param {*} id
 */
const initEventListener = (id) => {
    document.getElementById(id).addEventListener('click', function(e) {
        let target = e.target;
        // eslint-disable-next-line no-console
        console.log(target.dataset.action);
        if (!target.dataset.action) {

            return;
        }
        switch (target.dataset.action) {
            case ACTIONS.DELETE:
                fileDelete(target, target.dataset.id);
                break;
            default:
                break;
        }
    });
};


/**
 * Deletes a file
 * @param {EventTarget} target the id of the todo
 * @param {Integer} id the id of the todo
 */
const fileDelete = (target, id) => {
    // TODO: modal question?
    Ajax.call([{
        methodname: "local_coodle_delete_file",
        args: {
            'fileid': parseInt(id),
        },
        done: function() {
            target.closest('li').remove();
        },
        fail: function(ex) {
            // eslint-disable-next-line no-console
            console.log("ex:" + ex);
        },
    }]);
};
