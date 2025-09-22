<?php

namespace App\Http\Controllers;

use App\Models\Transaction;

class TestingController extends Controller
{
    public function testInvoice(string $id)
    {
        $transaction = Transaction::find($id);

        return view('pdf.invoice', compact('transaction'));
    }
}
