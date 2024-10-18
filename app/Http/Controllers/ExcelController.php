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
            return in_array($item, ['id', 'created_at', 'updated_at']);
        });

        $headers = collect((object)$headers->flatten());

        $data = new \Illuminate\Database\Eloquent\Collection();

        //consider that, $headers has {'col1', 'col2', 'col3'} and $collection has {'0' => 'value1', '1' => 'value2', '3' => 'value3'}
        //i need to sustitute '0' with 'col1', '1' with 'col2', '3' with 'col3' and so on the $data collection for export to excel file

        //return 'cols ' . count($headers) . ' rows ' . count($collection);

        foreach ($collection as $item) {
            $row = new \stdClass();
            foreach ($headers as $index => $header) {
                $value = isset($item->{$index}) ? $item->{$index} : null;

                // Check if the value is a date string and convert it
                if ($value && $this->isDateString($value)) {
                    $value = $this->convertToMySQLDate($value);
                }

                $row->{$header} = $value;
            }
            $data->push($row);
        }

        // prepare $data to be created at $dynaModel's table

        $dataArray = $data->map(function ($item) {
            return (array)$item;
        })->toArray();

        try {
            $dynaModel::insert($dataArray);

            File::delete($filePath);

            return redirect()->route('dashboard')->with('success', 'Importación exitosa');
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error('Error during data import: ' . $e->getMessage());

            // Delete the uploaded file
            File::delete($filePath);

            // Redirect back with an error message
            return redirect()->route('dashboard')->with('error', 'Motivo de la falla:' . $e->getMessage());
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

        $diferimento = new \Illuminate\Database\Eloquent\Collection();

        $data = $this->generarPlan(
            $request->input('capital_inicial'),
            $request->input('meses'),
            $request->input('taza_interes'),
            $request->input('correlativo'),
            $request->input('plazo_credito'),
            $request->input('fecha_inicio')
        );

        if (
            $request->input('diff_cuotas') and
            $request->input('diff_capital') and
            $request->input('diff_interes')
        ) {
            $diferimento = $this->generarDiferimento(
                $request->input('diff_cuotas'),
                $request->input('diff_capital'),
                $request->input('diff_interes'),
                $request->input('plazo_credito'),
                $data->last()->vencimiento
            );
        }

        $helperData = \App\Models\Helper::where('idepro', $request->input('idepro'))->get();
        foreach ($helperData as $h) {
            $h->estado = 'INACTIVO';
            $h->save();
        }

        $helperData = null;

        $planData = \App\Models\Plan::where('idepro', $request->input('idepro'))->get();

        foreach ($planData as $plan) {
            $plan->estado = 'INACTIVO';
            $plan->save();
        }

        $planData = null;

        $readjustmentData = \App\Models\Readjustment::where('idepro', $request->input('idepro'))->get();

        foreach ($readjustmentData as $readjustment) {
            $readjustment->estado = 'INACTIVO';
            $readjustment->save();
        }

        $readjustmentData = null;

        foreach ($data as $d) {
            \App\Models\Readjustment::create([
                'idepro' => $request->input('idepro'),
                'fecha_ppg' => $d->vencimiento,
                'prppgnpag' => $d->nro_cuota,
                'prppgcapi' => $d->abono_capital,
                'prppginte' => $d->interes,
                'prppgsegu' => $d->seguro,
                'prppgtota' => $d->total_cuota,
                'estado' => 'ACTIVO',
            ]);
        }

        foreach ($diferimento as $d) {
            \App\Models\Helper::create([
                'idepro' => $request->input('idepro'),
                'indice' => $d->nro_cuota,
                'capital' => $d->capital,
                'interes' => $d->interes,
                'vencimiento' => $d->vencimiento,
                'estado' => $d->estado,
            ]);
        }

        $headers = collect($data->first())->keys();
        $headers = $headers->combine($headers);
        $data->prepend(collect($headers));

        $d1 = $this->generateExcelFile($data, $title, $fileName);
        if ($diferimento->count() > 0) {
            $d2 = $this->generateExcelFile($diferimento, 'diferimiento', 'exportacion_' . uniqid() . '_diferimiento.xlsx');
            return $d1 . '--- ' . $d2;
        } else {
            return $d1;
        }
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
