<?php
/**
 * Created by JetBrains PhpStorm.
 * User: edubskiy
 * Date: 3/12/12
 * Time: 3:55 PM
 * ! APPPATH must be defined to use absolute path, otherwise relative will be used !
 */

class Compiler
{
    // Settings for main Compiler

    // Application directory location (relative or absolute - It's up to you!)
    protected  $app  = 'application/';

    // Name for tmp file which will contain all merged data (will be removed automatically after compilation)
    protected $tmpFile  = 'tmp-for-compiling';

    // Cache files location
    protected $cache    = 'cache/';

    // Default Compiling Options
    protected $options = array(
        'charset' => 'utf-8',
        'verbose' => false, // verbose mode
        'type' => 'js', // compile type
    );

    // Collection of media files to compile (must be added externally)
    protected $mediaCollection = array();

    // This properties must be extended in Child Classes (Yui, Closure etc)
    protected $compiler = 'unknown';
    protected $cmd = 'unknown';
    protected $hashPrefix = 'unknown';

    // Contains full path to last compressed file in this compile type to included in a View
    public $lastCompressedFile = '';

    protected $hashNewSum = '';
    protected $hashOldSum = '';
    protected $hashFile = '';
    protected $hashExt = '.hash';

    // for debug message
    public $isCompiled = false;

    public function __construct($options = array())
    {
        // Convert paths to match application location
        $this->cache    = $this->app . $this->cache;
        $this->compiler = $this->app . $this->compiler;

        foreach($options as $type => $option)
        {
            $this->SetOption($type, $option);
        }

        // path to hash file wich contains last hash for compressed files
        $this->hashFile = $this->GetHashFilePath();

        // remembering last hash sum from file
        if (file_exists($this->hashFile))
        {
            $this->hashOldSum = file_get_contents($this->hashFile);
        }

        // setting name for last compressed file to grab in case of non compile
        $this->lastCompressedFile = $this->hashOldSum . $this->GetCompileExt();
    }

    protected function GetHashFilePath()
    {
        return $this->cache . $this->hashPrefix . $this->GetCompileType() . $this->hashExt;
    }

    public function SetOption($type, $value)
    {
        $this->options[$type] = $value;
    }

    public function AddFiles($files)
    {
        foreach($files as $file)
        {
            array_push($this->mediaCollection, $file);
        }
        return true;
    }

    public function AddFile($file)
    {
        array_push($this->mediaCollection, $file);
        return true;
    }

    public function GetCompileType()
    {
        return $this->options['type'];
    }

    // By default extension of compressed file equals to compile type [css || js]
    public function GetCompileExt()
    {
        $extension = "." . $this->options['type'];
        return $extension;
    }

    public function GetNewHashSum()
    {
        $modifyTimeData = '';
        foreach ($this->mediaCollection as $fileInCollection)
        {
            $modifyTimeData .= filemtime($fileInCollection);
        }

        return sha1($modifyTimeData);
    }

    public function PreCompile()
    {
        $this->hashNewSum = $this->GetNewHashSum();

        if ($this->IsNoNeedInCompress())
        {
            return false;
        }

        return true;
    }

    public function IsNoNeedInCompress()
    {
        // Saving new hashsum
        if ($this->hashOldSum != $this->hashNewSum)
        {
            file_put_contents($this->hashFile, $this->hashNewSum);
            return false;
        }

        // If only hash exists but compressed file doesn't
        if ($this->hashOldSum)
        {
            $oldCompressedFile = $this->cache . $this->hashOldSum . $this->GetCompileExt();

            if ( ! file_exists($oldCompressedFile))
            {
                $this->hashNewSum = $this->hashOldSum;

                // Prevent unlinking old file
                $this->hashOldSum = null;
                return false;
            }
        }

        return true;
    }

    public function GetTmpFilePath()
    {
        return $this->cache . $this->tmpFile . $this->GetCompileExt();
    }

    public function GetCompileData()
    {
        $compileData = '';
        foreach ($this->mediaCollection as $fileInCollection)
        {
            $compileData .= file_get_contents($fileInCollection);
        }
        return $compileData;
    }
}