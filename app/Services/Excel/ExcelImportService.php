<?php

namespace App\Services\Excel;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

class ExcelImportService
{
    /**
     * Import data from Excel file
     * 
     * @param mixed $file Uploaded file
     * @param string $modelClass Model class that implements ImportableModel
     * @param array $additionalData Additional data to merge with each row
     * @return array ['success' => int, 'failed' => int, 'errors' => array]
     */
    public function import($file, string $modelClass, array $additionalData = []): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        DB::beginTransaction();
        
        try {
            // Read Excel file
            $sheets = Excel::toArray([], $file);
            $data = $sheets[0] ?? []; // Get first sheet (Data Import)
            
            if (empty($data)) {
                throw new Exception('File Excel kosong atau tidak valid.');
            }
            
            $headers = array_shift($data); // Remove header row
            $headers = array_map(function($header) {
                // Bersihkan asterisk (*) dan spasi — toleran terhadap format "Shift *"
                return mb_strtolower(trim(str_replace('*', '', (string)$header)));
            }, $headers);
            
            $mapping = $modelClass::getImportMapping();
            $rules = $modelClass::getImportRules();
            
            foreach ($data as $rowIndex => $row) {
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }
                
                try {
                    // Map Excel columns to database fields
                    $rowData = $this->mapRowToFields($row, $headers, $mapping);
                    
                    // Merge additional data
                    $rowData = array_merge($rowData, $additionalData);
                    
                    // Transform if needed
                    if (method_exists($modelClass, 'transformImportRow')) {
                        $rowData = $modelClass::transformImportRow($rowData);
                    }
                    
                    // Validate
                    $validator = Validator::make($rowData, $rules);
                    
                    if ($validator->fails()) {
                        $results['failed']++;
                        $results['errors'][] = "Baris " . ($rowIndex + 2) . ": " . $validator->errors()->first();
                        continue;
                    }
                    
                    // Insert to database
                    // Jika model punya method saveImportRow() (contoh: master-detail),
                    // panggil itu. Jika tidak, gunakan create() standar.
                    if (method_exists($modelClass, 'saveImportRow')) {
                        $modelClass::saveImportRow($rowData);
                    } else {
                        $modelClass::create($rowData);
                    }
                    $results['success']++;
                    
                } catch (Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = "Baris " . ($rowIndex + 2) . ": " . $e->getMessage();
                }
            }
            
            if ($results['success'] > 0) {
                DB::commit();
            } else {
                DB::rollBack();
            }
            
            return $results;
            
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Map Excel row to database fields
     */
    private function mapRowToFields(array $row, array $headers, array $mapping): array
    {
        $result = [];

        foreach ($mapping as $excelCol => $dbField) {
            // Normalkan mapping key ke lowercase+trim untuk matching toleran
            $normalizedKey = mb_strtolower(trim($excelCol));
            $index = array_search($normalizedKey, $headers);

            if ($index !== false && isset($row[$index])) {
                $value = $row[$index];

                // Skip empty values
                if ($value !== null && $value !== '') {
                    $result[$dbField] = $value;
                }
            }
        }

        return $result;
    }
}
