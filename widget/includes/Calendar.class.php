<?php
/**
 * Calendar class
 */
class Miga_calendar_Day
{
    private $number;
    private $date;
    private $weekday;
    private $statusClass;
    private $showTitle = true;
    private $statusText;
    private $month;
    private $year;

    public function __construct($year, $month, $day)
    {
        $this->date = strtotime($year . "-" . $month . "-" . $day);
        $this->month = $month;
        $this->year = $year;
        $this->number = date("d", $this->date);
        $this->weekday = date("w", $this->date); // Sunday = 0
    }

    public function getNumber()
    {
        return (int) $this->number;
    }
    public function getDate()
    {
        return $this->date;
    }
    public function getWeekday()
    {
        return (int) $this->weekday;
    }
    public function setStatus($statusClass, $statusText)
    {
        $this->statusClass = $statusClass;
        $this->statusText = $statusText;
    }
    public function showTitle($val)
    {
        $this->showTitle = $val;
    }
    public function render()
    {
        $hasClass = "";
        if (!empty($this->statusClass)) {
            $hasClass = "calendar__day--hasClass";
        }

        $title = esc_html($this->statusText);
        $todayClass = "";
        if ($this->getNumber() == date('d') && $this->month == date('m') && $this->year == date("Y")) {
            $todayClass = "calendar__day--today";
            $title = __("today", "simple-calendar-for-elementor");
        }

        if (!$this->showTitle) {
            $title = "";
        }
        return '<div title="'.$title.'" class="calendar__day '.$todayClass.' '.esc_html($hasClass).' '.esc_html($this->statusClass).'" data-weekday="'.(int) ($this->weekday).'">'.(int) ($this->getNumber())."</div>";
    }
}

class Miga_calendar_Month
{
    private $month;
    private $year;
    private $daysInMonth;
    private $monthName;
    private $statusClass;
    private $statusText;

    public function __construct($month, $year)
    {
        $monthNames = [
            __("Jan", "simple-calendar-for-elementor"),
            __("Feb", "simple-calendar-for-elementor"),
            __("Mar", "simple-calendar-for-elementor"),
            __("Apr", "simple-calendar-for-elementor"),
            __("May", "simple-calendar-for-elementor"),
            __("Jun", "simple-calendar-for-elementor"),
            __("Jul", "simple-calendar-for-elementor"),
            __("Aug", "simple-calendar-for-elementor"),
            __("Sep", "simple-calendar-for-elementor"),
            __("Oct", "simple-calendar-for-elementor"),
            __("Nov", "simple-calendar-for-elementor"),
            __("Dec", "simple-calendar-for-elementor"),
        ];
        $this->month = $month;
        $this->year = $year;
        $this->statusClass = array_fill(0, 35, "");
        $this->statusText = array_fill(0, 35, "");
        $this->daysInMonth = cal_days_in_month(
            CAL_GREGORIAN,
            $this->month,
            $this->year
        );
        $this->monthName = $monthNames[(int) $this->month - 1];
    }

    public function setStatus($day, $statusClass, $statusText)
    {
        $replace = [(int) $day => $statusClass];
        $replaceText = [(int) $day => $statusText];
        $this->statusClass = array_replace($this->statusClass, $replace);
        $this->statusText = array_replace($this->statusText, $replaceText);
    }
    public function getStatusClass($day)
    {
        return $this->statusClass[(int) $day];
    }
    public function getStatusText($day)
    {
        return $this->statusText[(int) $day];
    }

    public function render($options = null)
    {
        $showMenu = isset($options["menu"]) && $options["menu"] == true;
        $showButton = isset($options["showButton"]) && $options["showButton"] == true;
        $showEditorMenu = isset($options["editorMenu"]) && $options["editorMenu"] == true;
        $fixedMonth = isset($options["fixedMonth"]) && $options["fixedMonth"] == true;
        $showTitle = isset($options["showTitle"]) && $options["showTitle"] == true;

        if ($fixedMonth) {
            $showMenu = $showButton = $showEditorMenu = false;
        }

        echo '<div class="calendar">';
        echo '<div class="calendar__header">';
        if ($showMenu) {
            echo '<button class="calendar__prev" onclick="miga_calendar_loadPrev(this); return false;"><svg width="15" height="15" version="1.1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path transform="matrix(-.2605 .8617 .9721 .2309 6.828 -1.227)" d="m21.73 21.86-23.06-6.178 16.88-16.88 3.089 11.53z"/></svg></button>';
        } elseif ($showEditorMenu) {
            echo '<button class="calendar__prev" onclick="miga_calendar_loadEditorPrev(this); return false;"><svg width="15" height="15" version="1.1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path transform="matrix(-.2605 .8617 .9721 .2309 6.828 -1.227)" d="m21.73 21.86-23.06-6.178 16.88-16.88 3.089 11.53z"/></svg></button>';
        }

        echo '<div class="calendar__title">' . esc_html($this->monthName) . " " .esc_html($this->year) . "</div>";
        if ($showMenu) {
            echo '<button class="calendar__next" onclick="miga_calendar_loadNext(this)"><svg width="15" height="15" version="1.1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path transform="matrix(.2605 .8617 -.9721 .2309 17.05 -1.227)" d="m21.73 21.86-23.06-6.178 16.88-16.88 3.089 11.53z"/></svg></button>';
        } elseif ($showEditorMenu) {
            echo '<button class="calendar__next" onclick="miga_calendar_loadEditorNext(this)"><svg width="15" height="15" version="1.1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path transform="matrix(.2605 .8617 -.9721 .2309 17.05 -1.227)" d="m21.73 21.86-23.06-6.178 16.88-16.88 3.089 11.53z"/></svg></button>';
        }
        echo "</div>";

        echo '<div class="calendar__dates">';
        echo '<div class="calendar__week">';
        echo '<div class="calendar__day_name">' .
            __("Mo", "simple-calendar-for-elementor") .
            "</div>";
        echo '<div class="calendar__day_name">' .
            __("Tu", "simple-calendar-for-elementor") .
            "</div>";
        echo '<div class="calendar__day_name">' .
            __("We", "simple-calendar-for-elementor") .
            "</div>";
        echo '<div class="calendar__day_name">' .
            __("Th", "simple-calendar-for-elementor") .
            "</div>";
        echo '<div class="calendar__day_name">' .
            __("Fr", "simple-calendar-for-elementor") .
            "</div>";
        echo '<div class="calendar__day_name">' .
            __("Sa", "simple-calendar-for-elementor") .
            "</div>";
        echo '<div class="calendar__day_name">' .
            __("Su", "simple-calendar-for-elementor") .
            "</div>";
        echo "</div>";
        echo '<div class="calendar__week">';
        $lastWeekday = 0;
        for ($i = 1, $len = $this->daysInMonth; $i <= $len; ++$i) {
            $d = new Miga_calendar_Day($this->year, $this->month, $i);
            if ($i == 1) {
                $end = $d->getWeekday() - 1;
                if ($end == -1) {
                    $end = 6;
                }
                for ($j = 0; $j < $end; ++$j) {
                    echo '<div class="calendar__day calendar__day--empty"></div>';
                }
            }
            if ($i > 1 && $d->getWeekday() == 1) {
                echo '</div><div class="calendar__week">';
            }
            $lastWeekday = $d->getWeekday() - 1;
            if ($lastWeekday == -1) {
                $lastWeekday = 6;
            }
            $d->showTitle($showTitle);
            $d->setStatus($this->getStatusClass($d->getNumber()), $this->getStatusText($d->getNumber()));
            echo $d->render();
        }
        for ($j = $lastWeekday; $j < 6; ++$j) {
            echo '<div class="calendar__day calendar__day--empty"></div>';
        }
        echo "</div>";
        echo "</div>";
        if ($showButton) {
            if ($showMenu) {
                echo '<button class="calendar__today" onclick="miga_calendar_loadToday(this); return false;">' .
                    esc_html__("today", "simple-calendar-for-elementor") .
                    "</button>";
            } elseif ($showEditorMenu) {
                echo '<button class="calendar__today" onclick="miga_calendar_loadEditorToday(this); return false;">' .
                    esc_html__("today", "simple-calendar-for-elementor") .
                    "</button>";
            }
        }
        echo "</div>";
    }
}
