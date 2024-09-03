<?php

namespace App\Http\Controllers\API\V1\Statistiques;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AdminStatistiquesController extends Controller
{

    public function default_statistiques()
    {
        try {
            $all_users = DB::table('administration_models')->count();
            //$all_abonnes = DB::table('abonnes_models')->count();
            $all_abonnements = DB::table('abonnements')->count();

            return [
                'all_users' => $all_users,
                //'all_abonnes' => $all_abonnes,
                'all_abonnements' => $all_abonnements,
            ];



        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }
}
