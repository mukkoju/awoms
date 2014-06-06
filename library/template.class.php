<?php

/**
 * Template class
 *
 * Handles template data
 *
 * PHP version 5.4
 * 
 * @author    Brock Hensley <Brock@AWOMS.com>
 * 
 * @version   v00.00.0000
 * 
 * @since     v00.00.0000
 */
class Template
{
    /**
     * Class data
     *
     * @var array $data Array holding any class data used in get/set
     * @var string $controller Controller
     * @var string $action Action
     */
    protected $data = array(), $controller, $action, $template;

    /**
     * __construct
     * 
     * Magic method executed on new class
     * 
     * @since v00.00.0000
     * 
     * @version v00.00.0000
     * 
     * @param string $controller Controller name
     * @param string $action Action name
     */
    public function __construct($controller, $action, $template)
    {
        $this->controller = $controller;
        $this->action     = $action;
        $this->template   = $template;
    }

    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function __get($key)
    {
        if ($this->__isset($key)) {
            return $this->data[$key];
        }
        return false;
    }

    public function __isset($key)
    {
        if (array_key_exists($key, $this->data)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * render
     * 
     * Display template
     * 
     * @since v00.00.0000
     * 
     * @version v00.00.0000
     */
    function render()
    {

        // Converts all data to variables for template to use
        extract($this->data);

        // Omit header/footer if ajax call
        $m = FALSE;
        if ($this->template == "json"
                || $this->template == "ajax"
                || $this->template == "m")
        {
            $m = TRUE;
        }
                
        // Views folder
        $viewsFolder = ROOT . DS . 'application' . DS . 'views' . DS;

        // Header if not in ajax/json mode
        if (!$m)
        {

            // Header template
            $templates = array();
            $templates[] = $viewsFolder . 'templates' . DS . BRAND_LABEL . DS . BRAND_THEME . DS . $this->controller . DS . 'header.php';
            $templates[] = $viewsFolder . 'templates' . DS . BRAND_LABEL . DS . BRAND_THEME . DS . 'header.php';
            $templates[] = $viewsFolder . 'header.php';
            foreach ($templates as $template)
            {
                if (file_exists($template))
                {
                    include($template);
                    break;
                }
            }
        }

        // Fatal error skips action template (error msg displayed via header template)
        if (empty($_SESSION['ErrorMessage'])) {
        
            // Action template
            $templates = array();
            $templates[] = $viewsFolder . 'templates' . DS . BRAND_LABEL . DS . BRAND_THEME . DS . $this->controller . DS . $this->action . '.php';
            $templates[] = $viewsFolder . 'templates' . DS . BRAND_LABEL . DS . BRAND_THEME . DS . $this->action .'.php';
            $templates[] = $viewsFolder . $this->controller . DS . $this->action . '.php';
            $templates[] = $viewsFolder . $this->action . '.php';
            foreach ($templates as $template)
            {
                if (file_exists($template))
                {
                    include($template);
                    break;
                }
            }
        }

        // Footer if not in ajax mode
        if (!$m)
        {

            // Footer template
            $templates = array();
            $templates[] = $viewsFolder . 'templates' . DS . BRAND_LABEL . DS . BRAND_THEME . DS . $this->controller . DS . 'footer.php';
            $templates[] = $viewsFolder . 'templates' . DS . BRAND_LABEL . DS . BRAND_THEME . DS . 'footer.php';
            $templates[] = $viewsFolder . 'footer.php';
            foreach ($templates as $template)
            {
                if (file_exists($template))
                {
                    include($template);
                    break;
                }
            }
        }
    }

}