<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CandidateController extends Controller
{
    public function index()
    {
        try {
            $candidates = Candidate::all();
            $positions = ['Presidente', 'Vicepresidente', 'Senador', 'Diputado', 'Alcalde', 'Concejal'];
        } catch (\Exception $e) {
            \Log::error('Error loading candidates: ' . $e->getMessage());
            $candidates = collect();
            $positions = [];
            session()->flash('error', 'Error loading candidates data.');
        }

        return view('tables-candidates', compact('candidates', 'positions'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'party' => 'required|string|max:255',
                'position' => 'required|string|max:255',
                'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'party_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ], [
                'name.required' => 'El nombre es obligatorio.',
                'party.required' => 'El partido político es obligatorio.',
                'position.required' => 'El cargo es obligatorio.',
                'color.required' => 'El color es obligatorio.',
                'color.regex' => 'El color debe ser un código hexadecimal válido (#RRGGBB).',
            ]);

            $data = $request->only(['name', 'party', 'position', 'color']);

            if ($request->hasFile('photo')) {
                $data['photo'] = $request->file('photo')->store('candidates/photos', 'public');
            }

            if ($request->hasFile('party_logo')) {
                $data['party_logo'] = $request->file('party_logo')->store('candidates/party-logos', 'public');
            }

            Candidate::create($data);

            return redirect()->route('candidates.index')
                ->with('success', 'El candidato fue creado con éxito.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            \Log::error('Error creating candidate: ' . $e->getMessage());
            return redirect()->back()->withInput()
                ->with('error', 'Error al crear el candidato. Por favor intente nuevamente.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $candidate = Candidate::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255',
                'party' => 'required|string|max:255',
                'position' => 'required|string|max:255',
                'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'party_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ], [
                'name.required' => 'El nombre es obligatorio.',
                'party.required' => 'El partido político es obligatorio.',
                'position.required' => 'El cargo es obligatorio.',
                'color.required' => 'El color es obligatorio.',
                'color.regex' => 'El color debe ser un código hexadecimal válido (#RRGGBB).',
            ]);

            $data = $request->only(['name', 'party', 'position', 'color']);

            if ($request->hasFile('photo')) {
                if ($candidate->photo) {
                    Storage::disk('public')->delete($candidate->photo);
                }
                $data['photo'] = $request->file('photo')->store('candidates/photos', 'public');
            }

            if ($request->hasFile('party_logo')) {
                if ($candidate->party_logo) {
                    Storage::disk('public')->delete($candidate->party_logo);
                }
                $data['party_logo'] = $request->file('party_logo')->store('candidates/party-logos', 'public');
            }

            $candidate->update($data);

            return redirect()->route('candidates.index')
                ->with('success', 'El candidato fue actualizado con éxito.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            \Log::error('Error updating candidate: ' . $e->getMessage());
            return redirect()->back()->withInput()
                ->with('error', 'Error al actualizar el candidato. Por favor intente nuevamente.');
        }
    }

    public function destroy($id)
    {
        try {
            $candidate = Candidate::findOrFail($id);
            if ($candidate->photo) {
                Storage::disk('public')->delete($candidate->photo);
            }
            if ($candidate->party_logo) {
                Storage::disk('public')->delete($candidate->party_logo);
            }

            $candidate->delete();

            return redirect()->route('candidates.index')
                ->with('success', 'El candidato fue eliminado correctamente.');
        } catch (\Exception $e) {
            \Log::error('Error deleting candidate: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al eliminar el candidato. Por favor intente nuevamente.');
        }
    }
}