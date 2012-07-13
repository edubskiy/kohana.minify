<?php
/**
 * YUI Compressor Loader (using for compressing CSS)
 * User: edubskiy
 * Date: 3/6/12
 * Time: 11:53 AM
 * Example: new Compiler_YuiCompressor('applicaion/media/css/' array('verbose' => true));
 *
 * java -jar closure-compiler.jar
 *  --js /Users/edubskiy/Dropbox/Development/Projects/Web/dubskiy.pro/application/media/js/application.js
 *  --js /Users/edubskiy/Dropbox/Development/Projects/Web/dubskiy.pro/application/media/js/bootstrap.min.js
 *  --js  /Users/edubskiy/Dropbox/Development/Projects/Web/dubskiy.pro/application/media/js/jquery-1.7.1.min.js
 *  --js_output_file /Users/edubskiy/Dropbox/Development/Projects/Web/dubskiy.pro/application/cache/compiled-by-google.js
 */

class Compiler_Minify extends Compiler
{
    protected $hashPrefix = 'minify-';

//    protected $hashFile = 'cache/css-yui.hash';
    protected $compiler = 'compilers/minify/min/lib';

    // empty DYLD_LIBRARY_PATH prevents crash
    protected $cmd = null;

    protected $minifyLibClass = 'Minify';
    protected $minifyCompilerClasses = array(
        'css'  => 'Minify_CSS',
        'js'   => 'JSMin',
        'html' => 'Minify_HTML'
    );
    protected $minifyMethod = 'minify';

    /**
     * @param $options null || array
     */
    public function __construct($options = array())
    {
        parent::__construct($options);

        $this->AttachCompiler();
    }

    public function AttachCompiler()
    {
        // setup include path
        set_include_path($this->compiler . PATH_SEPARATOR . get_include_path());

        $ext = '.php';
        $typeCompile = $this->options['type'];
        $compilerClass = $this->minifyCompilerClasses[$typeCompile];

        $this->cmd = $compilerClass;

        $pathCompiler = join('/', explode("_", $compilerClass));

        require_once($this->minifyLibClass . $ext);
        require_once($pathCompiler . $ext);

        return true;
    }

    public function CompilePredefinedData($compileData)
    {
        // Minification
        return call_user_func(
            array($this->cmd, 'minify'),
            $compileData
        );
    }

    public function Compile()
    {
        if ( ! $this->PreCompile())
        {
            return false;
        }

        $compileExt = $this->GetCompileExt();
        $oldCompiledFile = $this->cache . $this->hashOldSum . $compileExt;

        // Set output file name
        $this->lastCompressedFile = $this->hashNewSum . $compileExt;

//        $compileData = $this->GetCompileData();

        $combinedData = call_user_func(
            array($this->minifyLibClass, 'combine'),
            $this->mediaCollection
        );

        // Minification
        $minifiedData = call_user_func(
            array($this->cmd, 'minify'),
            $combinedData
        );

        // Save data
        $isSavedCompiledOk = file_put_contents(
            $this->cache . $this->lastCompressedFile,
            $minifiedData
        );

        // File wasn't save correctly
        if (empty($isSavedCompiledOk))
        {
            $this->lastCompressedFile = $oldCompiledFile;
            return false;
        }

        // Removing old compressed file
        if (file_exists($oldCompiledFile))
        {
            unlink($oldCompiledFile);
        }

        $this->isCompiled = true;

        return $this;
    }
};