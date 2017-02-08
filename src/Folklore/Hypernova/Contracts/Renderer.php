<?php

namespace Folklore\Hypernova\Contracts;

interface Renderer
{
    public function addJob($name, $job);

    public function render();
}
