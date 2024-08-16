<?php

namespace App\Http\Controllers\API\V1\Sessions;

use Illuminate\Http\Request;
use App\Services\CodeGenerator;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\SessionsModels\SessionsModels;

class SessionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            return DB::table('sessions_models')->orderByDesc('id')->first();
        } catch (\Throwable $e) {
            return response()->json(
                [
                    'status' => 'error',
                    'code' => 302,
                    'message' => $e->getMessage()
                ]
            );
        }
    }




    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {


            if(empty($request->date_debut)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'error',
                        'message' => "La date de debut est obligatoire"
                    ]
                );
            endif;
            if(empty($request->date_fin)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'error',
                        'message' => "La date fin est obligatoire"
                    ]
                );
            endif;


            $update_sessions = new SessionsModels();
            $update_sessions->date_debut =  date('Y-m-d', strtotime($request->date_debut)) ;
            $update_sessions->date_fin =  date('Y-m-d', strtotime($request->date_fin)) ;
            $update_sessions->description = $request->description;

            $update_sessions->slug = CodeGenerator::generateSlugCode();

            if($update_sessions->save()) :
                return response()->json(
                    [
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'Ok!, La session a été enregistrée avec succès'
                    ]
                );
            else:
                return response()->json(
                    [
                        'status' => 'error',
                        'code' => 300,
                        'message' => "Erreur ! Échec de l'enregistrement de la session, veuillez réessayer!"
                    ]
                );
            endif;
        } catch (\Throwable $e) {
            return response()->json(
                [
                    'status' => 'error',
                    'code' => 302,
                    'message' => $e->getMessage()
                ]
            );
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $slug)
    {
        try {

            if(empty($request->date_debut)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'error',
                        'message' => "La date de debut est obligatoire"
                    ]
                );
            endif;
            if(empty($request->date_fin)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'error',
                        'message' => "La date fin est obligatoire"
                    ]
                );
            endif;


            $update_sessions = SessionsModels::where('slug',$slug)->first();
            $update_sessions->date_debut =  date('Y-m-d', strtotime($request->date_debut)) ;
            $update_sessions->date_fin =  date('Y-m-d', strtotime($request->date_fin)) ;
            $update_sessions->description = $request->description;

            if($update_sessions->save()) :
                return response()->json(
                    [
                        'status' => 'succès',
                        'code' => 200,
                        'message' => 'Ok!, La session a été modifiée avec succès'
                    ]
                );
            else:
                return response()->json(
                    [
                        'status' => 'error',
                        'code' => 300,
                        'message' => "Erreur ! Échec de la modification de la session, veuillez réessayer!"
                    ]
                );
            endif;
        } catch (\Throwable $e) {
            return response()->json(
                [
                    'status' => 'erreur',
                    'code' => 302,
                    'message' => $e->getMessage()
                ]
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        try {
            if (!$slug) :
                return response()->json(
                    [
                        'status' => 'error',
                        'code' => 404,
                        'message' => "Erreur ! Aucun element trouvé"
                    ]
                );
            else :

                DB::table('sessions_models')->where('slug', $slug)->delete();
                return response()->json(
                    [
                        'status' => 'succès',
                        'code' => 200,
                        'message' => "Ok ! Suppression éffectuée"
                    ]
                );

            endif;
        } catch (\Throwable $e) {
            return response()->json(
                [
                    'status' => 'erreur',
                    'code' => 302,
                    'message' => $e->getMessage()
                ]
            );
        }
    }
}
