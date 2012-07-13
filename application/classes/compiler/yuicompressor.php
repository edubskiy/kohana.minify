<?php
/**
 * YUI Compressor Loader (using for compressing CSS)
 * User: edubskiy
 * Date: 3/6/12
 * Time: 11:53 AM
 * Example: new Compiler_YuiCompressor('applicaion/media/css/' array('verbose' => true));
 */

class Compiler_Yuicompressor extends Compiler
{
    protected $hashPrefix = 'yui-';

//    protected $hashFile = 'cache/css-yui.hash';
    protected $compiler = 'compilers/yuicompressor/build/yuicompressor.jar';

    // empty DYLD_LIBRARY_PATH prevents crash
    protected $cmd = "export DYLD_LIBRARY_PATH=''; java -jar ";

    /**
     * @param $options null || array
     */
    public function __construct($options = array())
    {
        parent::__construct($options);
    }

//    public function GetCompileData()
//    {
//        $compileData = '';
//        foreach ($this->mediaCollection as $fileInCollection)
//        {
//            $compileData .= file_get_contents($fileInCollection);
//        }
//        return $compileData;
//    }

    public function FilterCompileData(&$compileData)
    {
        $compileData = str_replace("../img/", "../media/img/", $compileData);
        $compileData = str_replace("../fonts/", "../media/fonts/", $compileData);
        return true;
    }

    public function CreateTmpAllInOneFile($compileData)
    {
        $tmpFilePath = $this->GetTmpFilePath();

        // put everything to Example: /application/cache/css_cached
        file_put_contents($tmpFilePath, $compileData);

        return $tmpFilePath;
    }

    public function Compile()
    {
        if ( ! $this->PreCompile())
        {
            return false;
        }

        $compileExt = $this->GetCompileExt();

        // All content combined from all files in compile directory
        $compileData = $this->GetCompileData();

        $this->FilterCompileData($compileData);

        $tmpFilePath = $this->CreateTmpAllInOneFile($compileData);

        // Set output file name
        $this->lastCompressedFile = $this->hashNewSum . $compileExt;

        // java -jar %{fullpath}yuicompressor.jar
        $this->cmd .= $this->compiler;

        // Add file output file path
        $this->cmd .= ' -o ' . $this->cache . $this->lastCompressedFile . " ";

        // Add compressing file
        $this->cmd .= $tmpFilePath;

        exec($this->cmd . ' 2>&1'); // ' 2>&1'

        // Removing tmp file
        unlink($tmpFilePath);

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