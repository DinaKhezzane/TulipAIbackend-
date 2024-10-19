<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse; // For CSV/Excel
use PDF; // Assuming you are using dompdf for PDF generation
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller
{
    public function generateReport(Request $request)
    {
        $reportType = $request->input('reportType');  // e.g., "Income Statement"
        $startDate = $request->input('startDate');    // e.g., "2024-01-01"
        $endDate = $request->input('endDate');        // e.g., "2024-12-31"
        $fileFormat = $request->input('fileFormat');  // e.g., "CSV"

        // Fetch the actual data based on the report type and date range
        $data = $this->fetchReportData($reportType, $startDate, $endDate);

        // Generate the report based on the requested format
        if ($fileFormat === 'CSV') {
            return $this->generateCsv($data);
        } elseif ($fileFormat === 'PDF') {
            return $this->generatePdf($data, $reportType, $startDate, $endDate);
        }elseif ($fileFormat === 'Excel') {
            return $this->generateExcel($data, $reportType, $startDate, $endDate);
        }

        return response()->json(['error' => 'Invalid file format'], 400);
    }

    // Example of fetching the data (you should adjust this to your actual data source)
    private function fetchReportData($reportType, $startDate, $endDate)
    {
        if ($reportType === 'Income report') {
            return \DB::table('inflows')
                ->join('inflow_categories', 'inflows.inflow_category_id', '=', 'inflow_categories.id')
                ->select('inflows.date', 'inflow_categories.name as category', 'inflows.description', 'inflows.amount')
                ->whereBetween('inflows.date', [$startDate, $endDate])
                ->orderBy('inflows.date', 'asc')
                ->get();
        } elseif ($reportType === 'Expense report') {
            return \DB::table('outflows')
                ->join('outflow_categories', 'outflows.outflow_category_id', '=', 'outflows.id')
                ->select('outflows.date', 'outflow_categories.name as category', 'outflows.description', 'outflows.amount')
                ->whereBetween('outflows.date', [$startDate, $endDate])
                ->orderBy('outflows.date', 'asc')
                ->get();
        }
    
        return [];
    }
    


private function generateCsv($data)
{
    $response = new StreamedResponse(function () use ($data) {
        $handle = fopen('php://output', 'w');

        // Write the headers
        fputcsv($handle, ['Date', 'Category', 'Amount', 'Description']);

        // Write the data
        foreach ($data as $row) {
            fputcsv($handle, [
                $row->date,
                $row->category,
                $row->amount,
                $row->description,
            ]);
        }

        fclose($handle);
    });

    $response->headers->set('Content-Type', 'text/csv');
    $response->headers->set('Content-Disposition', 'attachment; filename="income-report.csv"');

    return $response;
}


    // Generate PDF
    private function generatePdf($data, $reportType, $startDate, $endDate)
{
    // Hardcode company information for now, without the description
    $company = (object) [
        'org_name' => 'Your Company Name',
        'category' => 'Your Company Category',
    ];

    // Pass the hardcoded company information to the PDF view
    $pdf = PDF::loadView('reports.template', [
        'data' => $data,
        'reportType' => $reportType,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'company' => $company, // Use hardcoded company data
    ]);

    // Generate the PDF with a descriptive filename
    $fileName = $company->org_name . '_' . $reportType . '_' . $startDate . '_to_' . $endDate . '.pdf';

    return $pdf->download($fileName);
}



    // Generate Excel (use a library like PhpSpreadsheet for Excel generation)
    // Method to generate an Excel report
private function generateExcel($data, $reportType, $startDate, $endDate)
{
    // Create a new Spreadsheet object
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set the document properties
    $sheet->setTitle($reportType . ' Report');

    // Add header row
    $sheet->setCellValue('A1', 'Date');
    $sheet->setCellValue('B1', 'Category');
    $sheet->setCellValue('C1', 'Description');
    $sheet->setCellValue('D1', 'Amount');

    // Add data rows
    $rowNum = 2; // Start from the second row after headers
    foreach ($data as $row) {
        $sheet->setCellValue('A' . $rowNum, $row->date);
        $sheet->setCellValue('B' . $rowNum, $row->category);
        $sheet->setCellValue('C' . $rowNum, $row->description);
        $sheet->setCellValue('D' . $rowNum, $row->amount);
        $rowNum++;
    }

    // Stream the Excel file as a response
    $response = new StreamedResponse(function () use ($spreadsheet) {
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    });

    // Set headers for Excel file download
    $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    $response->headers->set('Content-Disposition', 'attachment; filename="' . $reportType . '-report.xlsx"');
    $response->headers->set('Cache-Control', 'max-age=0');

    return $response;
}

}
