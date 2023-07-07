<?php

interface EnumInterface
{
    public static function from(int|string $label): static;
}