<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{

    public function index()
    {
        $companies = Company::paginate(10);
        
        return view('companies.index', compact('companies'));
    }

    /**
     * Display the specified company.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\View\View
     */
    public function show(Company $company)
    {
        $company->load('zone');
        return view('companies.show', compact('company'));
    }
        
}
