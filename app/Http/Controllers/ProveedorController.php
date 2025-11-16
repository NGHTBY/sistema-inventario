<?php
namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ProveedorController extends Controller 
{
    public function index()
    {
        $proveedores = Proveedor::orderBy('id','desc')->get();
        return view('proveedores.index', compact('proveedores'));
    }

    public function create()
    { 
        return view('proveedores.create'); 
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'empresa' => 'required',
                'contacto' => 'required'
            ]);
            
            Proveedor::create($request->all());
            return redirect()->route('proveedores.index')->with('success','Proveedor agregado correctamente.');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id) // Cambiado a $id
    { 
        $proveedor = Proveedor::findOrFail($id);
        return view('proveedores.show', compact('proveedor')); 
    }

    public function edit($id) // Cambiado a $id
    { 
        $proveedor = Proveedor::findOrFail($id);
        return view('proveedores.edit', compact('proveedor')); 
    }

    public function update(Request $request, $id) // Cambiado a $id
    {
        try {
            $request->validate([
                'empresa' => 'required',
                'contacto' => 'required'
            ]);
            
            $proveedor = Proveedor::findOrFail($id);
            $proveedor->update($request->all());
            
            return redirect()->route('proveedores.index')->with('success','Proveedor actualizado.');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id) // Cambiado a $id
    {
        try {
            $proveedor = Proveedor::findOrFail($id);
            
            // GUARDAR EL NOMBRE ANTES DE ELIMINAR
            $nombreProveedor = $proveedor->empresa;
            $cantidadProductos = $proveedor->productos()->count();

            // VERIFICAR SI EL PROVEEDOR TIENE PRODUCTOS ASOCIADOS
            if ($cantidadProductos > 0) {
                return redirect()->route('proveedores.index')
                    ->with('error', '❌ No se puede eliminar el proveedor "' . $nombreProveedor . '" porque tiene ' . $cantidadProductos . ' productos asociados.');
            }

            // ELIMINAR EL PROVEEDOR
            $proveedor->delete();
            
            return redirect()->route('proveedores.index')
                ->with('success', '✅ Proveedor "' . $nombreProveedor . '" eliminado exitosamente.');
                
        } catch (\Exception $e) {
            return redirect()->route('proveedores.index')
                ->with('error', '❌ Error al eliminar proveedor: ' . $e->getMessage());
        }
    }

    public function pdf()
    {
        $proveedores = Proveedor::all();
        $pdf = Pdf::loadView('proveedores.pdf', compact('proveedores'))->setPaper('a4','portrait');
        return $pdf->download('reporte_proveedores.pdf');
    }
}