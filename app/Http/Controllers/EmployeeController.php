<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees!
     */
    public function index()
    {
        $employees = Employee::with('person')->paginate(10);
        return view('Employee.index', compact('employees'));
    }

    /**
     * Show the form to create a new employee.
     */
    public function create()
    {
        $persons = Person::all(); // Fetch all persons
        return view('Employee.create', compact('persons'));
    }


    /**
     * Store a new employee in the database.
     */
    public function store(Request $request)
{
    // Validate the incoming request data
    $validated = $request->validate([
        'person_id' => 'required|exists:people,id',
        'number' => 'required|string|max:255',
        'employee_type' => 'required|string|max:255',
        'specialization' => 'nullable|string|max:255',
        'availability' => 'nullable|string|max:255',
        'comment' => 'nullable|string|max:500',
    ]);

        // Retrieve the person
        $person = Person::findOrFail($validated['person_id']);

        // Create a new employee record
        Employee::create([
            'person_id' => $validated['person_id'],
            'user_id' => Auth::id(),
            'name' => $person->name, // Use the name from the selected person
            'email' => $person->email, // Use the email from the selected person
            'number' => $validated['number'],
            'employee_type' => $validated['employee_type'],
            'specialization' => $validated['specialization'],
            'availability' => $validated['availability'],
            'comment' => $validated['comment'],
        ]);

        // Redirect with a success message
        return redirect()->route('employees.index')->with('success', 'Medewerker succesvol aangemaakt.');
    }



    /**
     * Show details of a specific employee.
     */
    public function show(Employee $employee)
    {
        // Load the employee's relationships
        $employee->load('person');

        // Get all persons for the dropdown
        $persons = Person::all();

        return view('Employee.show', compact('employee', 'persons'));
    }

    /**
     * Show the form to edit an employee.
     */
    public function edit(Employee $employee)
    {
        $persons = Person::pluck('name', 'id');
        return view('Employee.edit', compact('employee', 'persons'));
    }

    /**
     * Update an employee in the database.
     */
    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $employee->update([
            'name' => $request->input('name'),
            'employee_type' => $request->input('employee_type'),
            'email' => $request->input('email'),
            'specialization' => $request->input('specialization'),
        ]);

        return redirect()->route('employee.index')->with('success', 'Medewerker succesvol bijgewerkt.');
    }


    /**
     * Delete an employee from the database.
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }
}
