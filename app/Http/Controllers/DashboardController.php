<?php

namespace App\Http\Controllers;

use App\Models\Prestamo;
use App\Models\InventarioDetalle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        $prestamosActivos = Prestamo::whereNull('fecha_entrega')->count();
        $prestamosTardios = Prestamo::whereNull('fecha_entrega')
            ->where('fecha_estimacion', '<', now())
            ->count();

        $fogAccessible = $this->checkFogApi();
        $mysqlAccessible = $this->checkMySQL();

        // Actividad reciente: combinar Prestamos e InventarioDetalles
        $actividadPrestamos = Prestamo::with('persona')
            ->orderByDesc('updated_at')
            ->take(10)
            ->get()
            ->map(function ($item) {
                return [
                    'tipo' => 'prestamo',
                    'nombre' => $item->persona->nombre_completo ?? 'Sin nombre',
                    'updated_at' => $item->updated_at,
                ];
            });

        $actividadInventario = InventarioDetalle::orderByDesc('updated_at')
            ->take(10)
            ->get()
            ->map(function ($item) {
                return [
                    'tipo' => 'inventario',
                    'nombre' => 'Equipo #' . $item->fog_id,
                    'updated_at' => $item->updated_at,
                ];
            });

        $actividadReciente = collect($actividadPrestamos)
            ->merge($actividadInventario)
            ->sortByDesc('updated_at')
            ->take(10)
            ->values();

        $proximosAVencer = Prestamo::with('persona')
            ->whereNull('fecha_entrega')
            ->whereBetween('fecha_estimacion', [now(), now()->addDays(7)])
            ->orderBy('fecha_estimacion')
            ->take(10)
            ->get();

        return view('dashboard', compact(
            'prestamosActivos',
            'prestamosTardios',
            'fogAccessible',
            'mysqlAccessible',
            'actividadReciente',
            'proximosAVencer'
        ));
    }

    private function checkFogApi(): bool
    {
        try {
            $response = Http::withHeaders([
                'fog-api-token' => env('FOG_API_TOKEN'),
                'fog-user-token' => env('FOG_USER_TOKEN'),
            ])->get(env('FOG_SERVER_URL') . '/fog/system/info');

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkMySQL(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
