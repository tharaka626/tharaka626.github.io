<?php

namespace App\Http\Controllers;

use App\Models\InvoiceItem;
use Illuminate\Http\Request;

class InvoiceItemController extends Controller
{
    //destroy invoice item
    public function destroy($id) {
        $invoiceItem = InvoiceItem::findOrFail($id);

        $invoiceItem->delete();

    }
}
