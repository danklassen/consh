<?php
interface DeployStrategy
{
    public function deploy();

    public function cleanup();

    public function prepare();
}