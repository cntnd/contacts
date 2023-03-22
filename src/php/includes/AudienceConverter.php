<?php

use CSVDB\Converter;

class AudienceConverter implements Converter
{

    public function convert(iterable $records): array
    {
        $results = [];
        foreach ($records as $record) {
            $results[] = Audience::of($record);
        }
        return $results;
    }
}