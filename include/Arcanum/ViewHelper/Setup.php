<?php

class Arcanum_ViewHelper_Setup {
  
  public static function example_accordion($pretext, $title = '') {
    static $example_accordion_autoincrement = 1;
    static $example_accordion_autoincrement2 = 1;
    if($title == '') {
      $title = _("Examples");
    }

    echo '<div class="accordion" id="accordion_example_'.$example_accordion_autoincrement.'">
      <div class="accordion-group">
        <div class="accordion-heading">
          <a class="accordion-toggle" data-toggle="collapse"  href="#accordion_'.$example_accordion_autoincrement.'collapse_'.$example_accordion_autoincrement2.'">
               '.$title .'
          </a>
        </div>
        <div id="accordion_'.$example_accordion_autoincrement.'collapse_'.$example_accordion_autoincrement2.'" class="accordion-body collapse">
          <div class="accordion-inner">
    <pre style="margin-left: 1em;">'.$pretext .'</pre>
          </div>
        </div>
    </div>';

    $example_accordion_autoincrement++;
    $example_accordion_autoincrement2++;

  }

  public static function navigation($op, $editing_existing) {
    global $operations;
    $out = '<ul class="nav nav-tabs nav-stacked">';
    foreach($operations as $o => $t) {
        if($op == $o) {
            $out .= '<li class="active">'.
                ($editing_existing ? '<a href="setup.php?step='.$o.'">' : '') .
                $t.
                ($editing_existing ? ' </a>' : '') .
                '</li>';
        } else {
            
            $out .= '<li>'.
                ($editing_existing ? '<a href="setup.php?step='.$o.'">' : '') .
                $t.
                ($editing_existing ? '</a>' : '') .
                '</li>';
        }
    }
    $out .= '</ul>';

    return $out;
  }

}

