<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use App\Models\Outflow;
use App\Models\Inflow;
use App\Models\Company;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class IncomeStatementController extends Controller
{
    public function generateIncomeStatement(Request $request)
    {
        // Validate the request
        $request->validate([
            'month' => 'required|date_format:Y-m', // YYYY-MM format for the month
            'fileFormat' => 'required|in:PDF,Excel' // Ensure the file format is either PDF or Excel
        ]);

        // Get the token information to identify the user's company
        $tokenInfo = $request->attributes->get('tokenInfo');

        // Get the company associated with the token (based on manager_id or employee_id)
        $company = null;
        if ($tokenInfo->manager_id) {
            $company = Company::where('manager_id', $tokenInfo->manager_id)->first();
        } elseif ($tokenInfo->employee_id) {
            $company = Company::whereHas('employees', function ($query) use ($tokenInfo) {
                $query->where('id', $tokenInfo->employee_id);
            })->first();
        }

        // If no company found, return an error
        if (!$company) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        // Get start and end of the selected month
        $startOfMonth = $request->month . '-01';
        $endOfMonth = date("Y-m-t", strtotime($startOfMonth)); // Get the last day of the selected month

        // Fetch inflows (revenues) for the selected month and company
        $inflows = Inflow::where('company_id', $company->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->with('category') // Eager load category for optimization
            ->get();

        // Fetch outflows (expenses) for the selected month and company
        $outflows = Outflow::where('company_id', $company->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->with('category') // Eager load category for optimization
            ->get();

        // Calculate totals for inflows, outflows, gross profit, etc.
        $totalRevenue = $inflows->sum('amount');
        $totalExpenses = $outflows->sum('amount');
        $grossProfit = $totalRevenue - $totalExpenses;

        // Prepare data for the view or Excel generation
        $data = [
            'company' => $company->org_name,
            'month' => $request->month,
            'inflows' => $inflows,
            'outflows' => $outflows,
            'totalRevenue' => $totalRevenue,
            'totalExpenses' => $totalExpenses,
            'grossProfit' => $grossProfit,
        ];

        // Handle PDF or Excel export based on user request
        if ($request->fileFormat === 'PDF') {
            // Generate PDF
            $pdf = PDF::loadView('pdf.income-statement', $data);
            return $pdf->download('income_statement_' . $request->month . '.pdf');
        } elseif ($request->fileFormat === 'Excel') {
            // Generate Excel
            return $this->generateExcelTest();
        }

        return response()->json(['error' => 'Invalid file format'], 400);
    }

    private function generateExcelTest()
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'Test Excel');
    $sheet->setCellValue('A2', 'This is a test.');

    // Stream the Excel file as a response
    $response = new StreamedResponse(function () use ($spreadsheet) {
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    });

    // Set headers for Excel file
    $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    $response->headers->set('Content-Disposition', 'attachment; filename="test.xlsx"');
    $response->headers->set('Cache-Control', 'max-age=0');

    return $response;
}


    private function generateExcel($data)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set the document properties
        $sheet->setCellValue('A1', 'Income Statement');
        $sheet->setCellValue('A2', 'Company: ' . $data['company']);
        $sheet->setCellValue('A3', 'Month: ' . $data['month']);

        // Set the headers for the table
        $sheet->setCellValue('A5', 'Description');
        $sheet->setCellValue('B5', 'Amount');

        // Add inflows (revenue)
        $sheet->setCellValue('A6', 'Revenue');
        $row = 7;
        foreach ($data['inflows'] as $inflow) {
            $sheet->setCellValue('A' . $row, $inflow->category->name);
            $sheet->setCellValue('B' . $row, $inflow->amount);
            $row++;
        }

        // Total revenue
        $sheet->setCellValue('A' . $row, 'Total Revenue');
        $sheet->setCellValue('B' . $row, $data['totalRevenue']);
        $row++;

        // Add outflows (expenses)
        $sheet->setCellValue('A' . ($row + 1), 'Operating Expenses');
        $row += 2;
        foreach ($data['outflows'] as $outflow) {
            $sheet->setCellValue('A' . $row, $outflow->category->name);
            $sheet->setCellValue('B' . $row, $outflow->amount);
            $row++;
        }

        // Total expenses
        $sheet->setCellValue('A' . $row, 'Total Expenses');
        $sheet->setCellValue('B' . $row, $data['totalExpenses']);
        $row++;

        // Gross Profit
        $sheet->setCellValue('A' . ($row + 1), 'Gross Profit');
        $sheet->setCellValue('B' . ($row + 1), $data['grossProfit']);

        // Stream the Excel file as a response
        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        // Set the headers for the Excel file download
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="income_statement_' . $data['month'] . '.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');
        
        return $response;
    }
}
