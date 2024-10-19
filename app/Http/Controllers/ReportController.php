<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse; // For CSV/Excel
use PDF; // Assuming you are using dompdf for PDF generation
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller
{
    public function generateReport(Request $request)
    {
        try {
            // Validate the incoming request
            $request->validate([
                'reportType' => 'required|string',
                'startDate' => 'required|date',
                'endDate' => 'required|date',
                'fileFormat' => 'required|string|in:PDF,CSV,Excel',
            ]);

            // Retrieve token information from the request attributes
            $tokenInfo = $request->attributes->get('tokenInfo');

            // Determine the company from token information (manager_id or employee_id)
            $company = null;
            if ($tokenInfo->manager_id) {
                // If the token belongs to a manager
                $company = Company::where('manager_id', $tokenInfo->manager_id)->first();
            } elseif ($tokenInfo->employee_id) {
                // If the token belongs to an employee
                $company = Company::whereHas('employees', function ($query) use ($tokenInfo) {
                    $query->where('id', $tokenInfo->employee_id);
                })->first();
            }

            // If no company found, return an error response
            if (!$company) {
                return response()->json(['message' => 'Company not found'], 404);
            }

            // Fetch the report data based on the report type, company ID, and date range
            $data = $this->fetchReportData($request->reportType, $request->startDate, $request->endDate, $company->id);

            // Generate the report based on the requested format
            if ($request->fileFormat === 'CSV') {
                return $this->generateCsv($data);
            } elseif ($request->fileFormat === 'PDF') {
                return $this->generatePdf($data, $request->reportType, $request->startDate, $request->endDate, $company);
            } elseif ($request->fileFormat === 'Excel') {
                return $this->generateExcel($data, $request->reportType, $request->startDate, $request->endDate);
            }

            return response()->json(['error' => 'Invalid file format'], 400);

        } catch (\Exception $e) {
            Log::error('Error generating report: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to generate report', 'error' => $e->getMessage()], 500);
        }
    }

    // Example of fetching the data filtered by company ID (added company ID filtering)
    private function fetchReportData($reportType, $startDate, $endDate, $companyId)
    {
        if ($reportType === 'Income report') {
            return \DB::table('inflows')
                ->join('inflow_categories', 'inflows.inflow_category_id', '=', 'inflow_categories.id')
                ->select('inflows.date', 'inflow_categories.name as category', 'inflows.description', 'inflows.amount')
                ->where('inflows.company_id', $companyId) // Filter by company ID
                ->whereBetween('inflows.date', [$startDate, $endDate])
                ->orderBy('inflows.date', 'asc')
                ->get();
        } elseif ($reportType === 'Expense report') {
            return \DB::table('outflows')
                ->join('outflow_categories', 'outflows.outflow_category_id', '=', 'outflows.id')
                ->select('outflows.date', 'outflow_categories.name as category', 'outflows.description', 'outflows.amount')
                ->where('outflows.company_id', $companyId) // Filter by company ID
                ->whereBetween('outflows.date', [$startDate, $endDate])
                ->orderBy('outflows.date', 'asc')
                ->get();
        }

        return [];
    }

    // Method to generate CSV (no changes)
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

    // Method to generate PDF
    private function generatePdf($data, $reportType, $startDate, $endDate, $company)
{
    // Use the company information retrieved from the token
    $pdf = PDF::loadView('pdf_templates.template', [
        'data' => $data,
        'reportType' => $reportType,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'company' => $company, // Use actual company data instead of hardcoded values
    ]);

    // Generate the PDF with a descriptive filename
    $fileName = $company->org_name . '_' . $reportType . '_' . $startDate . '_to_' . $endDate . '.pdf';

    return $pdf->download($fileName);
}


    // Method to generate Excel
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
