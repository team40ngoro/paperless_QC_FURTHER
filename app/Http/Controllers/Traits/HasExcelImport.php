<?php

namespace App\Http\Controllers\Traits;

use App\Services\Excel\ExcelImportService;
use App\Services\Excel\ExcelTemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait HasExcelImport
{
    /**
     * Download Excel template with master data dropdowns
     */
    public function downloadTemplate()
    {
        try {
            $modelClass = $this->getImportModelClass();
            $filename = $this->getTemplateFilename();
            
            $templateService = new ExcelTemplateService();
            
            return $templateService->generateWithDropdowns($modelClass, $filename);
            
        } catch (\Exception $e) {
            Log::error('Template download error: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Gagal mengunduh template: ' . $e->getMessage());
        }
    }
    
    /**
     * Import data from Excel file
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:10240', // Max 10MB
        ]);
        
        try {
            $file = $request->file('file');
            $modelClass = $this->getImportModelClass();
            
            // Additional data (user_id, id_plan, etc.)
            $additionalData = $this->getAdditionalImportData();
            
            $importService = new ExcelImportService();
            $results = $importService->import($file, $modelClass, $additionalData);
            
            if ($results['success'] > 0) {
                $message = "Import berhasil: {$results['success']} data diimport";
                
                if ($results['failed'] > 0) {
                    $message .= ", {$results['failed']} data gagal";
                }
                
                return redirect()->back()->with([
                    'success' => $message,
                    'import_errors' => $results['errors']
                ]);
            } else {
                $firstError = !empty($results['errors']) ? ' (' . $results['errors'][0] . ')' : '';
                return redirect()->back()->with([
                    'error' => 'Import gagal: Tidak ada data yang berhasil diimport' . $firstError,
                    'import_errors' => $results['errors']
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }
    
    /**
     * Get the model class for import (must be implemented by controller)
     */
    abstract protected function getImportModelClass(): string;
    
    /**
     * Get the template filename (must be implemented by controller)
     */
    abstract protected function getTemplateFilename(): string;
    
    /**
     * Get additional data to merge with import (optional override)
     */
    protected function getAdditionalImportData(): array
    {
        return [
            'user_id' => auth()->id(),
            'id_plan' => auth()->user()->id_plan ?? null,
        ];
    }
}
