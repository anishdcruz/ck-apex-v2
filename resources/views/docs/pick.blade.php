@extends('docs.master', ['title' => 'Pick List '.$model->number, 'options' => $options])

@section('content')
    <div class="content-title">
        PICK LIST
    </div>
    <table class="summary">
        <tbody>
            <tr>
                <td class="summary-address">
                    <strong>Ship To:</strong>
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
                                <td>Sales Order:</td>
                                <td>{{$model->number}}</td>
                            </tr>
                            @if($model->reference)
                            <tr>
                                <td>Reference:</td>
                                <td>{{$model->reference}}</td>
                            </tr>
                            @endif
                            <tr>
                                <td>Print Timestamp:</td>
                                <td>{{Carbon\Carbon::now()}}</td>
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
                    <th width="65%">Description</th>
                    <th class="ac" width="10%">Qty</th>
                    <th class="ar" width="10%">Verify</th>
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
                        <td></td>
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
            <td class="ar" width="70%">Picked By:</td>
            <td><br></td>
        </tr>
        <tr>
            <td class="ar" width="70%">Verified By:</td>
            <td><br><br></td>
        </tr>
        <tr>
            <td class="ar" width="70%">Remarks:</td>
            <td><br></td>
        </tr>
    </tbody>
</table>
@endsection
