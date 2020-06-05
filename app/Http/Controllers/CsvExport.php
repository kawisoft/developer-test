<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExport extends Controller {
    /**
     * Converts the user input into a CSV file and streams the file back to the user
     */
    public function convert(Request $request)
    {
        if (! $request->has('payload')) {
            throw new \Exception('Request failed!');
        }

        $data = $request->payload;
        $header = $request->header;

        return new StreamedResponse(
            function () use ($header, $data) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, $header);

                foreach ($data as $row) {
                    fputcsv($handle, $row);
                }

                fclose($handle);
            },200, [
                'Content-type'        => 'text/csv',
                'Content-Disposition' => 'attachment; filename=data.csv'
            ]
        );
    }
}
