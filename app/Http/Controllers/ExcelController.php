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

        //return $collection;

        $data = $dynaModel::first();
        $headers = collect($data->first())->keys();

        // DELETE 'id', 'created_at', 'updated_at' FROM $HEADERS
        $headers = $headers->reject(function ($item, $key) {
            return in_array($item, ['id', 'cod_promotor', 'cod_cristorey', 'cod_fondesif', 'cod_smp', 'created_at', 'updated_at']);
        });

        $headers = collect((object)$headers->flatten());

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
            $data->push($row);
        }

        // prepare $data to be created at $dynaModel's table

        $dataArray = $data->map(function ($item) {
            $itemArray = (array)$item;
            // Remove 'id' from the array if it exists
            unset($itemArray['id']);
            return $itemArray;
        })->toArray();

        try {
            $dynaModel::insert($dataArray);

            File::delete($filePath);

            return redirect()->route('importaciones')->with('success', 'Se lograron importar ' . count($dataArray) . ' registros');
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
        $userId = auth()->id();

        $data = $this->generatePlanData($request);
        $diferimento = $this->generateDiferimentoIfNeeded($request, $data);

        $this->deactivateExistingRecords($idepro, $userId);
        $this->createNewRecords($data, $diferimento, $request, $userId);

        $headers = $this->prepareHeaders($data);
        $data->prepend(collect($headers));

        $excelFilePath = $this->generateExcelFile($data, $title, $fileName);

        return response()->download($excelFilePath)->deleteFileAfterSend(true);
    }

    private function generatePlanData(Request $request)
    {
        return $this->generarPlan(
            $request->input('capital_inicial'),
            $request->input('gastos_judiciales'),
            $request->input('meses'),
            $request->input('taza_interes'),
            $request->input('seguro'),
            $request->input('correlativo'),
            $request->input('plazo_credito'),
            $request->input('fecha_inicio')
        );
    }

    private function generateDiferimentoIfNeeded(Request $request, $data)
    {
        if ($request->filled(['diff_cuotas', 'diff_capital', 'diff_interes'])) {
            return $this->generarDiferimento(
                $request->input('diff_cuotas'),
                $request->input('diff_capital'),
                $request->input('diff_interes'),
                $request->input('plazo_credito'),
                $data->last()->vencimiento
            );
        }
        return collect();
    }

    private function deactivateExistingRecords($idepro, $userId)
    {
        $models = [\App\Models\Helper::class, \App\Models\Plan::class, \App\Models\Readjustment::class];

        foreach ($models as $model) {
            $model::where('idepro', $idepro)->update([
                'estado' => 'INACTIVO',
                'user_id' => $userId
            ]);
        }
    }

    private function createNewRecords($data, $diferimento, Request $request, $userId)
    {
        $dynaModel = $request->input('correlativo') ? 'App\\Models\\Readjustment' : 'App\\Models\\Plan';
        $idepro = $request->input('idepro');

        foreach ($data as $d) {
            $dynaModel::create([
                'idepro' => $idepro,
                'fecha_ppg' => $d->vencimiento,
                'prppgnpag' => $d->nro_cuota,
                'prppgcapi' => $d->abono_capital,
                'prppginte' => $d->interes,
                'prppgsegu' => $d->seguro,
                'prppgotro' => $d->gastos_judiciales,
                'prppgtota' => $d->total_cuota,
                'estado' => 'ACTIVO',
                'user_id' => $userId,
            ]);
        }

        foreach ($diferimento as $d) {
            \App\Models\Helper::create([
                'idepro' => $idepro,
                'indice' => $d->nro_cuota,
                'capital' => $d->capital,
                'interes' => $d->interes,
                'vencimiento' => $d->vencimiento,
                'estado' => $d->estado,
                'user_id' => $userId,
            ]);
        }
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
