<?php
// Auth::loginUsingId(1);
// var_dump(request()->path());
Route::post('login', 'PageController@login')
    ->middleware('guest');
Route::get('logout', 'PageController@logout')
    ->middleware('auth');
Route::get('uploads/{filename}', 'PageController@showAttachment')
    ->middleware('auth');
Route::group(['prefix' => 'api', 'middleware' => 'auth'], function() {

    Route::get('dashboard', 'DashboardController@index');

    Route::get('personal_settings', 'SettingsController@showPersonal');
    Route::post('personal_settings', 'SettingsController@storePersonal');

    Route::resource('clients', 'ClientController');
    Route::resource('products', 'ProductController');
    Route::resource('vendors', 'VendorController');

    Route::post('quotations/{quotation}/mark', 'QuotationController@markAs');
    Route::resource('quotations', 'QuotationController');

    Route::post('advance_payment_invoices/{advance_payment}/apply', 'AdvancePaymentController@applyInvoices');
    Route::resource('advance_payments', 'AdvancePaymentController');

    Route::post('sales_orders/{sales_order}/mark', 'SalesOrderController@markAs');
    Route::resource('sales_orders', 'SalesOrderController');

    Route::post('invoices/{invoice}/mark', 'InvoiceController@markAs');
    Route::resource('invoices', 'InvoiceController');

    Route::resource('client_payments', 'ClientPaymentController');

    Route::resource('expenses', 'ExpenseController');

    Route::post('purchase_orders/{purchase_order}/mark', 'PurchaseOrderController@markAs');
    Route::resource('purchase_orders', 'PurchaseOrderController');

    Route::resource('receive_orders', 'ReceiveOrderController');
    Route::resource('goods_issue', 'GoodsIssueController');

    Route::resource('bills', 'BillController');

    Route::resource('vendor_payments', 'VendorPaymentController');

    Route::group(['prefix' => 'search'], function() {
        Route::get('clients', 'ClientController@search');
        Route::get('vendors', 'VendorController@search');
        Route::get('products', 'ProductController@search');
        Route::get('currencies', 'CurrencyController@search');

        Route::get('advance_payment_invoices/{advance_payment}', 'AdvancePaymentController@showInvoices');
    });

    // admin routes
    Route::group(['middleware' => 'admin'], function() {
        Route::get('settings', 'SettingsController@show');
        Route::post('settings', 'SettingsController@store');
        Route::resource('users', 'Settings\UserController');
    });

    Route::group(['prefix' => 'email'], function() {
        Route::get('quotations/{quotation}', 'EmailController@showQuotation');
        Route::post('quotations/{quotation}', 'EmailController@sendQuotation');

        Route::get('advance_payments/{advance_payment}', 'EmailController@showAdvancePayment');
        Route::post('advance_payments/{advance_payment}', 'EmailController@sendAdvancePayment');

        Route::get('sales_orders/{sales_order}', 'EmailController@showSalesOrder');
        Route::post('sales_orders/{sales_order}', 'EmailController@sendSalesOrder');

        Route::get('invoices/{invoice}', 'EmailController@showInvoice');
        Route::post('invoices/{invoice}', 'EmailController@sendInvoice');

        Route::get('client_payments/{client_payment}', 'EmailController@showClientPayment');
        Route::post('client_payments/{client_payment}', 'EmailController@sendClientPayment');

        Route::get('purchase_orders/{purchase_order}', 'EmailController@showPurchaseOrder');
        Route::post('purchase_orders/{purchase_order}', 'EmailController@sendPurchaseOrder');
    });

    Route::group(['prefix' => 'mini/'], function() {
        Route::get('clients/invoices/{client}', 'ClientController@showInvoices');
        Route::get('clients/quotations/{client}', 'ClientController@showQuotations');
        Route::get('clients/sales_orders/{client}', 'ClientController@showSalesOrders');
        Route::get('clients/advance_payments/{client}', 'ClientController@showAdvancePayments');
        Route::get('clients/payments/{client}', 'ClientController@showPayments');

        Route::get('vendors/expenses/{vendor}', 'VendorController@showExpenses');
        Route::get('vendors/payments/{vendor}', 'VendorController@showPayments');
        Route::get('vendors/bills/{vendor}', 'VendorController@showBills');
        Route::get('vendors/purchase_orders/{vendor}', 'VendorController@showPurchaseOrders');
        Route::get('vendors/products/{product}', 'VendorController@showProducts');
        Route::get('vendors/receive_orders/{vendor}', 'VendorController@showRecevieOrders');
        // Route::get('products/quotations/{product}', 'ProductController@showQuotations');
        // Route::get('products/invoices/{product}', 'ProductController@showInvoices');
    });
});

Route::group(['prefix' => 'docs', 'middleware' => 'auth'], function() {
    Route::get('quotations/{quotation}', 'QuotationController@pdf');
    Route::get('sales_orders/{sales_order}', 'SalesOrderController@pdf');
    Route::get('invoices/{invoice}', 'InvoiceController@pdf');
    Route::get('advance_payments/{advance_payment}', 'AdvancePaymentController@pdf');
    Route::get('client_payments/{client_payment}', 'ClientPaymentController@pdf');

    Route::get('expenses/{expense}', 'ExpenseController@pdf');
    Route::get('purchase_orders/{purchase_order}', 'PurchaseOrderController@pdf');
    Route::get('bills/{bill}', 'BillController@pdf');
    Route::get('vendor_payments/{vendor_payment}', 'VendorPaymentController@pdf');
    Route::get('receive_orders/{purchase_order}', 'ReceiveOrderController@pdf');
    Route::get('goods_issue/{goods_issue}', 'GoodsIssueController@pdf');
});

Route::get('{vue?}', 'PageController@index')->where('vue', '[\/\w\.-]*');

