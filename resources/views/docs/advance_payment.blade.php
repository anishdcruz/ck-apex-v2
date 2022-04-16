@extends('docs.master', ['title' => 'Advance Payment '.$model->number, 'options' => $options])

@section('content')
    <div class="content-title">
        Advance Payment Receipt
    </div>
    <table class="summary">
        <tbody>
            <tr>
                <td class="summary-address">
                    <strong>Payment From:</strong>
                    @if($model->client->company)
                        <pre>{{$model->client->company}}<br>{{$model->client->billing_address}}</pre>
                        <p>
                            <strong>Attention:</strong>
                            {{$model->client->person}}
                        </p>
                    @else
                        <pre>{{$model->client->person}}<br>{{$model->client->billing_address}}</pre>
                    @endif
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
                    <td>Payment Mode:</td>
                    <td>{{$model->payment_mode}}</td>
                </tr>
                @if($model->payment_reference)
                <tr>
                    <td>Payment Reference:</td>
                    <td>{{$model->payment_reference}}</td>
                </tr>
                @endif
                @if($model->quotation)
                <tr>
                    <td>Quotation:</td>
                    <td>{{$model->quotation->number}}</td>
                </tr>
                @endif
                <tr>
                    <td>Currency:</td>
                    <td>{{$model->currency->code}}</td>
                </tr>
                <tr>
                    <td>Amount Received:</td>
                    <td>{{moneyFormat($model->amount_received, $model->currency, false)}}</td>
                </tr>
                <tr>
                    <td>Description</td>
                    <td><pre>{{$model->description}}</pre></td>
                </tr>
            </tbody>
    </table>
    @if($model->applied_amount && $model->applied_date)
        <table class="items">
            <tbody>
                <tr>
                    <td width="60%">Amount Applied to Invoices</td>
                    <td width="40%">{{moneyFormat($model->applied_amount, $model->currency, false)}}</td>
                </tr>
                <tr>
                    <td>Amount Applied Date</td>
                    <td>{{$model->applied_date}}</td>
                </tr>
            </tbody>
        </table>
        <table class="items">
                        <thead>
                            <tr>
                                <th>Invoice Date</th>
                                <th>Invoice Number</th>
                                <th class="ar">Invoice Total</th>
                                <th class="ar">Amount Applied</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($model->items as $item)
                                <tr>
                                    <td class="ar">
                                        {{$item->invoice->date}}
                                    </td>
                                    <td class="ar">
                                        {{$item->invoice->number}}
                                    </td>
                                    <td class="ar">
                                        {{moneyFormat($item->invoice->total, $model->currency, false)}}
                                    </td>
                                    <td class="ar">
                                        {{moneyFormat($item->amount_applied, $model->currency, false)}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="1"></td>
                                <td colspan="2">
                                    <strong>Amount Received</strong>
                                </td>
                                <td class="ar">
                                    <strong>
                                        {{moneyFormat($model->amount_received, $model->currency, false)}}
                                    </strong>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="1"></td>
                                <td colspan="2">
                                    <strong>Amount Applied to Invoices</strong>
                                </td>
                                <td class="ar">
                                    <strong>
                                        {{moneyFormat($model->applied_amount, $model->currency, false)}}
                                    </strong>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
    @endif
@endsection
