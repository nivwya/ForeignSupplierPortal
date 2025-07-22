<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InvoiceTabController extends Controller
{
    public function invoiceTab(Request $request){
        return view('tabs.invoices');
    }
}
