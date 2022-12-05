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

/**
 * Defines dataset Actions
 */
const ACTIONS = {
    CHECK: 'local-coodle-todo-check',
    DELETE: 'local-coodle-todo-delete',
    COLLAPSE: 'local-coodle-todo-collapse',
};

/**
 * Defines Selectors
 */
const SELECTORS = {
    DASHBOARD: '.coodle-dashboard',
};

/**
 * Initialize function
 */
export const init = () => {
    initEventListener();
};

/**
 * Initialize function
 */
const initEventListener = () => {
    document.querySelector(SELECTORS.DASHBOARD).addEventListener('click', function(e) {
        let target = e.target;
        // eslint-disable-next-line no-console
        console.log(target.dataset.action);
        if (!target.dataset.action) {
            // eslint-disable-next-line no-console
            console.log('nix');
            return;
        }
        if (target.dataset.action == ACTIONS.CHECK) {
            todoCheck(target.dataset.id);
        }
        if (target.dataset.action == ACTIONS.DELETE) {
            todoDelete(target.dataset.id);
        }
        if (target.dataset.action == ACTIONS.COLLAPSE) {
            todoCollapse(target.dataset.id);
        }
    });
};

/**
 * Marks the todo as completed
 * @param {Integer} id the id of the todo
 */
const todoCheck = (id) => {
    // eslint-disable-next-line no-console
    console.log('check' + id);
};

/**
 * Deletes todo
 * @param {Integer} id the id of the todo
 */
const todoDelete = (id) => {
    // eslint-disable-next-line no-console
    console.log('delete' + id);
};

/**
 * Collapses todos for client
 * @param {Integer} userid
 */
const todoCollapse = (userid) => {
    // eslint-disable-next-line no-console
    console.log('collapse' + userid);
};
