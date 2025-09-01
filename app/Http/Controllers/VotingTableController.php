<?php

namespace App\Http\Controllers;

use App\Models\VotingTable;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class VotingTableController extends Controller
{
    public function index()
    {
        try {
            $votingTables = VotingTable::with(['institution'])->get();
            $institutions = Institution::where('active', true)->get();
        } catch (\Exception $e) {
            \Log::error('Error loading voting tables: ' . $e->getMessage());
            $votingTables = collect();
            $institutions = collect();
            session()->flash('error', 'Error loading voting tables data.');
        }
        
        return view('tables-voting-tables', compact('votingTables', 'institutions'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'code' => 'required|string|max:50|unique:voting_tables,code',
                'number' => 'required|integer|min:1',
                'from_name' => 'nullable|string|max:255',
                'to_name' => 'nullable|string|max:255',
                'capacity' => 'required|integer|min:1',
                'status' => 'required|in:active,closed,pending',
                'institution_id' => 'required|exists:institutions,id',
            ], [
                'code.required' => 'El código de mesa es obligatorio.',
                'code.unique' => 'Este código de mesa ya existe.',
                'code.max' => 'El código no puede exceder los 50 caracteres.',
                'number.required' => 'El número de mesa es obligatorio.',
                'number.integer' => 'El número de mesa debe ser un valor numérico.',
                'number.min' => 'El número de mesa debe ser al menos 1.',
                'capacity.required' => 'La capacidad es obligatoria.',
                'capacity.integer' => 'La capacidad debe ser un valor numérico.',
                'capacity.min' => 'La capacidad debe ser al menos 1.',
                'status.required' => 'El estado es obligatorio.',
                'status.in' => 'El estado seleccionado no es válido.',
                'institution_id.required' => 'Debe seleccionar una institución.',
                'institution_id.exists' => 'La institución seleccionada no es válida.',
            ]);
            
            // Check for unique number within the same institution
            $existingTable = VotingTable::where('institution_id', $request->institution_id)
                                        ->where('number', $request->number)
                                        ->first();
                                        
            if ($existingTable) {
                throw ValidationException::withMessages([
                    'number' => 'Ya existe una mesa con este número en la institución seleccionada.',
                ]);
            }
            
            VotingTable::create($request->all());
            
            return redirect()->route('voting-tables.index')
                            ->with('success', 'La mesa de votación fue creada con éxito.');
        } catch (ValidationException $e) {
            return redirect()->back()
                            ->withErrors($e->validator)
                            ->withInput();
        } catch (\Exception $e) {
            \Log::error('Error creating voting table: ' . $e->getMessage());
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Error al crear la mesa de votación. Por favor intente nuevamente.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $votingTable = VotingTable::findOrFail($id);
            
            $request->validate([
                'code' => 'required|string|max:50|unique:voting_tables,code,' . $id,
                'number' => 'required|integer|min:1',
                'from_name' => 'nullable|string|max:255',
                'to_name' => 'nullable|string|max:255',
                'capacity' => 'required|integer|min:1',
                'status' => 'required|in:active,closed,pending',
                'institution_id' => 'required|exists:institutions,id',
            ], [
                'code.required' => 'El código de mesa es obligatorio.',
                'code.unique' => 'Este código de mesa ya existe.',
                'code.max' => 'El código no puede exceder los 50 caracteres.',
                'number.required' => 'El número de mesa es obligatorio.',
                'number.integer' => 'El número de mesa debe ser un valor numérico.',
                'number.min' => 'El número de mesa debe ser al menos 1.',
                'capacity.required' => 'La capacidad es obligatoria.',
                'capacity.integer' => 'La capacidad debe ser un valor numérico.',
                'capacity.min' => 'La capacidad debe ser al menos 1.',
                'status.required' => 'El estado es obligatorio.',
                'status.in' => 'El estado seleccionado no es válido.',
                'institution_id.required' => 'Debe seleccionar una institución.',
                'institution_id.exists' => 'La institución seleccionada no es válida.',
            ]);
            
            // Check for unique number within the same institution, excluding current record
            $existingTable = VotingTable::where('institution_id', $request->institution_id)
                                        ->where('number', $request->number)
                                        ->where('id', '!=', $id)
                                        ->first();
                                        
            if ($existingTable) {
                throw ValidationException::withMessages([
                    'number' => 'Ya existe una mesa con este número en la institución seleccionada.',
                ]);
            }
            
            $votingTable->update($request->all());
            
            return redirect()->route('voting-tables.index')
                            ->with('success', 'La mesa de votación fue actualizada con éxito.');
        } catch (ValidationException $e) {
            return redirect()->back()
                            ->withErrors($e->validator)
                            ->withInput();
        } catch (\Exception $e) {
            \Log::error('Error updating voting table: ' . $e->getMessage());
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Error al actualizar la mesa de votación. Por favor intente nuevamente.');
        }
    }

    public function destroy($id)
    {
        try {
            $votingTable = VotingTable::findOrFail($id);
            $votingTable->delete();
            
            return redirect()->route('voting-tables.index')
                            ->with('success', 'La mesa de votación fue eliminada correctamente.');
        } catch (\Exception $e) {
            \Log::error('Error deleting voting table: ' . $e->getMessage());
            return redirect()->back()
                            ->with('error', 'Error al eliminar la mesa de votación. Por favor intente nuevamente.');
        }
    }
}