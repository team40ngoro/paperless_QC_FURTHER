<?php
namespace App\Http\Controllers\Traits;

trait HasNoDataHtmlResponse
{
    protected function buildNoDataHtmlResponse(string $message, array $filterInfo = [])
    {
        $filterHtml = '';
        if (!empty($filterInfo)) {
            $filterHtml = '<div class="filter-info"><h4>Filter yang digunakan:</h4><ul>'
                . collect($filterInfo)->map(fn ($f) => "<li>{$f}</li>")->implode('')
                . '</ul></div>';
        }

        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Data Tidak Ditemukan</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 40px; text-align: center; background-color: #f8f9fa; }
                .container { max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .icon { font-size: 64px; color: #ffc107; margin-bottom: 20px; }
                h1 { color: #495057; margin-bottom: 20px; }
                .message { color: #6c757d; margin-bottom: 30px; font-size: 16px; }
                .filter-info { background: #e9ecef; padding: 15px; border-radius: 5px; margin: 20px 0; text-align: left; }
                .filter-info h4 { margin: 0 0 10px 0; color: #495057; }
                .filter-info ul { margin: 0; padding-left: 20px; }
                .btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 20px; }
            </style></head><body>
            <div class="container">
                <div class="icon">⚠️</div>
                <h1>Data Tidak Ditemukan</h1>
                <p class="message">' . $message . '</p>' . $filterHtml . '
                <p style="color: #6c757d; font-size: 14px;">Silakan coba dengan filter yang berbeda atau pastikan data sudah tersedia di sistem.</p>
                <a href="javascript:window.close()" class="btn">Tutup</a>
            </div></body></html>';

        return response($html)->header('Content-Type', 'text/html');
    }
}