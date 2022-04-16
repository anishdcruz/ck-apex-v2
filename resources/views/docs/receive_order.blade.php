@extends('docs.master', ['title' => 'Receive Order '.$model->number, 'options' => $options])

@section('content')
    <div class="content-title">
        RECEIVE ORDER
    </div>
    <table class="summary">
        <tbody>
            <tr>
                <td class="summary-address">
                    <strong>Received From:</strong><br>
                    <span>{{$model->vendor->person}},</span><br>
                    <span>{{$model->vendor->company}},</span><br>
                    <pre>{{$model->vendor->billing_address}}</pre>
                </td>
                <td class="summary-empty"></td>
                <td class="summary-info">
                    <table class="info">
                        <tbody>
                            <tr>
                                <td>Date:</td>
                                <td>{{$model->date}}</td>
                            </tr>
                            <tr>
                                <td>Receive Order Number:</td>
                                <td>{{$model->number}}</td>
                            </tr>
                            <tr>
                                <td>Purchase Order Number:</td>
                                <td>{{$model->purchaseOrder->number}}</td>
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
                    <th class="ac" width="10%">Qty Received</th>
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
                    <td></td>
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
