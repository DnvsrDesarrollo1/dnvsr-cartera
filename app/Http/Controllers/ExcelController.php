<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExcelController extends Controller
{
    public function importDifferiments(Request $request)
    {
        $validatedData = $request->validate([
            'file-differiments' => 'required|max:10240',
            'separator-differiments' => 'required|string|max:1',
        ], [
            'file-differiments.required' => 'El archivo es requerido',
            'file-differiments.max' => 'El archivo no debe superar los 10MB',
            'separator-differiments.required' => 'El separador es requerido',
        ]);

        $file = $request->file('file-differiments');
        $separator = $validatedData['separator-differiments'];

        $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
        $filePath = 'storage/uploads/' . $fileName;
        $file->move(public_path('storage/uploads'), $fileName);

        $rows = array_map('str_getcsv', file($filePath));

        //return $rows;

        foreach ($rows as $row) {
            $array[] = explode($separator, $row[0]);
        }

        //return $array;

        $collection = new \Illuminate\Database\Eloquent\Collection();

        foreach ($array as $key => $value) {
            $collection->push(collect((object)[
                'idepro' => $value[0],
                'capital' => $value[1],
                'interes' => $value[2],
                'cuotas' => $value[3],
            ]));
        }

        //return $collection;

        try {
            $data = new \Illuminate\Database\Eloquent\Collection();

            foreach ($collection as $key => $value) {

                $startIndex = \App\Models\Plan::where('idepro', $value['idepro'])->where('estado', 'ACTIVO')->orderBy('fecha_ppg', 'desc')->first();

                if ($startIndex != null) {
                    $cap = round((float)$value['capital'] / $value['cuotas'], 8);
                    $int = round((float)$value['interes'] / $value['cuotas'], 8);

                    for ($i = 1; $i <= $value['cuotas']; $i++) {
                        $data->push(collect((object)[
                            'idepro' => $value['idepro'],
                            'nro_cuota' => $i + ($startIndex->prppgnpag ?? 0),
                            'capital' => $cap,
                            'interes' => $int,
                            'vencimiento' => date('Y/m/15', strtotime(($startIndex->fecha_ppg ?? now()) . ' + ' . $i . ' months')),
                            'estado' => 'ACTIVO'
                        ]));
                    }
                }
            }

            foreach ($data as $d) {
                if ($d != null) {
                    \App\Models\Helper::create([
                        'idepro' => $d['idepro'],
                        'indice' => $d['nro_cuota'],
                        'capital' => $d['capital'],
                        'interes' => $d['interes'],
                        'vencimiento' => $d['vencimiento'],
                        'estado' => $d['estado'],
                        'user_id' => auth()->user()->id,
                    ]);
                }
            }

            //return $data;

            return redirect()->route('importaciones')->with('successD', 'Se lograron importar ' . count($rows) . ' registros para diferimentos.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error during differimento generation: ' . $e->getMessage());

            return $e;

            return redirect()->route('importaciones')->with('errorD', 'Motivo de la falla: ' . $e->getMessage());
        }
    }

    public function importModelCSV(Request $request)
    {
        $validatedData = $request->validate([
            'file' => 'required|max:10240',
            'separator' => 'required|string|max:1',
            'title' => 'nullable|string|max:255',
            'model' => 'required|string|max:255',
        ], [
            'file.required' => 'El archivo es requerido',
            'file.max' => 'El archivo no debe superar los 10MB',
            'separator.required' => 'El separador es requerido',
            'title.max' => 'El título no puede superar los 255 caracteres',
            'model.required' => 'El modelo es requerido',
        ]);

        $file = $request->file('file');
        $dynaModel = 'App\\Models\\' . Str::singular(ucwords($validatedData['model']));
        $separator = $validatedData['separator'];

        $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
        $filePath = 'storage/uploads/' . $fileName;
        $file->move(public_path('storage/uploads'), $fileName);

        $rows = array_map('str_getcsv', file($filePath));

        $collection = new \Illuminate\Database\Eloquent\Collection();

        foreach ($rows as $row) {
            $array = explode($separator, $row[0]);
            $collection->push((object) $array);
        }

        // Reemplazar la obtención de headers con:
        $tableName = (new $dynaModel)->getTable();
        $headers = \Schema::getColumnListing($tableName);

        // Filtrar las columnas no deseadas
        $headers = collect($headers)->reject(function ($item) {
            return in_array($item, [
                'id',
                'cod_promotor',
                'cod_cristorey',
                'cod_fondesif',
                'cod_smp',
                'created_at',
                'updated_at'
            ]);
        });

        $headers = collect((object)$headers->flatten());


        // DELETE 'id', 'created_at', 'updated_at' FROM $HEADERS
        $headers = $headers->reject(function ($item, $key) {
            return in_array($item, ['id', 'cod_promotor', 'cod_cristorey', 'cod_fondesif', 'cod_smp', 'created_at', 'updated_at']);
        });

        $headers = collect((object)$headers->flatten());

        //return $headers;

        $data = new \Illuminate\Database\Eloquent\Collection();

        foreach ($collection as $item) {
            $row = new \stdClass();
            foreach ($headers as $index => $header) {
                $value = isset($item->{$index}) ? $item->{$index} : null;

                // Check if the value is a date string and convert it
                if ($value && $this->isDateString($value)) {
                    $value = $this->convertToMySQLDate($value);
                }

                // Handle empty strings for numeric fields
                if ($value === '') {
                    $value = null;
                }

                $row->{$header} = $value;
            }
            $row->{'user_id'} = auth()->user()->id;
            $data->push($row);
        }

        $updt = 0;

        try {
            foreach ($data as $d) {
                if ($dynaModel::where('idepro', $d->idepro)->exists())
                {
                    $dynaModel::where('idepro', $d->idepro)->update((array)$d);
                } else {
                    $dynaModel::create((array)$d);
                }
            }

            File::delete($filePath);

            return redirect()->route('importaciones')->with('success', 'Se lograron importar ' . count($data) . ' registros, actualizados: ' . $updt);
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error('Error during data import: ' . $e->getMessage());

            // Delete the uploaded file
            File::delete($filePath);

            // Redirect back with an error message
            return redirect()->route('importaciones')->with('error', 'Motivo de la falla: ' . $e->getMessage());
        }
    }

    public function exportModel($model)
    {
        $dynaModel = 'App\\Models\\' . Str::singular(ucwords($model));
        $fileName = 'exportacion_' . $model . '_' . uniqid() . '.xlsx';
        $data = $dynaModel::all();

        $headers = collect($data->first())->keys();
        $headers = $headers->combine($headers);
        $data->prepend(collect($headers));

        return $this->generateExcelFile($data, $model, $fileName);
    }

    public function exportCollection(Request $request, $title = 'datos')
    {
        $fileName = 'exportacion_' . uniqid() . '.xlsx';
        $idepro = $request->input('idepro');

        $userId = auth()->user()->id;

        $data = $this->generatePlanData($request);
        $diferimento = $this->generateDiferimentoIfNeeded($request, $data);

        $this->deactivateExistingRecords($idepro, $userId);
        $this->createNewRecords($data, $diferimento, $request, $userId);

        $headers = $this->prepareHeaders($data);
        $data->prepend(collect($headers));

        $excelFilePath = $this->generateExcelFile($data, $title, $fileName);

        return response()->download($excelFilePath)->deleteFileAfterSend(true);
    }

    private function prepareHeaders($data)
    {
        $headers = collect($data->first())->keys();
        return $headers->combine($headers);
    }

    private function generateExcelFile($data, $sheetTitle, $fileName)
    {
        $this->ensureExportDirectoryExists();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $array = $this->prepareDataArray($data);
        $this->populateSpreadsheet($sheet, $array);

        $sheet->setTitle($sheetTitle);
        $this->styleHeaderRow($sheet, count($array[0]));

        $writer = new Xlsx($spreadsheet);
        $filePath = public_path('storage/exports/' . $fileName);
        $writer->save($filePath);

        $spreadsheet->disconnectWorksheets();

        return $filePath;
        //response()->download($filePath)->deleteFileAfterSend(true);
    }

    private function ensureExportDirectoryExists()
    {
        $directory = public_path('storage/exports');
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
    }

    private function prepareDataArray($data)
    {
        return $data->map(function ($row) {
            return collect($row)->flatten()->toArray();
        })->toArray();
    }

    private function populateSpreadsheet($sheet, $array)
    {
        foreach ($array as $rowIndex => $row) {
            foreach ($row as $columnIndex => $value) {
                $coordinates = (string) ($this->numberToLetters($columnIndex + 1) . $rowIndex + 1);
                $sheet->setCellValue($coordinates, $value ?? '');
            }
        }
    }

    private function styleHeaderRow($sheet, $columnCount)
    {
        $lastColumn = $this->numberToLetters($columnCount);
        $headerRange = "A1:{$lastColumn}1";

        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('cae9ff');

        $sheet->getStyle($headerRange)->getFont()->setBold(true);
    }

    private function numberToLetters($number): string
    {
        $letters = '';
        while ($number > 0) {
            $number--;
            $letters = chr(65 + ($number % 26)) . $letters;
            $number = intdiv($number, 26);
        }
        return $letters;
    }

    private function isDateString($string)
    {
        // This regex pattern matches common date formats
        $datePattern = '/^(\d{2}\/\d{2}\/\d{4}|\d{4}-\d{2}-\d{2}|\d{2}-\d{2}-\d{4}|\d{4}\/\d{2}\/\d{2})$/';
        return preg_match($datePattern, $string);
    }

    private function convertToMySQLDate($dateString)
    {
        $formats = ['d/m/Y', 'Y-m-d', 'd-m-Y', 'Y/m/d'];

        foreach ($formats as $format) {
            try {
                return \Carbon\Carbon::createFromFormat($format, $dateString)->format('Y-m-d');
            } catch (\Exception $e) {
                continue;
            }
        }

        // If all formats fail, return null
        return null;
    }
}
