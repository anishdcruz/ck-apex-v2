@extends('docs.master', ['title' => 'Delivery Note '.$model->number, 'options' => $options])

@section('content')
    <div class="content-title">
        DELIVERY NOTE
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
                                <td>Number:</td>
                                <td>{{$model->number}}</td>
                            </tr>
                            <tr>
                                <td>Invoice:</td>
                                <td>{{$model->number}}</td>
                            </tr>
                            <tr>
                                <td>Delivery Date:</td>
                                <td>{{today()->format('Y-m-d')}}</td>
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
                    <th class="ar" width="10%">&nbsp;</th>
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
    </table>
<table class="terms">
    <tbody>
        <tr>
            <td class="ar" width="70%">
                <strong>Received in Good Condition By</strong>
            </td>
        </tr>
        <tr>
            <td class="ar" width="70%">Name:</td>
            <td><br></td>
        </tr>
        <tr>
            <td class="ar" width="70%">Signature:</td>
            <td><br><br></td>
        </tr>
        <tr>
            <td class="ar" width="70%">Date:</td>
            <td><br></td>
        </tr>
    </tbody>
</table>
@endsection
