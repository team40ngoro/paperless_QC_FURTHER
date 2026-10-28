<?php

namespace App\Services\Excel\Contracts;

interface ImportableModel
{
    /**
     * Get the column mapping for Excel import
     * 
     * @return array ['Excel Column Name' => 'database_field_name']
     */
    public static function getImportMapping(): array;
    
    /**
     * Get validation rules for import
     * 
     * @return array ['field_name' => 'validation_rule']
     */
    public static function getImportRules(): array;
    
    /**
     * Get master data fields for dropdown in Excel template
     * 
     * @return array [
     *   'field_name' => [
     *     'label' => 'Display Label',
     *     'source' => ModelClass::class,  // or 'options' => []
     *     'label_column' => 'name',
     *     'value_column' => 'id',
     *     'list_source' => "='Master Data'!A2:A100",
     *   ]
     * ]
     */
    public static function getMasterDataFields(): array;
    
    /**
     * Transform row data before import (optional)
     * 
     * @param array $row
     * @return array
     */
    public static function transformImportRow(array $row): array;
}
