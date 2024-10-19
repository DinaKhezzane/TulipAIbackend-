<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Income Statement</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { color: #154472; text-align: center; }
        h3 { color: #154472; text-align: center; margin-top: -10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #ccc; padding: 8px; text-align: right; }
        th { background-color: #154472; color: white; }
        td:first-child { text-align: left; }
        .total { font-weight: bold; background-color: #59C4E4; color: #154472; }
        .section-header { background-color: #D6E7EE; font-weight: bold; color: #154472; }
        .header-text { text-align: left; padding-left: 10px; }
    </style>
</head>
<body>
    <h1>Income Statement - {{ $company }}</h1>
    <h3>For the Month: {{ $month }}</h3>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <!-- Revenue Section -->
            <tr class="section-header">
                <td class="header-text"><strong>Revenue</strong></td>
                <td></td>
            </tr>
            @foreach ($inflows as $inflow)
                <tr>
                    <td>{{ $inflow->category->name }}</td>
                    <td>${{ number_format($inflow->amount, 2) }}</td>
                </tr>
            @endforeach
            <tr class="total">
                <td>Total Revenue</td>
                <td>${{ number_format($totalRevenue, 2) }}</td>
            </tr>

            <!-- Operating Expenses Section -->
            <tr class="section-header">
                <td class="header-text"><strong>Operating Expenses</strong></td>
                <td></td>
            </tr>
            @foreach ($outflows as $outflow)
                <tr>
                    <td>{{ $outflow->category->name }}</td>
                    <td>${{ number_format($outflow->amount, 2) }}</td>
                </tr>
            @endforeach
            <tr class="total">
                <td>Total Expenses</td>
                <td>${{ number_format($totalExpenses, 2) }}</td>
            </tr>

            <!-- Gross Profit Section -->
            <tr class="total">
                <td>Gross Profit</td>
                <td>${{ number_format($grossProfit, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
