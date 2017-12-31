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
 * @created    30/01/17 09:39
 * @package    local_kopere_dashboard
 * @copyright  2017 Eduardo Kraus {@link http://eduardokraus.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_dashboard;

defined('MOODLE_INTERNAL') || die();

use local_kopere_dashboard\report\files;
use local_kopere_dashboard\util\bytes_util;
use local_kopere_dashboard\util\dashboard_util;
use local_kopere_dashboard\util\title_util;

/**
 * Class dashboard
 * @package local_kopere_dashboard
 */
class dashboard {
    /**
     *
     */
    public function start() {
        dashboard_util::start_page(array());

        echo '<div class="element-content">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="element-box color1">
                            <div class="label">'.get_string_kopere('dashboard_title_user').'</div>
                            <div class="value"><a href="?users::dashboard">
                                ' . users::count_all(true) . ' / ' . users::count_all_learners(true) . '</a></div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="element-box color2">
                            <div class="label">'.get_string_kopere('dashboard_title_online').'</div>
                            <div class="value"><a href="?usersonline::dashboard">
                                <span id="user-count-online">' . usersonline::count(10) . '</span>
                                / ' . usersonline::count(60) . '</a></div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="element-box color3">
                            <div class="label">'.get_string_kopere('dashboard_title_course').'</div>
                            <div class="value"><a href="?courses::dashboard">
                            ' . courses::count_all(true) . '
                                / ' . courses::count_all_visibles(true) . '</a></div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="element-box color4">
                            <div class="label">'.get_string_kopere('dashboard_title_disk').'</div>
                            <div class="value"><a href="?reports::dashboard&type=server">
                            ' . bytes_util::size_to_byte(files::count_all_space()) . '</a></div>
                        </div>
                    </div>
                </div>
            </div>';

        echo '<div class="row">';
        $this->last_grades();
        $this->last_enroll();
        echo '</div>';

        dashboard_util::end_page();
    }

    /**
     *
     */
    private function last_grades() {
        global $DB, $PAGE;
        echo '<div class="col-sm-6">
                  <div class="element-box">';
        title_util::print_h3('dashboard_grade_title');

        $grade = new grade();
        $last_grades = $grade->get_last_grades();

        foreach ($last_grades as $grade) {

            $user = $DB->get_record('user', array('id' => $grade->userid));

            $userpicture = new \user_picture($user);
            $userpicture->size = 1;
            $profileimageurl = $userpicture->get_url($PAGE)->out(false);

            if ($grade->itemtype == 'mod') {
                $evaluation = get_string_kopere('dashboard_grade_inmod', $grade) ;
            } else if ($grade->itemtype == 'course') {
                $evaluation = get_string_kopere('dashboard_grade_incourse', $grade);
            } else {
                continue;
            }

            $grade_text = number_format($grade->finalgrade, 1, get_string('decsep', 'langconfig'), '') . ' '.get_string_kopere('dashboard_grade_of').' ' . intval($grade->rawgrademax);

            echo '<div class="media dashboard-grade-list">
                      <div class="media-left">
                          <img title="' . fullname($user) . '" src="' . $profileimageurl . '" class="media-object" >
                      </div>
                      <div class="media-body">
                          <h4 class="media-heading">
                              <a data-toggle="modal" data-target="#modal-details"
                                 href="?users::details&userid=' . $user->id . '"
                                 data-href="open-ajax-table.php?users::details&userid=' . $user->id . '">' . fullname($user) . '</a>
                          </h4>
                          <p>'.get_string_kopere('dashboard_grade_text', ['grade' => $grade_text, 'evaluation' => $evaluation]).'</p>
                          <div class="date"><small>'.get_string_kopere('dashboard_grade_in').' <i>' . userdate($grade->timemodified, get_string_kopere('dateformat')) . '</i></small></div>
                      </div>
                      <div class="clear"></div>
                  </div>';

        }
        echo '    </div>
              </div>';
    }

    /**
     *
     */
    private function last_enroll() {
        global $DB, $PAGE;
        echo '<div class="col-sm-6 ">
                  <div class="element-box">';
        title_util::print_h3('dashboard_enrol_title');

        $enrol = new enroll();
        $last_enroll = $enrol->last_enroll();

        foreach ($last_enroll as $enrol) {

            $user = $DB->get_record('user', array('id' => $enrol->userid));
            if($user) {

                $userpicture = new \user_picture($user);
                $userpicture->size = 1;
                $profileimageurl = $userpicture->get_url($PAGE)->out(false);

                $status_matricula = '<span class="btn-danger">' . get_string_kopere('dashboard_enrol_inactive') . '</span>';
                if ($enrol->status == 0) {
                    $status_matricula = '<span class="btn-success">' . get_string_kopere('dashboard_enrol_active') . '</span>';
                }

                echo '<div class="media dashboard-grade-list">
                          <div class="media-left">
                              <img title="' . fullname($user) . '" src="' . $profileimageurl . '" class="media-object" >
                          </div>
                          <div class="media-body">
                              <h4 class="media-heading">
                                  <a data-toggle="modal" data-target="#modal-details"
                                     href="?users::details&userid=' . $user->id . '"
                                     data-href="open-ajax-table.php?users::details&userid=' . $user->id . '">' . fullname($user) . '</a>
                              </h4>
                              <p>' . get_string_kopere('dashboard_enrol_text', $enrol) . '
                                  <span class="status">' . $status_matricula . '</span>
                              </p>
                              <div class="date"><small>' . get_string_kopere('dashboard_enrol_lastmodifield') . ' <i>' . userdate($enrol->timemodified, get_string_kopere('dateformat')) . '</i></small></div>
                          </div>
                          <div class="clear"></div>
                      </div>';
            }
        }

        echo '    </div>
              </div>';
    }
}