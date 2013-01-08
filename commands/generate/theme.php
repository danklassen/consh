<?php
/**
 * Generates a theme from a skeleton
 *
 *
 * @author    Dan Klassen <dan@triplei.ca>
 * @package Commands
 * @since 0.2
 */
class GenerateTheme extends Command
{

    /**
     * sets the name, description, and help
     */
    public function __construct()
    {
        parent::__construct();
        $this->name = "Generate:Theme";
        $this->description = "Generates a skeleton theme";
        $this->help = "Generate a blank theme";
    }

    /**
     * does the magic
     *
     * @param array $options not used at all
     *
     * @return boolean
     */
    public function run($options = array())
    {
        if (count($options) == 0) {
            output("Available Theme Options:");
            ThemeSkeleton::showThemeOptions();
            return false;
        }
        $handle = array_shift($options);
        $output = array_shift($options);
        $theme = ThemeSkeleton::getThemeByHandle($handle);
        if ($theme) {
            $theme->install($output);
        }
        return true;
    }

}