<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    /**
     * Delete event.
     */
    public function destroy($id)
    {
        $datum = Company::query()->find($id);
        $user = Auth::user();

        if (!$datum) {
            return redirect()->back()->with('error', 'Company not found');
        }

        if (!$user->hasRole('admin')) {
            return redirect()->back()->with('error', 'Unauthorized deletion to this resource');
        }

        if(!$datum->delete()){
            return redirect()->back()->with('error', 'Unable to update the resource');
        }

        return redirect()->back()->with('success', 'Company deleted successfully');
    }
        
}
