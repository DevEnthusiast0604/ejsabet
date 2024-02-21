<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Nuevo Informe de auditoria Para {{ $data['company_name'] }}</title>
    <style>
        @page {margin: 20px 25px;}
        body {
            margin: 20px 25px;
            background-color: #FFFFFF;
            font-family: 'Open Sans', sans-serif !important;
        }
        table {
            border: 1px solid #dee2e6;
            border-collapse: collapse;
            width: 100%;
        }
        table td, table th {
            font-size: 13.4px;
            padding: 6px 8px 4px !important;
            border: 1px solid #dee2e6;
        }
        .table thead th {
            text-align: left;
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
            padding: 6px 8px 4px !important;
        }
        .table tfoot th {
            text-align: left;
        }
        .title {
            font-size: 20px;
            text-decoration: underline;
            padding: 6px;
            margin-bottom: 10px;
        }

        .company-name {
            font-size: 17px;
            font-weight: 600;
        }

        .table-total-balance  {
            border: none;
        }
        .table-total-balance th,
        .table-total-balance td {
            border: none;
            font-weight: 600;
            font-size: 15px
        }
        .table-total-balance td.value {
            width: 300px;
            text-align: right;
        }
        .border-top {
            border-top: solid 1px #212529 !important;
        }
        .border-bottom {
            border-bottom: solid 1px #212529 !important;
        }
        .text-danger {
            color: #dc3545 !important;
        }
        .text-success {
            color: #28A745 !important;
        }
        .text-underline {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div>
        <div style="text-align: center;0">
            <h5 class="title">{{__('page.audit_report')}}</h5>
            <p class="company-name" style="margin-bottom: 10px;margin-top: 0;">{{ $data['company_name'] }}</p>
            <p style="margin-bottom: 0;margin-top: 0;font-style: italic;">{{ $data['start_date'] }} a {{ $data['end_date'] }}</p>
        </div>
        <br>
        <div class="category-data">
            <table>
                <thead>
                    <tr>
                        <th>{{__('page.category')}}</th>
                        <th style="width: 250px;">{{__('page.total_amount')}}</th>
                        <th style="width: 150px;">{{__('page.type')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['category_data'] as $item)
                        <tr>
                            <td>{{ $item['name'] }}</td>
                            <td style="text-align: right;" class="@if($item['type'] === 'expense') text-danger text-underline @else text-success @endif">{{ number_format($item['total_amount'])}}</td>
                            <td>{{ __('page.' . $item['type']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <br>
        <div class="total-balance">
            <table class="table-total-balance">
                <tbody>
                    <tr>
                        <td class="border-bottom">{{__('page.initial_balance')}}</td>
                        <td class="value border-bottom">{{ number_format($data['latest_audit_balance']) }}</td>
                        <td style="width: 150px;"></td>
                    </tr>
                    <tr>
                        <td>{{__('page.expense')}}</td>
                        <td class="value">{{ number_format($data['total_expense']) }}</td>
                        <td style="width: 150px;"></td>
                    </tr>
                    <tr>
                        <td>{{__('page.incoming')}}</td>
                        <td class="value">{{ number_format($data['total_incoming']) }}</td>
                        <td style="width: 150px;"></td>
                    </tr>
                    @php
                        $balance = $data['latest_audit_balance'] + $data['total_incoming'] - $data['total_expense'];
                    @endphp
                    <tr>
                        <td class="border-top">{{__('page.balance')}}</td>
                        <td class="value border-top">{{ number_format($balance) }}</td>
                        <td style="width: 150px;"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>