<?php
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

if(! function_exists('settings')) {
    /**
     * Settings class helper
     *
     * @return App\Support\Settings
     */
    function settings()
    {
        return new App\Support\Settings;
    }
}

if(! function_exists('currency')) {
    /**
     * Currency class helper
     *
     * @return App\Support\Currency
     */
    function currency()
    {
        return new App\Support\CurrencyWrapper;
    }
}

if(! function_exists('counter')) {
    /**
     * Counter class helper
     *
     * @return App\Support\Counter
     */
    function counter()
    {
        return new App\Support\Counter;
    }
}

if(! function_exists('api')) {
    /**
     * Json response for API
     *
     * @param array $response
     * @param integer $code
     * @return App\Support\Counter
     */
    function api($response, $code = 200)
    {
        return response()
            ->json($response, $code);
    }
}

if(! function_exists('uploadFile')) {
    /**
     * Upload file to storage folder
     *
     * @param $file
     * @param $dir
     * @return string or null
     */
    function uploadFile($file, $dir = 'storage/app/uploads/')
    {
        $fileName = str_random(32).'.'.$file->extension();

        if($file->move(base_path($dir), $fileName)) {
            return $fileName;
        }

        return null;
    }
}

if(! function_exists('deleteFile')) {
    /**
     * Delete file from storage folder
     *
     * @param $file
     * @param $dir
     * @return string or null
     */
    function deleteFile($fileName, $dir = 'storage/app/uploads/')
    {
        return File::delete(base_path($dir).$fileName);
    }
}

if(! function_exists('pdf')) {
    /**
     * PDF output helper
     *
     * @param string $file
     * @param array $model
     */
    function pdf($file, $model)
    {

        $pdf = pdfRaw($file, $model);


        $file = $model->number.'-'.time().'.pdf';

        if(request()->has('mode') && request()->mode == 'download') {
            return $pdf->Output($file, Destination::DOWNLOAD);
        }

        return $pdf->Output($file, Destination::INLINE);
    }
}

function pdfRaw($file, $model) {
    $options = settings()->getMany(['header-html', 'footer-html']);
    $options = array_filter($options, function($key){
        return !is_null($key);
    } );

    // dd($options['header-html']);
    $html = view($file, ['model' => $model, 'options' => $options]);
    $pdf = new Mpdf(config('pdf'));
    $pdf->WriteHTML($html);

    return $pdf;
}

function moneyFormat($value, $currency, $code = true)
{
    $amount = number_format($value, $currency->decimal_place);

    return $code? $currency->code.' '.$amount : $amount;
}


function selectedTax($items)
{
    $taxes = [];

    foreach($items as $item) {
        if($item->taxes && count($item->taxes) > 0) {
            foreach($item->taxes as $tax) {
                $key = $tax->name.' '.$tax->rate.'%';
                if(isset($taxes[$key])) {
                    $taxes[$key] = $taxes[$key] + ($item->unit_price * $item->qty) * $tax->rate / 100;
                } else {
                    $taxes[$key] = ($item->unit_price * $item->qty) * $tax->rate / 100;
                }
            }
        }
    }

    return $taxes;
}
