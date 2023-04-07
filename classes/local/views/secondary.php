<?php
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

namespace local_coodle\local\views;

use core\navigation\views\secondary as core_secondary;

/**
 * Class secondary_navigation_view.
 *
 * Custom implementation for a plugin.
 *
 * @package     local_coodle
 * @category    navigation
 * @copyright   2022 Thomas Winkler <thomas.winkler@wunderbyte.at>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class secondary extends core_secondary {
    /**
     * Define a custom secondary nav order/view.
     *
     * @return void
     */

    public function initialise(): void {
        $context = \context_system::instance();

        $isadvisor = \local_coodle\settings_manager::is_advisor();
        $isadmin = is_siteadmin();

        if ($isadvisor) {
            $this->add(get_string('home', 'local_coodle') , '/local/coodle/advisor.php', \navigation_node::TYPE_CUSTOM,
            'clientlist', 'clientlist');
            $this->add(get_string('myclients', 'local_coodle') , '/local/coodle/myusers.php', \navigation_node::TYPE_CUSTOM,
            'myclients', 'myclients');
            /*
            $this->add('Todolist' , '/local/coodle/todos.php', \navigation_node::TYPE_CUSTOM,
            'todos', 'todos'); */
            $this->add(get_string('calendar', 'local_coodle'), '/local/coodle/calendar.php', \navigation_node::TYPE_CUSTOM,
            'calendar', 'calendar');
        }
        if ($isadmin) {
            $this->add(get_string('advisorlist', 'local_coodle') , '/local/coodle/advisorlist.php', \navigation_node::TYPE_CUSTOM,
            'advisorlist', 'advisorlist');
            /* $this->add('Add User' , '/local/coodle/advisorlist.php', \navigation_node::TYPE_CUSTOM,
            'advisorlist', 'advisorlist');
            $this->add('Add Advisor' , '/local/coodle/advisorlist.php', \navigation_node::TYPE_CUSTOM,
            'advisorlist', 'advisorlist'); */
            $this->add(get_string('allclients', 'local_coodle') , '/local/coodle/allclients.php', \navigation_node::TYPE_CUSTOM,
            'allclients', 'allclients');
        }
        $this->initialised = true;
    }
}
