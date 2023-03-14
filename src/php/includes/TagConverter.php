<?php

use CSVDB\Converter;

class TagConverter implements Converter
{

    /**
     * Converts Csv records with "Tag:" prefix to bool.
     */
    public function convert(iterable $records): array
    {
        $results = [];
        foreach ($records as $record) {
            if (is_array($record)) {
                foreach (array_keys($record) as $array_key) {
                    $value = $record[$array_key];
                    $record[$array_key] = $value;

                    $has_tag = stripos($array_key, "Tag:");
                    $has_check = stripos($array_key, "Check:");
                    $has_prefix = false;
                    if ($has_tag !== false) {
                        $has_prefix = $has_tag;
                    }
                    if ($has_check !== false) {
                        $has_prefix = $has_check;
                    }
                    if ($has_prefix !== false && $has_prefix == 0) {
                        $record[$array_key] = ($value == "x") ? 1 : 0;
                    }
                }
            }
            $results[] = $record;
        }
        return $results;
    }
}