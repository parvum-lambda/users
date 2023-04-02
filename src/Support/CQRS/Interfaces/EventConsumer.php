<?php

namespace Support\CQRS\Interfaces;

interface EventConsumer
{
    public function handle(DataSet $dataSet);
}
