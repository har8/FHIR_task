<?php

namespace App\Contracts;

interface IFhirService
{
    public function prepareExport();
    public function getBulkStatus();
    public function fetchData(string $key);
}