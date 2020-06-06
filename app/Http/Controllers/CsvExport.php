<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CsvExport extends Controller {
    protected $statusCode = 200;

    /**
     * Converts the user input into a CSV file and streams the file back to the user
     */
    public function convert(Request $request)
    {
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
        return $this
            ->setStatusCode(422)
            ->respondWithError($message);
    }

    /**
     * respond with error.
     *
     * @param $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondWithError($message)
    {
        return $this->respond([
            'error' => [
                'message'     => $message,
                'status_code' => $this->getStatusCode(),
            ],
        ]);
    }

    /**
     * set the status code.
     *
     * @param [type] $statusCode [description]
     *
     * @return statuscode
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * get the status code.
     *
     * @return statuscode
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Respond.
     *
     * @param array $data
     * @param array $headers
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function respond($data, $headers = [])
    {
        return response()->json($data, $this->getStatusCode(), $headers);
    }
}
