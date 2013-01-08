<?php

class ThemeSkeleton
{
    public function __construct($path, $handle)
    {
        $this->path = $path;
        $this->handle = $handle;
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

    public function install($out_name = null)
    {
        Hook::fire('before_theme_install', $this->handle, $out_name);
        $target_folder = C5_DIR . "/themes/" . $this->handle;
        if ($out_name) {
            $target_folder = C5_DIR . "/themes/" . $out_name;
        }
        $skeleton_folder = $this->path;
        shell_exec("cp -dpr {$skeleton_folder} $target_folder");
        if (file_exists($skeleton_folder . "/consh_hooks.php")) {
            include $skeleton_folder . "/consh_hooks.php";
            $class_name = camelize('theme_' . $this->handle . "_hooks");
            $theme_hooks = new $class_name();
            if (method_exists($theme_hooks, 'post_install')) {
                $theme_hooks->post_install($this);
            }
            shell_exec("rm {$target_folder}/consh_hooks.php");
        }
        Hook::fire('after_theme_install', $this->handle, $out_name, $target_folder);
        return $target_folder;
    }

    /**
     * output a list of templates avilable to base off of
     *
     * @return void
     */
    public static function showThemeOptions()
    {
        $themes = ThemeSkeleton::getThemeOptions();

        output(str_pad('KEY', 10) . str_pad("NAME", 28) . "DESCRIPTION");
        foreach ($themes as $key =>$theme) {
            output(str_pad($theme->handle, 10) . str_pad($theme->name, 28) . $theme->description);
        }
    }

    public static function getThemeOptions()
    {
        $themes = array();
        if ($dh = opendir(CONSH_SKELETON_DIR . 'theme/')) {
            while (($file = readdir($dh)) !== false) {
                if (filetype(CONSH_SKELETON_DIR . "theme/" . $file) == 'dir' && substr($file, 0, 1) != '.' && $file != '..') {
                    $themes[$file] = new ThemeSkeleton(CONSH_SKELETON_DIR . "theme/" . $file, $file);
                }
            }
            closedir($dh);
        }

        if ($dh = opendir(CONSH_SKELETON_LOCAL_DIR . 'theme/')) {
            while (($file = readdir($dh)) !== false) {
                if (filetype(CONSH_SKELETON_LOCAL_DIR . "theme/" . $file) == 'dir' && substr($file, 0, 1) != '.' && $file != '..') {
                    $themes[$file] = new ThemeSkeleton(CONSH_SKELETON_LOCAL_DIR . "theme/" . $file, $file);
                }
            }
            closedir($dh);
        }
        ksort($themes);
        return $themes;
    }

    public static function getThemeByHandle($handle)
    {
        $themes = ThemeSkeleton::getThemeOptions();
        if (array_key_exists($handle, $themes)) {
            return $themes[$handle];
        } else {
            output(sprintf("%s theme could not be found", $handle), 'error');
            return false;
        }

    }

}