<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Treatment;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Toon de lijst met facturen.
     */
    public function index()
    {
        $invoices = Invoice::paginate(12);
        return view('Invoice.index', compact('invoices'));
    }

    /**
     * Toon details van een specifieke factuur.
     */
    public function show($id)
    {
        $invoice = Invoice::findOrFail($id);
        $patient = $invoice->patient; // Relatie gebruiken

        return view('Invoice.show', compact('invoice', 'patient'));
    }

    /**
     * Toon de create view voor een nieuwe factuur.
     */
    public function create()
    {

        $lastInvoice = Invoice::latest('id')->first();
        $newNumber = $lastInvoice ? str_pad($lastInvoice->number + 1, 6, '0', STR_PAD_LEFT) : '000001';

        $patients = Patient::all(['id', 'name']);
        $treatments = Treatment::all();
        $treatmentTypes = Treatment::distinct()->pluck('treatment_type');

        return view('Invoice.create', compact('patients', 'treatments', 'treatmentTypes', 'newNumber'));
    }

    /**
     * Sla een nieuwe factuur op.
     */
    public function store(Request $request)
    {
        // Haal het laatste factuurnummer op
        $lastInvoice = Invoice::latest('id')->first();
        $newNumber = $lastInvoice ? str_pad($lastInvoice->number + 1, 6, '0', STR_PAD_LEFT) : '000001';

        // dd($request->all());

        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'treatment_type' => 'required|string',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'status' => 'nullable|string|in:in behandeling,betaald,onbetaald',
        ]);


        // dd($request->all());

         // Zoek het bijbehorende treatment_id op basis van treatment_type
        $treatment = Treatment::where('treatment_type', $request->treatment_type)->first();
        if (!$treatment) {
            return back()->withErrors(['treatment_type' => 'Geen behandeling gevonden voor het geselecteerde type.'])->withInput();
        }

        $validated['treatment_id'] = $treatment->id;
        $validated['number'] = $newNumber;

        // maak een nieuwe factuur aan
        Invoice::create($validated);

        return redirect()->route('invoice.index')->with('success', 'Factuur succesvol aangemaakt.');
    }

    /**
     * Toon de edit view voor een bestaande factuur.
     */
    public function edit($id)
    {
        $invoice = Invoice::findOrFail($id);
        $patients = Patient::all(['id', 'name']);
        $treatments = Treatment::all();
        $treatmentTypes = Treatment::distinct()->pluck('treatment_type');

        return view('Invoice.edit', compact('invoice', 'patients', 'treatments', 'treatmentTypes'));
    }

    /**
     * Werk een bestaande factuur bij.
     */
    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        // Valideer invoer
        $validated = $request->validate([
            'treatment_id' => 'required|exists:treatments,id',
            'patient_id' => 'required|exists:patients,id',
            'number' => 'required|max:6',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'status' => 'nullable|string|in:in behandeling,betaald,onbetaald',
        ]);

        // Update de factuur
        $invoice->update($validated);

        return redirect()->route('invoice.index')->with('success', 'Factuur succesvol bijgewerkt.');
    }

    /**
     * Verwijder een factuur.
     */
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();

        return redirect()->route('invoice.index')->with('success', 'Factuur succesvol verwijderd.');
    }

    /**
     * Genereer het volgende factuurnummer.
     */
    public function latestNumber()
    {
        $latestInvoice = Invoice::orderBy('number', 'desc')->first();
        $nextNumber = $latestInvoice ? intval($latestInvoice->number) + 1 : 1;

        return response()->json(['nextNumber' => str_pad($nextNumber, 6, '0', STR_PAD_LEFT)]);
    }
}
