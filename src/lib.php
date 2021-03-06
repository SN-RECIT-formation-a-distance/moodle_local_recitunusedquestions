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
//namespace core_question\bank\search;
//namespace local_recitunusedquestions;

use core_question\bank\search\condition;
defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->dirroot . '/question/classes/bank/search/condition.php');
require_once($CFG->dirroot . '/question/editlib.php');

function local_recitunusedquestions_get_question_bank_search_conditions($caller) {
    return array( new local_recitunusedquestions_question_bank_search_condition($caller));
}

class local_recitunusedquestions_question_bank_search_condition extends core_question\bank\search\condition  {
    protected $where;
    protected $params;
    protected $onlyused = 0;

    const ONLYUSED = 1;
    const ONLYUNUSED = -1;
    const ONLYANY = 0;

    public function __construct() {
        $params = [];
        // POST request from mod/quiz/edit.php to lib/ajax/service.php
        if(isset($GLOBALS['args']['args'][0]['value'])){
            $queryString = $GLOBALS['args']['args'][0]['value'];
            $queryString = preg_replace('/^\?/', '', $queryString);
            parse_str($queryString, $params);
        }
        if(isset($params['onlyused'])){
            $this->onlyused = $params['onlyused'];
        } else {
            // GET request from question/edit.php
            $this->onlyused = optional_param('onlyused', 0, PARAM_INT);
        }
        if ($this->onlyused != self::ONLYANY) {
            $this->init();
        }
    }

    public function where() {
        return $this->where;
    }

    public function params() {
        return $this->params;
    }

    public function display_options_adv() {
        
        global $CFG;
        $link1 = $CFG->wwwroot."/local/recitunusedquestions/pix/icon.png";
        echo '<div class= "card m-1 p-3">';
       
        echo '<p class="h3">'.html_writer::empty_tag('img', array('class' => 'icon','src' => $link1, 'alt' => ('alt'))). get_string('pluginname', 'local_recitunusedquestions').'</p>';
        $options = array(self::ONLYUNUSED => get_string('onlyunused', 'local_recitunusedquestions'),
                self::ONLYUSED => get_string('onlyused', 'local_recitunusedquestions'));
        $attr = array ('class' => 'searchoptions');
        echo html_writer::select($options, 'onlyused', $this->onlyused,
                array(self::ONLYANY => get_string('usedandunused', 'local_recitunusedquestions')), $attr);
                
        echo '</div>';
        echo '<br />';
    }

    private function init() {
        global $DB;
        if ($this->onlyused == self::ONLYUSED) {
            $this->where = '(q.id IN (SELECT questionid FROM {quiz_slots}))';
        } else if ($this->onlyused == self::ONLYUNUSED) {
            $this->where = '(q.id NOT IN (SELECT questionid FROM {quiz_slots}))';
        }
    }

}
