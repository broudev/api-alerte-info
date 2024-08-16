<?php

namespace App\Http\Controllers\API\V1\Abonnements;

use DateTime;
use DateInterval;
use Illuminate\Http\Request;
use App\Services\CodeGenerator;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Abonnements\AbonnementsModels;

class AbonnementsController extends Controller
{


    public function index()
    {
        try {
            return  DB::table('abonnements_models')
            ->join('abonnes_models', 'abonnements_models.abonne_id', '=', 'abonnes_models.id')
            ->join('forfait_id', 'abonnements_models.forfait_id','=', 'forfait_id.user_id')
            ->select(
                'abonnes_models.abonne_phone_number',
                'abonnes_models.abonne_name',
                'abonnes_models.abonne_email',
                'abonnements_models.*'
            )
            ->orderBy('abonnements_models.id', 'desc')
            ->limit(100)
            ->get();


        } catch (\Throwable $e) {
            return response()->json(
                [
                    'status' => 'error',
                    'code' => 300,
                    'message' => $e->getMessage(),
                ]
            );
        }
    }



    public function create_local_abonnements(Request $request)
    {
        try {

            if(empty($request->abonne_id)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "Erreur! Le l'identifiant de l'abonné est obligatoire"
                    ]
                );
            endif;

            if(empty($request->forfait_id)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "Erreur! Le forfait de l'abonné est obligatoire"
                    ]
                );
            endif;

            $forfait_info = DB::table('forfaits_abonnements_models')->where('id',$request->forfait_id)->first();

            //$sizeOfCountry = sizeof($request->pays);
            $dateline = 'P'.$forfait_info->duree_forfait.'D';

            $date_fin = new DateTime();
            $date_fin->add(new DateInterval($dateline));
            $date_fin->format('Y-m-d H:i:s');


            $add_abonnement = new AbonnementsModels();
            $add_abonnement->abonne_id = $request->abonne_id;
            $add_abonnement->forfait_id = $request->forfait_id;
            $add_abonnement->date_debut = date('Y-m-d H:i:s', strtotime((now())));
            $add_abonnement->date_fin = $date_fin;
            $add_abonnement->slug = CodeGenerator::generateSlugCode();

            if($add_abonnement->save()):

                DB::table('abonnes_models')->where('id', $request->abonne_id)->update(['status_abonnement' => 1]);

                return response()->json(
                    [
                        'status' => 'success',
                        'code' => 200,
                        'message' => " Ok  !  L'abonnement a été enregistré avec succès"
                    ]
                );
            else:
                return response()->json(
                    [
                        'status' => 'error',
                        'code' => 300,
                        'message' => "Erreur ! Échec de l'enregistrement de l'abonnement, veuillez réessayer!"
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






    public function update(Request $request,$id)
    {

        try {

            if(empty($request->abonne_id)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "Erreur! Le l'identifiant de l'abonné est obligatoire"
                    ]
                );
            endif;

            if(empty($request->forfait_id)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "Erreur! Le forfait de l'abonné est obligatoire"
                    ]
                );
            endif;


            $forfait_info = DB::table('forfaits_abonnements_models')->where('id',$request->forfait_id)->first();

            //$sizeOfCountry = sizeof($request->pays);
            $dateline = 'P'.$forfait_info->duree_forfait.'D';

            $date_fin = new DateTime();
            $date_fin->add(new DateInterval($dateline));
            $date_fin->format('Y-m-d H:i:s');

            $update_abonnement = AbonnementsModels::where('abonne_id',$request->abonne_id)->first();

            $update_abonnement->abonne_id = $request->abonne_id;
            $update_abonnement->forfait_id = $request->forfait_id;
            $update_abonnement->date_fin = $date_fin;

            if($update_abonnement->save()):

                return response()->json(
                    [
                        'status' => 'success',
                        'code' => 200,
                        'message' => " Ok ! La modification de l'abonnement a été effectuée avec succès."
                    ]
                );
            else:
                return response()->json(
                    [
                        'status' => 'error',
                        'code' => 300,
                        'message' => "Erreur ! Échec de la modification de l'abonnement, veuillez réessayer!"
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



    public function reabonnement(Request $request)
    {
        try {


            if(empty($request->abonne_id)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "Erreur! Le l'identifiant de l'abonné est obligatoire"
                    ]
                );
            endif;

            if(empty($request->forfait_id)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "Erreur! Le forfait de l'abonné est obligatoire"
                    ]
                );
            endif;


            $forfait_info = DB::table('forfaits_abonnements_models')->where('id',$request->forfait_id)->first();

            //$sizeOfCountry = sizeof($request->pays);
            $dateline = 'P'.$forfait_info->duree_forfait.'D';

            $date_fin = new DateTime();
            $date_fin->add(new DateInterval($dateline));
            $date_fin->format('Y-m-d H:i:s');

            $reabonnement = AbonnementsModels::where('abonne_id',$request->abonne_id)->first();

            $reabonnement->abonne_id = $request->abonne_id;
            $reabonnement->forfait_id = $request->forfait_id;
            $reabonnement->date_fin = $date_fin;

            if($reabonnement->save()):

                return response()->json(
                    [
                        'code' => 200,
                        'status' => 'succès',
                        'message' => "Réabonnement éffectué avec succès 💚"
                    ]
                );
            else:
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "Erreur!  La mise à jour de l'abonnement a échouée, veuillez réessayer!"
                    ]
                );
            endif;
        } catch (\Throwable $e)
        {
            return response()->json(
                [
                    'status' => 'error',
                    'code' => 302,
                    'message' => $e->getMessage()
                ]
            );
        }

    }
}
