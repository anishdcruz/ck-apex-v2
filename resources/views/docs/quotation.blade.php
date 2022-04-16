@extends('docs.master', ['title' => 'Quotation '.$model->number, 'options' => $options])

@section('content')
    <div class="content-title">
        QUOTATION
    </div>
    <table class="summary">
        <tbody>
            <tr>
                <td class="summary-address">
                    <strong>To:</strong>
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
                            <tr>
                                <td>Date:</td>
                                <td>{{$model->date}}</td>
                            </tr>
                            @if($model->reference)
                            <tr>
                                <td>Reference:</td>
                                <td>{{$model->reference}}</td>
                            </tr>
                            @endif
                            <tr>
                                <td>Currency:</td>
                                <td>{{$model->currency->code}}</td>
                            </tr>
                            <tr>
                                <td>Total:</td>
                                <td>{{moneyFormat($model->total, $model->currency, false)}}</td>
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
                    <th width="15%">Item Code</th>
                    <th width="40%">Description</th>
                    <th class="ar" width="15%">Unit Price</th>
                    <th class="ac" width="10%">Qty</th>
                    <th class="ar" width="20%">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($model->items as $item)
                    <tr>
                        <td>{{$item->product->item_code}}</td>
                        <td>
                            <pre>{{$item->product->description}}</pre>
                        </td>
                        <td class="ar">
                            {{moneyFormat($item->unit_price, $model->currency, false)}}
                        </td>
                        <td class="ac">{{$item->qty}}</td>
                        <td class="ar">
                            {{moneyFormat($item->qty * $item->unit_price, $model->currency, false)}}
                        </td>
                    </tr>
                    @foreach($item['taxes'] as $tax)
                        <tr class="item-tax">
                            <td class="ac" colspan="5">{{$tax->name}} @ {{$tax->rate}}%</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="item-empty"></td>
                    <td colspan="2">Sub Total</td>
                    <td>
                        {{moneyFormat($model->sub_total, $model->currency, false)}}
                    </td>
                </tr>
                @foreach(selectedTax($model->items) as $key => $tax)
                <tr>
                    <td colspan="2" class="item-empty"></td>
                    <td colspan="2">{{$key}}</td>
                    <td>
                        {{moneyFormat($tax, $model->currency, false)}}
                    </td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="2" class="item-empty"></td>
                    <td colspan="2">
                        <strong>Total:</strong>
                    </td>
                    <td>
                        <strong>{{moneyFormat($model->total, $model->currency)}}</strong>
                    </td>
                </tr>
            </tfoot>
    </table>
<table class="terms">
    <tbody>
        <tr>
            <td class="terms-description">
                <div>
                    <strong>Terms and Conditions</strong>
                    <pre>{{$model->terms}}</pre>
                </div>
            </td>
            <td></td>
        </tr>
    </tbody>
</table>
@endsection
