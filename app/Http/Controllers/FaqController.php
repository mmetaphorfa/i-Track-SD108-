<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FaqController extends Controller
{
    /* ---------------------------------------
        FAQ page
    --------------------------------------- */
    public function index() {
        $faqs = config('faq');
        
        return view('parent.faq.index', compact('faqs'));
    }
}
