<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Quotation\Quotation;
use App\AdvancePayment\AdvancePayment;
use App\SalesOrder\SalesOrder;
use App\ClientPayment\ClientPayment;
use App\PurchaseOrder\PurchaseOrder;
use App\Invoice\Invoice;
use App\Mail\SendDocument;
use Mail;

class EmailController extends Controller
{
    protected $company;

    function __construct()
    {
        $this->company = settings()->get('company_name');
    }

    public function showQuotation($id)
    {
        $user = auth()->user();

        $quotation = Quotation::with(['client'])->findOrFail($id);

        $message = 'Dear '.$quotation->client->person.','.PHP_EOL.PHP_EOL.PHP_EOL;
        $message .= 'Thank your for contacting us. ';
        $message .= 'Please find the PDF attachment. '.PHP_EOL.PHP_EOL;
        $message .= 'If you have any queries regarding this quotation,';
        $message .= ' feel free to contact us.'.PHP_EOL.PHP_EOL.PHP_EOL;
        $message .= $user->email_signature;

        $form = [
            'to' => $quotation->client->email,
            'bcc' => $user->email,
            'subject' => 'Quotation '.$quotation->number. ' from '.$this->company,
            'message' => $message
        ];
        return api([
            'form' => $form,
            'warning' => $this->getWarnings()
        ]);
    }

    public function sendQuotation($id, Request $request)
    {
        $quotation = Quotation::with(['client'])->findOrFail($id);

        $request->validate([
            'to' => 'required|email|string|max:255',
            'bcc' => 'required|email|string|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000'
        ]);

        Mail::send(new SendDocument(
            $request->only('to', 'bcc','subject', 'message'),
            'quotation', $quotation
        ));


        if($quotation->status_id === 1) {
            $quotation->status_id = 2;
            $quotation->save();
        }

        return api([
            'saved' => true,
            'id' => $quotation->id
        ]);
    }

    public function showAdvancePayment($id)
    {
        $user = auth()->user();

        $advancePayment = AdvancePayment::with(['client'])->findOrFail($id);

        $message = 'Dear '.$advancePayment->client->person.','.PHP_EOL.PHP_EOL.PHP_EOL;
        $message .= 'Thank your for your payment. ';
        $message .= 'Please find the PDF attachment. '.PHP_EOL.PHP_EOL;
        $message .= 'If you have any queries regarding this advance payment receipt,';
        $message .= ' feel free to contact us.'.PHP_EOL.PHP_EOL.PHP_EOL;
        $message .= $user->email_signature;

        $form = [
            'to' => $advancePayment->client->email,
            'bcc' => $user->email,
            'subject' => 'Advance Payment Receipt '.$advancePayment->number. ' from '.$this->company,
            'message' => $message
        ];

        return api([
            'form' => $form,
            'warning' => $this->getWarnings()
        ]);
    }

    public function sendAdvancePayment($id, Request $request)
    {
        $advancePayment = AdvancePayment::with(['client'])->findOrFail($id);

        $request->validate([
            'to' => 'required|email|string|max:255',
            'bcc' => 'required|email|string|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000'
        ]);

        Mail::send(new SendDocument(
            $request->only('to', 'bcc','subject', 'message'),
            'advance_payment', $advancePayment
        ));

        return api([
            'saved' => true,
            'id' => $advancePayment->id
        ]);
    }

    public function showSalesOrder($id)
    {
        $user = auth()->user();

        $salesOrder = SalesOrder::with(['client'])->findOrFail($id);

        $message = 'Dear '.$salesOrder->client->person.','.PHP_EOL.PHP_EOL.PHP_EOL;
        $message .= 'Thank your for your sales order. ';
        $message .= 'Please find the PDF attachment. '.PHP_EOL.PHP_EOL;
        $message .= 'If you have any queries regarding this sales order,';
        $message .= ' feel free to contact us.'.PHP_EOL.PHP_EOL.PHP_EOL;
        $message .= $user->email_signature;

        $form = [
            'to' => $salesOrder->client->email,
            'bcc' => $user->email,
            'subject' => 'Sales Order '.$salesOrder->number. ' from '.$this->company,
            'message' => $message
        ];

        return api([
            'form' => $form,
            'warning' => $this->getWarnings()
        ]);
    }

    public function sendSalesOrder($id, Request $request)
    {
        $salesOrder = SalesOrder::with(['client'])->findOrFail($id);

        $request->validate([
            'to' => 'required|email|string|max:255',
            'bcc' => 'required|email|string|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000'
        ]);

        Mail::send(new SendDocument(
            $request->only('to', 'bcc','subject', 'message'),
            'sales_order', $salesOrder
        ));


        if($salesOrder->status_id === 1) {
            $salesOrder->status_id = 2;
            $salesOrder->save();
        }

        return api([
            'saved' => true,
            'id' => $salesOrder->id
        ]);
    }

    public function showInvoice($id)
    {
        $user = auth()->user();

        $invoice = Invoice::with(['client'])->findOrFail($id);

        $message = 'Dear '.$invoice->client->person.','.PHP_EOL.PHP_EOL.PHP_EOL;
        $message .= 'Thank your for your business. ';
        $message .= 'Please find the Invoice PDF attachment. '.PHP_EOL.PHP_EOL;
        $message .= 'If you have any queries regarding this invoice,';
        $message .= ' feel free to contact us.'.PHP_EOL.PHP_EOL.PHP_EOL;
        $message .= $user->email_signature;

        $form = [
            'to' => $invoice->client->email,
            'bcc' => $user->email,
            'subject' => 'Invoice '.$invoice->number. ' from '.$this->company,
            'message' => $message
        ];

        return api([
            'form' => $form,
            'warning' => $this->getWarnings()
        ]);
    }

    public function sendInvoice($id, Request $request)
    {
        $invoice = Invoice::with(['client'])->findOrFail($id);

        $request->validate([
            'to' => 'required|email|string|max:255',
            'bcc' => 'required|email|string|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000'
        ]);

        Mail::send(new SendDocument(
            $request->only('to', 'bcc','subject', 'message'),
            'invoice', $invoice
        ));


        if($invoice->status_id === 1) {
            $invoice->status_id = 2;
            $invoice->save();
        }

        return api([
            'saved' => true,
            'id' => $invoice->id
        ]);
    }

    public function showClientPayment($id)
    {
        $user = auth()->user();

        $clientPayment = ClientPayment::with(['client'])->findOrFail($id);

        $message = 'Dear '.$clientPayment->client->person.','.PHP_EOL.PHP_EOL.PHP_EOL;
        $message .= 'Thank your for your payment. ';
        $message .= 'Please find the PDF attachment. '.PHP_EOL.PHP_EOL;
        $message .= 'If you have any queries regarding this payment receipt,';
        $message .= ' feel free to contact us.'.PHP_EOL.PHP_EOL.PHP_EOL;
        $message .= $user->email_signature;

        $form = [
            'to' => $clientPayment->client->email,
            'bcc' => $user->email,
            'subject' => 'Payment Receipt '.$clientPayment->number. ' from '.$this->company,
            'message' => $message
        ];

        return api([
            'form' => $form,
            'warning' => $this->getWarnings()
        ]);
    }

    public function sendClientPayment($id, Request $request)
    {
        $clientPayment = ClientPayment::with(['client'])->findOrFail($id);

        $request->validate([
            'to' => 'required|email|string|max:255',
            'bcc' => 'required|email|string|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000'
        ]);

        Mail::send(new SendDocument(
            $request->only('to', 'bcc','subject', 'message'),
            'client_payment', $clientPayment
        ));

        return api([
            'saved' => true,
            'id' => $clientPayment->id
        ]);
    }

    public function showPurchaseOrder($id)
    {
        $user = auth()->user();

        $purchaseOrder = PurchaseOrder::with(['vendor'])->findOrFail($id);

        $message = 'Dear '.$purchaseOrder->vendor->person.','.PHP_EOL.PHP_EOL.PHP_EOL;
        $message .= 'Please find the PDF attachment of the purchase order. '.PHP_EOL.PHP_EOL;
        $message .= 'If you have any queries regarding this purchase order,';
        $message .= ' feel free to contact us.'.PHP_EOL.PHP_EOL.PHP_EOL;
        $message .= $user->email_signature;

        $form = [
            'to' => $purchaseOrder->vendor->email,
            'bcc' => $user->email,
            'subject' => 'Purchase Order '.$purchaseOrder->number. ' from '.$this->company,
            'message' => $message
        ];

        return api([
            'form' => $form,
            'warning' => $this->getWarnings()
        ]);
    }

    public function sendPurchaseOrder($id, Request $request)
    {
        $purchaseOrder = PurchaseOrder::with(['vendor'])->findOrFail($id);

        $request->validate([
            'to' => 'required|email|string|max:255',
            'bcc' => 'required|email|string|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000'
        ]);

        Mail::send(new SendDocument(
            $request->only('to', 'bcc','subject', 'message'),
            'purchase_order', $purchaseOrder
        ));


        if($purchaseOrder->status_id === 1) {
            $purchaseOrder->status_id = 2;
            $purchaseOrder->save();
        }

        return api([
            'saved' => true,
            'id' => $purchaseOrder->id
        ]);
    }

    protected function getWarnings()
    {
        $settings = settings()->getMany(['sent_from_email', 'sent_from_name']);

        if(is_null($settings['sent_from_email']) || is_null($settings['sent_from_name'])) {
            return [
                'email' => ['Please provide "sent from email" and "sent from name" in settings, before you sent email.']
            ];
        }

        return null;
    }
}
