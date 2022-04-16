@extends('docs.master', ['title' => 'Goods Issued '.$model->number, 'options' => $options])

@section('content')
    <div class="content-title">
        GOODS ISSUED
    </div>
    <table class="summary">
        <tbody>
            <tr>
                <td class="summary-address">
                    <strong>Issued To:</strong>
                    @if($model->client->company)
                        <pre>{{$model->client->company}}<br>{{$model->client->shipping_address}}</pre>
                        <p>
                            <strong>Attention:</strong>
                            {{$model->client->person}}
                        </p>
                    @else
                        <pre>{{$model->client->person}}<br>{{$model->client->shipping_address}}</pre>
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
                            <tr>
                                <td>Goods Issue Number:</td>
                                <td>{{$model->number}}</td>
                            </tr>
                            <tr>
                                <td>Sales Order Number:</td>
                                <td>{{$model->salesOrder->number}}</td>
                            </tr>
                            @if($model->reference)
                            <tr>
                                <td>Reference:</td>
                                <td>{{$model->reference}}</td>
                            </tr>
                            @endif
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
                    <th width="65%">Description</th>
                    <th class="ac" width="10%">Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach($model->items as $item)
                    <tr>
                        <td>{{$item->product->item_code}}</td>
                        <td>
                            <pre>{{$item->product->description}}</pre>
                        </td>
                        <td class="ac">{{$item->qty}}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td class="item-empty"></td>
                    <td>
                        <strong>Total Items:</strong>
                    </td>
                    <td>
                        <strong>{{$model->items->sum('qty')}}</strong>
                    </td>
                </tr>
            </tfoot>
    </table>
    <table class="terms">
    <tbody>
        <tr>
            <td class="terms-description">
                <div>
                    <strong>Internal Note</strong>
                    <pre>{{$model->note}}</pre>
                </div>
            </td>
            <td></td>
        </tr>
    </tbody>
</table>
@endsection
