@extends('docs.master', ['title' => 'Expense '.$model->number, 'options' => $options])

@section('content')
    <div class="content-title">
        EXPENSE
    </div>
    <table class="summary">
        <tbody>
            <tr>
                <td class="summary-address">
                    <strong>Paid To:</strong><br>
                    <span>{{$model->vendor->person}},</span><br>
                    <span>{{$model->vendor->company}},</span><br>
                    <pre>{{$model->vendor->billing_address}}</pre>
                </td>
                <td class="summary-empty"></td>
                <td class="summary-info">
                    <table class="info">
                        <tbody>
                            <tr>
                                <td>Number:</td>
                                <td>{{$model->number}}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
        <table class="items">
            <tbody>
                <tr>
                    <td width="40%">Payment Date:</td>
                    <td width="60%">{{$model->payment_date}}</td>
                </tr>
                <tr>
                    <td>Currency:</td>
                    <td>{{$model->currency->code}}</td>
                </tr>
                <tr>
                    <td>Amount Paid:</td>
                    <td>{{moneyFormat($model->amount_paid, $model->currency, false)}}</td>
                </tr>
                <tr>
                    <td>Description</td>
                    <td><pre>{{$model->description}}</pre></td>
                </tr>
            </tbody>
    </table>

@endsection
