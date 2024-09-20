<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Signatory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\SignatoryDetailRequest;

class SignatoryController extends Controller
{
    public function create()
    {
        return Inertia::render('Signatory/Create');
    }


    public function store(SignatoryDetailRequest $request)
    {
        $validated = $request->validated();
    
        Signatory::create($validated);
    
        return redirect()->route('signatorypage')->with('message', 'Signatory added successfully');
    }
    

    public function edit(Signatory $signatory)
    {
        return Inertia::render('Signatory/Edit',[
            'signatory' => $signatory
        ]);
    }

    public function update(SignatoryDetailRequest $request, Signatory $signatory)
    {
        $validated = $request->validated();
    
        $signatory->update($validated);
    
        return redirect()->route('signatorypage')->with('message', 'Signatory updated successfully');
    }
    
    public function destroy(Signatory $signatory)
    {
        $signatory->delete(); 
        return Redirect::back()->with('message','Signatory Deleted Successfuly');
    }
}
