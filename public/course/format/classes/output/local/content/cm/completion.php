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

namespace core_courseformat\output\local\content\cm;

use cm_info;
use core_course\output\activity_completion;
use section_info;
use stdClass;
use core\output\externable;
use core\output\named_templatable;
use core\output\renderable;
use core\output\local\dropdown\dialog as dropdown_dialog;
use core_completion\cm_completion_details;
use core_completion\external\completion_info_exporter;
use core_courseformat\base as course_format;
use core_courseformat\output\local\courseformat_named_templatable;

/**
 * Base class to render course module completion.
 *
 * @package   core_courseformat
 * @copyright 2023 Mikel Martin <mikel@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class completion implements externable, named_templatable, renderable {
    use courseformat_named_templatable;

    /** @var bool $smallbutton if the button is rendered small (like in course page). */
    private bool $smallbutton = true;

    /**
     * Constructor.
     *
     * @param course_format $format the course format
     * @param section_info $section the section info
     * @param cm_info $mod the course module ionfo
     */
    public function __construct(
        protected course_format $format,
        protected section_info $section,
        protected cm_info $mod,
    ) {
    }

    /**
     * Set if the button is rendered small.
     *
     * By default, the button is rendered small like in the course page. However,
     * in other pages like the activities overview, the button is rendered like
     * a regular button.
     *
     * @param bool $smallbutton if the button is rendered small (like in course page).
     */
    public function set_smallbutton(bool $smallbutton): void {
        $this->smallbutton = $smallbutton;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return stdClass|null data context for a mustache template
     */
    public function export_for_template(\renderer_base $output): ?stdClass {
        global $USER;

        if (!$this->format->show_activity_editor_options($this->mod)) {
            return null;
        }

        $course = $this->mod->get_course();

        $showcompletionconditions = $course->showcompletionconditions == COMPLETION_SHOW_CONDITIONS;
        $completiondetails = cm_completion_details::get_instance($this->mod, $USER->id, $showcompletionconditions);

        $showcompletioninfo = $completiondetails->has_completion() &&
            ($showcompletionconditions || $completiondetails->show_manual_completion());
        if (!$showcompletioninfo) {
            return null;
        }

        $completion = new activity_completion($this->mod, $completiondetails, $this->smallbutton);
        $completiondata = $completion->export_for_template($output);

        if ($completiondata->isautomatic || ($completiondata->ismanual && !$completiondata->istrackeduser)) {
            $completiondata->completiondialog = $this->get_completion_dialog($output, $completiondata);
        }

        return $completiondata;
    }

    #[\Override]
    public function get_exporter(?\core\context $context = null): completion_info_exporter {
        global $USER;
        return new completion_info_exporter(
            $this->format->get_course(),
            $this->mod,
            $USER->id,
        );
    }

    #[\Override]
    public static function get_read_structure(
        int $required = VALUE_REQUIRED,
        mixed $default = null
    ): \core_external\external_single_structure {
        return completion_info_exporter::get_read_structure($required, $default);
    }

    #[\Override]
    public static function read_properties_definition(): array {
        return completion_info_exporter::read_properties_definition();
    }

    /**
     * Get the completion dialog.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @param stdClass $completioninfo the completion info
     * @return array the completion dialog exported for template
     */
    protected function get_completion_dialog(\renderer_base $output, stdClass $completioninfo): array {
        global $PAGE;

        $editurl = new \moodle_url(
            '/course/modedit.php',
            ['update' => $this->mod->id, 'showonly' => 'activitycompletionheader']
        );
        $completioninfo->editurl = $editurl->out(false);
        $completioninfo->editing = $PAGE->user_is_editing();
        $completioninfo->hasconditions = $completioninfo->ismanual || count($completioninfo->completiondetails) > 0;
        $dialogcontent = $output->render_from_template('core_courseformat/local/content/cm/completion_dialog', $completioninfo);

        $buttoncontent = get_string('completionmenuitem', 'completion');
        $buttonclass = 'btn-subtle-body';
        if ($completioninfo->istrackeduser) {
            $buttoncontent = get_string('todo', 'completion');
            if ($completioninfo->overallcomplete) {
                $buttoncontent = $output->pix_icon('i/checked', '') . " " . get_string('completion_manual:done', 'core_course');
                $buttonclass = 'btn-subtle-success';
            }
        }

        $buttonclass .= $this->smallbutton ? ' btn-sm' : '';

        $completiondialog = new dropdown_dialog($buttoncontent, $dialogcontent, [
            'classes' => 'completion-dropdown',
            'buttonclasses' => 'btn dropdown-toggle icon-no-margin ' . $buttonclass,
            'dropdownposition' => dropdown_dialog::POSITION['end'],
        ]);

        return $completiondialog->export_for_template($output);
    }
}
