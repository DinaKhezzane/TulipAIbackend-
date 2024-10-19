<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reportType }} Report</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            font-size: 12px;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        .container {
            padding: 20px;
            border: 1px solid #ddd;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
            color: #2c3e50;
        }

        .header p {
            font-size: 14px;
            margin: 0;
            color: #95a5a6;
        }

        .company-info {
            margin-bottom: 40px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        .company-info h2 {
            margin: 0;
            font-size: 20px;
            color: #2980b9;
        }

        .company-info p {
            margin: 0;
            font-size: 14px;
            color: #7f8c8d;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .footer {
            text-align: center;
            margin-top: 50px;
            font-size: 12px;
            color: #95a5a6;
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Company Info Section -->
        <div class="company-info">
            <h2>{{ $company->org_name }}</h2>
            <p><strong>Category:</strong> {{ $company->category }}</p>
            
        </div>

        <!-- Report Title Section -->
        <div class="header">
            <h1>{{ $reportType }} Report</h1>
            <p>From {{ $startDate }} to {{ $endDate }}</p>
        </div>

        <!-- Report Data Section -->
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $row)
                    <tr>
                        <td>{{ $row->date }}</td>
                        <td>{{ $row->category }}</td>
                        <td>{{ $row->description }}</td>
                        <td>{{ number_format($row->amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Footer Section -->
        <div class="footer">
            <p>{{ $company->org_name }} &copy; {{ now()->year }}. All rights reserved.</p>
        </div>
    </div>

</body>
</html>
