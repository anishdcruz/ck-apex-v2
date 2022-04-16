<!--mpdf
@if(isset($options['header-html']) && $options['header-html'])
<htmlpageheader name="header">
    {!! File::get($options['header-html']) !!}
</htmlpageheader>
<sethtmlpageheader name="header" show-this-page="1" />
@endif

@if(isset($options['footer-html']) && $options['footer-html'])
<htmlpagefooter name="footer">
    <div class="footer">
        <small>Page {PAGENO} of {nb}</small>
        {!! File::get($options['footer-html']) !!}
    </div>
</htmlpagefooter>
<sethtmlpagefooter name="footer" page="O" value="on" show-this-page="1" />
@endif
mpdf-->
<style>
    body {
        font-family: sans-serif;
        font-size: 9pt;
        color: #484746;
    }
    pre {
        font-family: sans-serif;
    }

    table {
        border-spacing: 0;
        width: 100%;
        border-collapse: collapse;
    }
    th,
    td{
        font-weight: normal;
        vertical-align: top;
        text-align: left;
    }
    .header-logo {
        width: 25%;
        text-align: right;
    }
    .header-company_name {
        font-size: 18pt;
        font-weight: bold;
        margin-bottom: 5px;
    }
    .content {

    }
    .content-title {
        margin-bottom: 20px;
        padding: 5px;
        text-align: center;
        font-size: 12pt;
        font-weight: bold;
        border: 0.1mm solid #484746;
    }

    .document-blue {
        color: #3aa3e3;
    }

    .document-orange {
        color: #FF9800;
    }

    .document-red {
        color: #E75650;
    }

    .document-blue_light {
        color: #48606f;
    }
    .document-green {
        color: #66bb6a;
    }
    .summary-address {
        width: 33.333%;
    }
    .summary-empty {
        width: 33.333%;
    }
    .summary-info {
        width: 33.333%;
    }
    .info td {
        text-align: right;
    }
    .info td:nth-child(2n) {
        padding-left: 15px;
    }
    .items {
        margin-top: 20px;
        border: 0.1mm solid #484746;
    }
    .items thead th {
        padding: 6px 3px;
        background: #f8f8f8;
        border: 0.1mm solid #484746;
    }
    .items tbody td {
        border: 0.1mm solid #484746;
        padding: 3px;
    }

    .items tfoot td {
         background: #f1f1f1;
        border: 0.1mm solid #484746;
        text-align: right;
        padding: 4px 3px;
    }
    .item-empty {

    }
    .ar {
        text-align: right;
    }
    .ac {
        text-align: center;
    }
    .terms {
        margin-top: 20px;
    }
    .terms-description {
        width: 70%;
    }
    .footer {
        text-align: center;
    }
</style>
<div class="content">
    @yield('content')
</div>
