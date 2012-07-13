<?php
/**
 * Main Application
 * User: edubskiy
 * Date: 3/7/12
 * Time: 4:17 PM
 * Example: new Application()
 */

class Application
{
    const USE_BUILD_SCRIPT = true;

    const USE_COMPRESSION = true;

    const USE_JAVA_COMPRESSION = false;

    const USE_HTML_COMPRESSION = true;

    public $emailDev = 'evgeniy@dubskiy.pro';
    public $emailSupport = 'write@dubskiy.pro';

    // Application Attributes
    public $title = "Professional Developer";
    public $description = '';
    public $charset = 'utf-8';
    public $author = "Evgeniy Dubskiy";
    public $content;
    public $header;
    public $footer;

    /* @var Menu **/
    public $Menu;

    // Path Media with given default folder names as %APPPATH/%folderName
    public $dirApp = 'application';
    public $dirJs =  'js';
    public $dirCss = 'css';
    public $dirImg = 'img';
    public $dirLess = 'less';
    public $dirCache = 'cache';
    public $dirAssets = 'assets';

    // Final Web
    public $webJs;
    public $webCss;
    public $webImg;
    public $webCache;
    public $webLess;

    public $favicon = 'favicon.png';
    public $appleTouchFavicon = 'apple-touch-icon.png';

    // Collections of media file exceptions
    public $standaloneStyles  = array();

    public $standaloneScripts = array(
        'plugins.js',
        'script.js',
        'libs/bootstrap/bootstrap.min.js'
    );

    /**
     * Scripts, styles are only used when const USE_BUILD_SCRIPT = false
     */

    // Ready for compression scripts
    public $scripts = array(
//        'html5.js',
        'jquery-1.7.1.min.js',
        'bootstrap.min.js',
        'application.js'
    );

    // Ready for compression styles
    public $styles = array(
        'bootstrap-responsive.min.css',
        'bootstrap.min.css',
        'application.css',
        'portfolio.css'
    );

    // Directory separator default value
    public $separator = '/';

    /* @var Compiler_Yuicompressor **/
    public $CssCompiler = null;

    /* @var Compiler_Yuicompressor **/
    public $JsCompiler  = null;

    public $viewAutoRender = true;

    protected static $instance;

    // To use to specific method
    public $masterPassword = 'Z0932DB4eeq';

    public $i18n = array();

    function __construct()
    {
        if (defined('DIRECTORY_SEPARATOR'))
        {
            $this->separator = DIRECTORY_SEPARATOR;
        }

        $this->BuildPaths();
    }

    // i18n
    public function __($string, array $values = NULL)
    {
        $mainLang = Request::initial()->param('lang');
        $appLang = 'application';

        $appLocale = $this->GetLocaleAbbr($mainLang, $appLang);

        $string = I18n::get($string, $appLocale);

        return empty($values) ? $string : strtr($string, $values);
    }

    public function IsProduction()
    {
        return (Kohana::$environment === Kohana::PRODUCTION);
    }

    /**
     * @static
     * @return self
     */
    public static function GetInstance()
    {
        if (empty(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function GetDirApp($userPath = null)
    {
        return $this->BuildPath(
            array(
                $this->dirApp,
                $userPath
            )
        );
    }

    public function GetLocaleAbbr($langLocale, $langFile)
    {
        return $langLocale . "-" . $langFile;
    }

    public function SetLocale($userDefinedLocale = null, $userDefinedLang = null)
    {
        $langURI = Request::initial()->param('lang');

        if ($langURI)
        {
            I18n::lang($langURI);
        }

        $thisLangLocale =  I18n::lang();
        $thisController = Request::initial()->controller();

        if ($userDefinedLocale)
        {
            $thisLangLocale = $userDefinedLocale;
        }

        if ($userDefinedLang)
        {
            $thisController = $userDefinedLang;
        }

        $langFile = $this->GetLocaleAbbr($thisLangLocale, $thisController);

        if (Kohana::find_file('i18n/' . $thisLangLocale, $thisController))
        {
            I18n::lang($langFile);
        }

        return $this;
    }

    public function AppleFavicon()
    {
        return $this->webImg . $this->appleTouchFavicon;
    }

    public function Favicon()
    {
        return $this->webImg . $this->favicon;
    }

    protected function InProduction()
    {
        // return dev in URI
    }

    protected function BuildPath($elems)
    {
        $buildPath = '';
        if (is_array($elems))
        {
            foreach ($elems as $elem)
            {
                $separator = $this->separator;
                if (strpos($elem, $separator) !== false || empty($elem))
                {
                    $separator = '';
                }
                $buildPath .= $elem . $separator;
            }
        }
        return $buildPath;
    }

    protected function BuildPaths()
    {
        // Set Production or Development Public Folder

        $dirPublic = $this->IsProduction() ? 'public' : '';
        $pathPublic = $this->GetWebRoot() . $this->BuildPath(
           array(
                $this->dirApp,
                $this->dirAssets,
                $dirPublic
           )
        );

        $this->webCss   = $pathPublic . $this->dirCss;
        $this->webJs    = $pathPublic . $this->dirJs;
        $this->webCache = $pathPublic . $this->dirCache;
        $this->webImg   = $pathPublic . $this->dirImg;
        $this->webLess  = $pathPublic . $this->dirLess;

        foreach($this->standaloneStyles as &$style)
        {
            $style = $this->webCss . $style;
        }

        unset($style);

        foreach($this->standaloneScripts as &$script)
        {
            $script = $this->webJs . $script;
        }

        unset($script);

        return true;
    }

    protected function GetWebRoot()
    {
        return Kohana::$base_url;
    }

    protected function SavePath($path)
    {
        return $path . $this->separator;
    }

    public function PrepareMenu()
    {
        $controllerName = Request::$initial->controller();

        $this->Menu = new Menu();

        if ( isset($this->Menu->items[$controllerName]) )
        {
            $this->Menu->items[$controllerName]['class'] = $this->Menu->activeClass;
        }

        return $this;
    }

    public function GetStylesWithWebPath()
    {
        $scripts = array();
        foreach($this->styles as $style)
        {
            $scripts[] = $this->webCss . $style;
        }
        return $scripts;
    }

    public function GetScriptsWithWebPath()
    {
        $scripts = array();
        foreach($this->scripts as $script)
        {
            $scripts[] = $this->webJs . $script;
        }
        return $scripts;
    }

    public function GetScriptsWithPath()
    {
        $scripts = array();
        foreach($this->scripts as $script)
        {
            $scripts[] = $this->GetDirApp($this->dirJs . $script);
        }
        return $scripts;
    }

    public function GetStylesWithPath()
    {
        $styles = array();
        foreach($this->styles as $style)
        {
            $styles[] = $this->GetDirApp($this->dirCss . $style);
        }
        return $styles;
    }

    public function GetCompiledStyleIncludePath()
    {
        if ($this->CssCompiler instanceof Compiler)
        {
            return $this->webCache . $this->CssCompiler->lastCompressedFile;
        }

        return false;
    }

    public function GetCompiledScriptIncludePath()
    {
        if ($this->JsCompiler instanceof Compiler)
        {
            return $this->webCache . $this->JsCompiler->lastCompressedFile;
        }

        return false;
    }

    public function Compile()
    {
        if ($this->CssCompiler instanceof Compiler)
        {
            $this->CssCompiler->Compile();
        }

        if ($this->JsCompiler instanceof Compiler)
        {
            $this->JsCompiler->Compile();
        }

        return $this;
    }

    public function PrepareCompilers()
    {
        if (self::USE_COMPRESSION)
        {
            if (self::USE_JAVA_COMPRESSION)
            {
                $this->CssCompiler= new Compiler_Yuicompressor(array('type' => 'css'));
//                $this->CssCompiler->AddFiles($this->GetStylesWithPath());

                // This is example of usage YUICompressor for JS files
//                $this->JsCompiler= new Compiler_Yuicompressor(array('type' => 'js'));

                // you can pass special options here
                $this->JsCompiler= new Compiler_Closure();
//                $this->JsCompiler->AddFiles($this->GetScriptsWithPath());
            }
            else
            {
                $this->CssCompiler= new Compiler_Minify(array('type' => 'css'));
//                $this->CssCompiler->AddFiles($this->GetStylesWithPath());

                $this->JsCompiler= new Compiler_Minify(array('type' => 'js'));
//                $this->CssCompiler->AddFiles($this->GetStylesWithPath());
                // Will use PHP5 minify instead
            }

            $this->CssCompiler->AddFiles($this->GetStylesWithPath());
            $this->JsCompiler->AddFiles($this->GetScriptsWithPath());
        }
        else
        {
            $this->standaloneScripts = array_merge(
                $this->standaloneScripts,
                $this->GetScriptsWithWebPath()
            );

            $this->standaloneStyles = array_merge(
                $this->standaloneStyles,
                $this->GetStylesWithWebPath()
            );
        }

        return $this;
    }
};