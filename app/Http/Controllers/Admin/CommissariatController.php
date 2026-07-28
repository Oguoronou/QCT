<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commissariat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CommissariatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $commissariats = Commissariat::orderBy('commune')->get();

        return view('Admin.Commissariats.view_commissariats', compact('commissariats'));
    }

    public function create()
    {
        return view('Admin.Commissariats.add_commissariat');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'commune' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
        ]);

        Commissariat::create([
            'name' => $request->name,
            'commune' => $request->commune,
            'city' => $request->city,
            'phone' => $request->phone,
            'address' => $request->address,
            'is_active' => true,
        ]);

        Session::flash('message', 'Commissariat ajouté avec succès !');
        return redirect('admin/commissariats');
    }

    public function edit($id)
    {
        $commissariat = Commissariat::findOrFail($id);

        return view('Admin.Commissariats.edit_commissariat', compact('commissariat'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'commune' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
        ]);

        $commissariat = Commissariat::findOrFail($id);
        $commissariat->update($request->only(['name', 'commune', 'city', 'phone', 'address']));

        Session::flash('message', 'Commissariat mis à jour avec succès !');
        return redirect('admin/commissariats');
    }

    public function toggleActive($id)
    {
        $commissariat = Commissariat::findOrFail($id);
        $commissariat->update(['is_active' => !$commissariat->is_active]);

        Session::flash('message', $commissariat->is_active
            ? 'Commissariat réactivé.'
            : 'Commissariat désactivé.');
        return redirect('admin/commissariats');
    }
}
