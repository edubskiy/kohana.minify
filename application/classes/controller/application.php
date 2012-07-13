<?php
/**
 * Created by JetBrains PhpStorm.
 * User: edubskiy
 * Date: 2/15/12
 * Time: 5:02 PM
 * To change this template use File | Settings | File Templates.
 */

class Controller_Application extends Controller_Template
{
    /* @var Controller_Application **/
    public $template = 'application';

    /* @var  Application **/
    public $App;

    /**
     * The before() method is called before your controller action.
     * In our template controller we override this method so that we can
     * set up default values. These variables are then available to our
     * controllers if they need to be modified.
     */
    public function before()
    {
        parent::before();

        $this->App = new Application();

        // You can chaining requests to set up application
        $this->App
            ->PrepareCompilers()
//            ->Compile();
    }

    public function ViewSave($property, $value)
    {
//        if (Application::USE_HTML_COMPRESSION)
//        {
//            if (class_exists('Compiler_Minify'))
//            {
//                $HTMLCompiler = new Compiler_Minify(array('type' => 'html'));
//                $value = $HTMLCompiler->CompilePredefinedData($value);
//            }
//        }

        $this->App->$property = $value;
        return $this;
    }

    /**
    * The after() method is called after your controller action.
    * In our template controller we override this method so that we can
    * make any last minute modifications to the template before anything
    * is rendered.
    */
    public function after()
    {
//        if ($this->auto_render) {}

        $this
            ->ViewSave('header', View::factory('header', array('menu' => $this->App->Menu)))
            ->ViewSave('footer', View::factory('footer'));

        // Load View automatically with the same name as current Controller
        if ($this->App->viewAutoRender)
        {
            $this->ViewSave('content', View::factory($this->request->controller()));
        }

        $this->template->App = $this->App;

        // render body
        parent::after();

        if (Application::USE_HTML_COMPRESSION)
        {
            if (class_exists('Compiler_Minify'))
            {
                $HTMLCompiler = new Compiler_Minify(array('type' => 'html'));
                $response = $HTMLCompiler->CompilePredefinedData($this->response->body());
                $this->response->body($response);
            }
        }

    }
}