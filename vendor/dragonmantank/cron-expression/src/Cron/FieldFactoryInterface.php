<?php

namespace Cron;

interface FieldFactoryInterface
{
    public function getField(int $position): FieldInterface;
}
