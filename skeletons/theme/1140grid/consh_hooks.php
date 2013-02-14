<?php
class Theme1140gridHooks
{
    public function post_install($theme_skeleton, $target)
    {
        $src = $theme_skeleton->path;
        shell_exec("cp -dpr {$src}/source_files/helpers/ " . C5_DIR. "/");
        shell_exec("cp -dpr {$src}/source_files/libraries/ " . C5_DIR. "/");
        shell_exec("rm -rf {$target}/source_files/");
    }
}
?>