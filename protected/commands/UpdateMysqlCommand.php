<?php
class UpdateMysqlCommand extends ConsoleCommand
{
    public $config;
    public $success;

    public function run($args)
    {

        $version = $this->config['global']['version'];

        echo $version;

    }
}
