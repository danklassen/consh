<?php
/**
 * output help
 *
 * @author  Dan Klassen <dan@triplei.ca>
 * @package Commands
 * @since 0.2
 */
class Help extends Command
{

    public function __construct()
    {
        $this->name = "Help";
        $this->description = "Display help about a command";
        $this->help = "Run consh help <command> to learn more about the command";
    }

    public function run($options = array())
    {
        if (count($options) != 1) {
            output($this->help);
            return true;
        }

        $command_str = array_shift($options);
        $command = convertCommandToObject($command_str);
        output("Help for: consh " . $command_str , 'success');
        output($command->help);
        if (!empty($command->parameters)) {
            output("");
            output("Command Paramters:");
            if (is_array($command->parameters)) {
                foreach ($command->parameters as $name => $description) {
                    output(str_pad($name, 20) . $description);
                }
            } else {
                output($command->parameters);
            }
        }
    }
}