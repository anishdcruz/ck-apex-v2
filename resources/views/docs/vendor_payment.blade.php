@extends('docs.master', ['title' => 'Vendor Payment '.$model->number, 'options' => $options])

@section('content')
    <div class="content-title">
        Vendor Payment
    </div>
    <table class="summary">
        <tbody>
            <tr>
                <td class="summary-address">
                    <strong>Paid To:</strong>
                    @if($model->vendor->company)
                        <pre>{{$model->vendor->company}}<br>{{$model->vendor->billing_address}}</pre>
                        <p>
                            <strong>Contact:</strong>
                            {{$model->vendor->person}}
                        </p>
                    @else
                        <pre>{{$model->vendor->person}}<br>{{$model->vendor->billing_address}}</pre>
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
                            <tr>
                                <td>Payment Date:</td>
                                <td>{{$model->payment_date}}</td>
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
                            <tr>
                                <td>Currency:</td>
                                <td>{{$model->currency->code}}</td>
                            </tr>
                            <tr>
                                <td>Amount Received:</td>
                                <td>{{moneyFormat($model->amount_received, $model->currency, false)}}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
        <table class="items">
                        <thead>
                            <tr>
                                <th>Bill Date</th>
                                <th>Bill Number</th>
                                <th class="ar">Bill Total</th>
                                <th class="ar">Amount Applied</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($model->items as $item)
                                <tr>
                                    <td class="ar">
                                        {{$item->bill->date}}
                                    </td>
                                    <td class="ar">
                                        {{$item->bill->number}}
                                    </td>
                                    <td class="ar">
                                        {{moneyFormat($item->bill->total, $model->currency, false)}}
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
                                        {{moneyFormat($model->amount_paid, $model->currency, false)}}
                                    </strong>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
@endsection
