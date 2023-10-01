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
    CHECK: 'local-coodle-todo-check',
    DELETE: 'local-coodle-todo-delete',
    UNCHECK: 'local-coodle-todo-uncheck',
    COLLAPSE: 'local-coodle-todo-collapse',
    MAKEUSERTODO: 'local-coodle-todo-usertodo',
    UNDOUSERTODO: 'local-coodle-todo-usertodo-revoke',
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
        switch (target.dataset.action) {
            case ACTIONS.CHECK:
                todoCheck(target, target.dataset.id);
                break;
            case ACTIONS.DELETE:
                todoDelete(target, target.dataset.id);
                break;
            case ACTIONS.UNCHECK:
                todoUncheck(target, target.dataset.id);
                break;
            case ACTIONS.COLLAPSE:
                todoCollapse(target, target.dataset.id);
                break;
            case ACTIONS.MAKEUSERTODO:
                todoMakeUserTodo(target, target.dataset.id);
                break;
            case ACTIONS.UNDOUSERTODO:
                todoUndoUserTodo(target, target.dataset.id);
                break;
            default:
                break;
        }
    });
};

/**
 * Marks the todo as completed
 * @param {EventTarget} target the id of the todo
 * @param {Integer} id the id of the todo
 */
const todoCheck = (target, id) => {
    Ajax.call([{
        methodname: "local_coodle_change_todo",
        args: {
            'todoid': parseInt(id),
            'method': 'done',
        },
        done: function() {
            target.closest('li').classList.add('done');
            switchbuttons(target, 'check', id);
        },
        fail: function(ex) {
            // eslint-disable-next-line no-console
            console.log("ex:" + ex);
        },
    }]);
};

/**
 * Deletes todo
 * @param {EventTarget} target the id of the todo
 * @param {Integer} id the id of the todo
 */
const todoDelete = (target, id) => {
    Ajax.call([{
        methodname: "local_coodle_change_todo",
        args: {
            'todoid': parseInt(id),
            'method': 'delete',
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

/**
 * Deletes todo
 * @param {EventTarget} target the id of the todo
 * @param {Integer} id the id of the todo
 */
const todoMakeUserTodo = (target, id) => {
    Ajax.call([{
        methodname: "local_coodle_change_todo",
        args: {
            'todoid': parseInt(id),
            'method': 'usertodo',
        },
        done: function() {
            target.closest('i').classList.remove('fa-user-o');
            target.closest('i').classList.add('fa-user');

            target.closest('i').dataset.action = 'local-coodle-todo-usertodo-revoke';
            target.closest('button').dataset.action = 'local-coodle-todo-usertodo-revoke';
        },
        fail: function(ex) {
            // eslint-disable-next-line no-console
            console.log("ex:" + ex);
        },
    }]);
};

/**
 * Deletes todo
 * @param {EventTarget} target the id of the todo
 * @param {Integer} id the id of the todo
 */
const todoUndoUserTodo = (target, id) => {
    Ajax.call([{
        methodname: "local_coodle_change_todo",
        args: {
            'todoid': parseInt(id),
            'method': 'undousertodo',
        },
        done: function() {
            target.closest('i').classList.remove('fa-user');
            target.closest('i').classList.add('fa-user-o');

            target.closest('i').dataset.action = 'local-coodle-todo-usertodo';
            target.closest('button').dataset.action = 'local-coodle-todo-usertodo';
        },
        fail: function(ex) {
            // eslint-disable-next-line no-console
            console.log("ex:" + ex);
        },
    }]);
};

/**
 * Deletes todo
 * @param {EventTarget} target the id of the todo
 * @param {Integer} id the id of the todo
 */
const todoUncheck = (target, id) => {
    Ajax.call([{
        methodname: "local_coodle_change_todo",
        args: {
            'todoid': parseInt(id),
            'method': 'uncheck',
        },
        done: function() {
            target.closest('li').classList.remove('done');
            switchbuttons(target, 'uncheck', id);
        },
        fail: function(ex) {
            // eslint-disable-next-line no-console
            console.log("ex:" + ex);
        },
    }]);
};

/**
 * Collapses todos for client
 * @param {Integer} userid
 */
const todoCollapse = (userid) => {
    // eslint-disable-next-line no-console
    console.log('collapse' + userid);
};

const switchbuttons = (target, mode, id) => {
    let html = '';
    if (mode == 'uncheck') {
        html = '<button class="btn btn-primary-outline coodle-checkbutton pull-right"' +
        'data-action="local-coodle-todo-check" data-id="' +
        id +
        '"><i class="fa fa-check-square-o fa-2x text-success " aria-hidden="true" data-action="local-coodle-todo-check" data-id="' +
        id +
        '"></i></button>';
    }
    if (mode == 'check') {
        html = '<button class="btn btn-primary-outline coodle-checkbutton pull-right"' +
        'data-action="local-coodle-todo-uncheck" data-id="' +
        id + '"><i class="fa fa-play fa-2x text-success " aria-hidden="true" data-action="local-coodle-todo-uncheck" data-id="' +
        id +
        '"></i></button>';
    }
    let button = target.closest('.coodle-checkbutton');
    button.insertAdjacentHTML("afterend", html);
    button.remove();
};
