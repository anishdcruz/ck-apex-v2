<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use File;
use Hash;

class SettingsController extends Controller
{
    public function showPersonal()
    {
        $user = auth()->user();
        $form = [
            'name' => $user->name,
            'title' =>  $user->title,
            'mobile_number' => $user->mobile_number,
            'telephone' => $user->telephone,
            'extension' => $user->extension,
            'password' => null,
            'password_confirmation' => null,
            'email_signature' => $user->email_signature,
            'email' => $user->email,
            'company' => settings()->get('company_name')
        ];

        return api([
            'form' => $form
        ]);
    }

    public function storePersonal(Request $request)
    {
        $model = auth()->user();
        $request->validate([
            'name' => 'required|max:255',
            'title' => 'nullable|max:255',
            'mobile_number' => 'nullable|max:255',
            'telephone' => 'nullable|max:255',
            'extension' => 'nullable|max:255',
            'current_password' => 'sometimes|min:6|max:60',
            'new_password' => 'required_with:current_password|confirmed|min:6|max:60',
            'email_signature' => 'required|max:255'
        ]);

        if($request->has('current_password')) {
            if(Hash::check($request->current_password, $model->password)) {
                // match passwords
                $model->password = $request->new_password;
            } else {
                // throw error
                return api([
                    'errors' => [
                        'current_password' => ['Current password is invalid, Please try again with correct password!']
                    ]
                ], 422);
            }
        }

        $model->fill($request->except('password', 'email'));
        $model->save();

        return api([
            'saved' => true
        ]);
    }

    public function show()
    {
        $currency = currency()->defaultToArray();

        $settings = settings()->getMany([
            'uploaded_logo', 'app_title',
            'company_name', 'company_address',
            'company_telephone', 'company_email', 'company_website',
            'sent_from_email', 'sent_from_name', 'global_bcc_email',
            'footer_line_1', 'footer_line_2', 'footer_line_3',
            'header', 'footer'
        ]);

        $form = array_merge($currency, $settings);

        return api([
            'form' => $form
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'currency_id' => 'required|integer|exists:currencies,id',
            'app_title' => 'nullable|max:255',
            'uploaded_logo_file' => 'nullable|image|max:2048',
            'company_name' => 'nullable|max:255',
            'company_address' => 'nullable|max:255',
            'company_telephone' => 'nullable|max:255',
            'company_email' => 'nullable|email',
            'company_website' => 'nullable|max:255',
            'company_payment_details' => 'nullable|max:255',
            'sent_from_email' => 'nullable|email',
            'sent_from_name' => 'nullable|max:255',
            'global_bcc_email' => 'nullable|email',
            'footer_line_1' => 'nullable|max:255',
            'footer_line_2' => 'nullable|max:255',
            'footer_line_3' => 'nullable|max:255',
            'header_file' => 'nullable|image|max:2048',
            'footer_file' => 'nullable|image|max:2048'
        ]);

        // upload document if exists
        $this->uploadIfExist('uploaded_logo', 'uploaded_logo_file');

        $header = $this->uploadIfExist('header', 'header_file');

        if($header) {
            // generate html
            $headerHtmlPath = $this->createHTMLfile($header, 'header');
            settings()->set('header-html', $headerHtmlPath);
        }

        $footer = $this->uploadIfExist('footer', 'footer_file');

        if($footer) {
            // generate html
            $footerHtmlPath = $this->createHTMLfile($footer, 'footer');
            settings()->set('footer-html', $footerHtmlPath);
        }

        settings()->setMany($request->only([
            'currency_id', 'app_title',
            'company_name', 'company_address', 'company_telephone',
            'company_email','company_website',
            'company_payment_details',
            'sent_from_email', 'sent_from_name','global_bcc_email',
            'footer_line_1', 'footer_line_2', 'footer_line_3',
        ]));

        return api([
            'saved' => true
        ]);
    }

    protected function uploadIfExist($settings, $file)
    {
        if(request()->hasFile($file) && request()->file($file)->isValid()) {
            // store in public uploads folder by default
           if($fileName = uploadFile(request()->file($file))) {
                // overwrite previous uploaded file
                deleteFile(settings()->get($settings));
                settings()->set($settings, $fileName);

                return $fileName;
           }
        }
    }

    protected function createHTMLfile($file, $type)
    {
        $path = storage_path('app/uploads/').$file;
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $data = File::get($path);
        $base64 = 'data:image/' .$extension. ';base64,' . base64_encode($data);

        $h ='<div>';
        $h .='    <img style="width: 100%;display: block;" src="'.$base64.'">';
        $h .='</div>';

        $path = storage_path('app/'.$type.'.html');

        File::put($path, $h);

        return $path;
    }
}
