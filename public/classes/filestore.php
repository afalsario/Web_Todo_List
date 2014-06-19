<?php

class Filestore {

    public $filename = '';

    function __construct($filename = '')
    {
        $this->filename = $filename;
    }

    /**
     * Returns array of lines in $this->filename
     */
    function read_lines()
    {
        if(is_readable($this->filename) && filesize($this->filename) > 0)
        {
            $handle = fopen($this->filename, 'r');
            $contents = trim(fread($handle, filesize($this->filename)));
            $list = explode(PHP_EOL, $contents);
        fclose($handle);
        return $list;
        }
        else
        {
            return array();
        }
    }

    /**
     * Writes each element in $array to a new line in $this->filename
     */
    function write_lines($array)
    {
        $handle = fopen($this->filename, 'w');
        foreach ($array as $value)
        {
            fwrite($handle, $value . PHP_EOL);
        }
        fclose($handle);
    }

    /**
     * Reads contents of csv $this->filename, returns an array
     */
    function read_csv()
    {

    }

    /**
     * Writes contents of $array to csv $this->filename
     */
    function write_csv($array)
    {

    }

}
