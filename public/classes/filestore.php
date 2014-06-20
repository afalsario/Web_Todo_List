<?php

class Filestore {

    public $filename = '';
    public $is_csv = FALSE;

    function __construct($filename = '')
    {
        $this->filename = $filename;
        $ext = substr($filename, -3);
        if($ext == 'csv')
        {
            $this->is_csv = TRUE;
        }
    }

    public function read()
    {
        if($this->is_csv)
        {
            return $this->read_csv();
        }
        else
        {
            return $this->read_lines();
        }
    }

     public function write($array)
    {
        if($this->is_csv)
        {
            $this->write_csv($array);
        }
        else
        {
            $this->write_lines($array);
        }
    }

    /**
     * Returns array of lines in $this->filename
     */
    private function read_lines()
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
    private function write_lines($array)
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
    private function read_csv()
    {
        $handle = fopen($this->filename, 'r');
        $array = [];
        while(!feof($handle))
        {
            $row = fgetcsv($handle);
            if(is_array($row))
            {
                $array[] = $row;
            }
        }
        fclose($handle);
        return $array;
    }

    /**
     * Writes contents of $array to csv $this->filename
     */
    private function write_csv($array)
    {
        if(is_writeable($this->filename))
        {
            $handle = fopen($this->filename, 'w');
            foreach ($array as $fields)
            {
                fputcsv($handle, $fields);
            }
            fclose($handle);
        }
    }

}
