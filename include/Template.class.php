<?php
/**
 * Simple Template Class
 *
 * @package arcanum
 * @version $Id: Template.class.php 5485 2012-01-17 12:59:11Z avel $
 */

/**
 * The simplest template class in the world.
 *
 * @todo Build a Cache Layer modeled after
 *       http://massassi.com/php/articles/template_engines/
 */
class Template {
    /**
     * Array of template values
     */
    private $values = array();

    /**
     * Assign template variable $name with value $value
     *
     * @param string $name
     * @param string $value
     * @return void
     */
    public function assign($name, $value = '') {
        $this->values[$name] = $value;
    }

    /**
     * Display file from template directory templates/; it should be a PHP file
     * with simple variables inside.
     *
     * For easy customization, the order in which template files are found is:
     * templates/$template.local.tpl.php
     * templates/$template.tpl.php
     *
     * @param string $template
     * @return void
     */
    public function display($template) {
        extract($this->values);
        if(file_exists('templates/'.$template.'.local.tpl.php')) {
            require('templates/'.$template.'.local.tpl.php');
        } else {
            require('templates/'.$template.'.tpl.php');
        }
    }

    /**
     * Fetch template int o a variable.
     * 
     * @see display()
     * @param string $template
     * @return string
     */
    public function fetch($template) {
        ob_start();
        $this->display($template);
        $out = ob_get_contents();
        ob_end_clean();
        return $out;
    }

}
