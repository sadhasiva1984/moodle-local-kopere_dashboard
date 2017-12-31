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

/**
 * @created    31/01/17 05:32
 * @package    local_kopere_dashboard
 * @copyright  2017 Eduardo Kraus {@link http://eduardokraus.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_dashboard;

defined('MOODLE_INTERNAL') || die();

use local_kopere_dashboard\html\data_table;
use local_kopere_dashboard\html\table_header_item;
use local_kopere_dashboard\util\dashboard_util;
use local_kopere_dashboard\util\datatable_search_util;
use local_kopere_dashboard\util\title_util;

/**
 * Class users
 * @package local_kopere_dashboard
 */
class users {
    /**
     *
     */
    public function dashboard() {
        dashboard_util::start_page(get_string_kopere('user_title'), -1);

        echo '<div class="element-box table-responsive">';

        $table = new data_table();
        $table->add_header('#', 'id', table_header_item::TYPE_INT);
        $table->add_header(get_string_kopere('user_table_fullname'), 'fullname');
        $table->add_header(get_string_kopere('user_table_username'), 'username');
        $table->add_header(get_string_kopere('user_table_email'), 'email');
        $table->add_header(get_string_kopere('user_table_phone'), 'phone1');
        $table->add_header(get_string_kopere('user_table_celphone'), 'phone2');
        $table->add_header(get_string_kopere('user_table_city'), 'city');

        $table->set_ajax_url('users::load_all_users');
        $table->set_click_redirect('users::details&userid={id}', 'id');
        $table->print_header();
        $table->close(true, 'order:[[1,"asc"]]');

        echo '</div>';
        dashboard_util::end_page();
    }

    /**
     *
     */
    public function load_all_users() {
        $column_select = array(
            'id',
            'firstname',
            'lastname',
            'username',
            'email',
            'phone1',
            'phone2',
            'city'
        );
        $column_order = array(
            'id',
            'fullname' => array('firstname'),
            'username',
            'email',
            'phone1',
            'phone2',
            'city'
        );

        $search = new datatable_search_util($column_select, $column_order);

        $search->execute_sql_and_return("
               SELECT {[COLUMNS]}
                 FROM {USER} u
                WHERE id > 1 AND deleted = 0 ", '', null,
            'local_kopere_dashboard\util\user_util::column_fullname');
    }

    /**
     *
     */
    public function details() {
        $profile = new profile();
        $profile->details();
    }

    /**
     * @param bool $format
     * @return string
     */
    public static function count_all($format = false) {
        global $DB;

        $count = $DB->get_record_sql('SELECT count(*) AS num FROM {USER} WHERE id > 1 AND deleted = 0');

        if ($format) {
            return number_format($count->num, 0, get_string('decsep', 'langconfig'), get_string('thousandssep', 'langconfig'));
        }

        return $count->num;
    }

    /**
     * @param bool $format
     * @return string
     */
    public static function count_all_learners($format = false) {
        global $DB;

        $count = $DB->get_record_sql('SELECT count(*) AS num FROM {USER} WHERE id > 1 AND deleted = 0 AND lastaccess > 0');

        if ($format) {
            return number_format($count->num, 0, get_string('decsep', 'langconfig'), get_string('thousandssep', 'langconfig'));
        }

        return $count->num;
    }

}