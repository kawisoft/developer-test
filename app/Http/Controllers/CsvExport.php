<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CsvExport extends Controller {
    /**
     * Converts the user input into a CSV file and streams the file back to the user
     */
    public function convert(Request $request)
    {
        if (! $request->has('payload')) {
            throw new \Exception('Request failed!');
        }

        $validation = Validator::make($request->all(), [
            'payload' => 'required|array',
            'header' => 'required|array'
        ]);

        if ($validation->fails()) {
            return $this->throwValidation($validation->messages()->first());
        }

        $data = $request->payload;
        $header = $request->header;

        return response()->streamDownload(
            function() use ($header, $data) {
                $handle = fopen('php://output', 'w');
                    fputcsv($handle, $header);

                    foreach ($data as $row) {
                        fputcsv($handle, $row);
                    }

                    fclose($handle);
            },
            'data.csv',
            ['Content-type' => 'text/csv']
        );
    }

    public function throwValidation($message)
    {
        return $this->setStatusCode(422)
            ->respondWithError($message);
    }
}
