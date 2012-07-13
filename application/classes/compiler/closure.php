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

class Compiler_Closure extends Compiler
{
    protected $hashPrefix = 'gclo-';

//    protected $hashFile = 'cache/css-yui.hash';
    protected $compiler = 'compilers/closure/closure-compiler.jar';

    // empty DYLD_LIBRARY_PATH prevents crash
    protected $cmd = "export DYLD_LIBRARY_PATH=''; java -jar ";

    /**
     * @param $options null || array
     */
    public function __construct($options = array())
    {
        $this->options['type'] = 'js';

        parent::__construct($options);
    }

    public function AddCompileFiles()
    {
        $jsFilesParam = '';
        $compileType = $this->GetCompileType();
        foreach($this->mediaCollection as $mediaCollection)
        {
            $jsFilesParam .= " --{$compileType} {$mediaCollection} ";
        }
        return $jsFilesParam;
    }

    public function Compile()
    {
        if ( ! $this->PreCompile())
        {
            return false;
        }

        $compileExt = $this->GetCompileExt();

        // Set output file name
        $this->lastCompressedFile = $this->hashNewSum . $compileExt;

        // java -jar %{fullpath}yuicompressor.jar
        $this->cmd .= $this->compiler;

        // Add compressing files
        $this->cmd .= $this->AddCompileFiles();

        // Add file output file path
        $this->cmd .= "--js_output_file {$this->cache}{$this->lastCompressedFile} ";

        exec($this->cmd . ' 2>&1');

        $oldCompiledFile = $this->cache . $this->hashOldSum . $compileExt;

        // Removing old compressed file
        if (file_exists($oldCompiledFile))
        {
            unlink($oldCompiledFile);
        }

        $this->isCompiled = true;

        return $this;
    }
};