<?php

class ThemeSkeleton
{
    public function __construct($path)
    {
        $this->path = $path;
        $this->loadData();
    }

    public function getName()
    {
        if (!$this->name) {
            $this->loadData();
        }
        return $this->name;
    }

    protected function loadData()
    {
        $file = $this->path . "/description.txt";
        $text = file_get_contents($file);
        if (!$text) {
            return false;
        }
        $data = explode("\n", $text);
        $this->name = $data[0];
        $this->description = $data[1];
    }

    /**
     * output a list of templates avilable to base off of
     *
     * @return void
     */
    public static function showThemeOptions()
    {
        $themes = array();
        if ($dh = opendir(CONSH_SKELETON_DIR . 'theme/')) {
            while (($file = readdir($dh)) !== false) {
                if (filetype(CONSH_SKELETON_DIR . "theme/" . $file) == 'dir' && substr($file, 0, 1) != '.' && $file != '..') {
                    $themes[$file] = new ThemeSkeleton(CONSH_SKELETON_DIR . "theme/" . $file);
                }
            }
            closedir($dh);
        }

        if ($dh = opendir(CONSH_SKELETON_LOCAL_DIR . 'theme/')) {
            while (($file = readdir($dh)) !== false) {
                if (filetype(CONSH_SKELETON_LOCAL_DIR . "theme/" . $file) == 'dir' && substr($file, 0, 1) != '.' && $file != '..') {
                    $themes[$file] = new ThemeSkeleton(CONSH_SKELETON_LOCAL_DIR . "theme/" . $file);
                }
            }
            closedir($dh);
        }

        output(str_pad('KEY', 10) . str_pad("NAME", 28) . "DESCRIPTION");
        foreach ($themes as $key =>$theme) {
            output(str_pad($key, 10) . str_pad($theme->name, 28) . $theme->description);
        }
    }

}