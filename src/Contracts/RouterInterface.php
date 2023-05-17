<?php

interface RouterInterface
{
    public function getContent() : mixed;

    public function build() : self;
}
