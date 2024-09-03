<?php

namespace App\Http\Controllers\API\V1\Redactions;

use App\Http\Controllers\Controller;
use App\Models\Redactions\DepecheModels;
use App\Models\Redactions\FlashesModels;
use App\Services\CodeGenerator;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FlashController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth:api');
    }

    
    public function index()
    {
        try {
            return DB::table('flashes_models')
            ->join('countries_models','flashes_models.pays_id', '=', 'countries_models.id')
            ->join('rubrique_models','flashes_models.rubrique_id', '=', 'rubrique_models.id')
            ->select(
                'countries_models.pays',
                'countries_models.flag',
                'rubrique_models.rubrique',
                'flashes_models.*'
            )->limit(50)->get();
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

    public function single_flash($slug)
    {

        try {
            return DB::table('flashes_models')
            ->join('countries_models','flashes_models.pays_id', '=', 'countries_models.id')
            ->join('rubrique_models','flashes_models.rubrique_id', '=', 'rubrique_models.id')
            ->select(
                'countries_models.pays',
                'countries_models.flag',
                'rubrique_models.rubrique',
                'flashes_models.*'
            )
            ->where('flashes_models.slug',$slug)->first();
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

    public function filter_on_flash(Request $request)
    {

        try {

            $date_debut = date('Y-m-d', strtotime($request->date_debut));
            $date_fin = new DateTime(date('Y-m-d', strtotime($request->date_fin)));


            $date_fin = $date_fin->add(new DateInterval('P1D'));
            $date_fin = $date_fin->format('Y-m-d');

            $myArry = array($date_debut, $date_fin);


            if (count($request->rubrique_id) == 0 && count($request->pays_id) == 0) :
                return DB::table('flashes_models')
                    ->join('countries_models','depeche_models.pays_id', '=', 'countries_models.id')
                    ->join('rubrique_models', 'flashes_models.rubrique_id', '=', 'rubrique_models.id')
                    ->select('rubrique_models.rubrique', 'countries_models.flag', 'flashes_models.*')
                    ->whereBetween('flashes_models.created_at', $myArry)
                    ->orderBy('flashes_models.id', 'asc')
                    ->get();
            endif;

            if (count($request->rubrique_id) > 0 && count($request->pays_id) == 0) :
                return DB::table('flashes_models')
                    ->join('countries_models','depeche_models.pays_id', '=', 'countries_models.id')
                    ->join('rubrique_models', 'flashes_models.rubrique_id', '=', 'rubrique_models.id')
                    ->select('rubrique_models.rubrique', 'countries_models.flag', 'flashes_models.*')
                    ->whereIn('flashes_models.rubrique_id', $request->rubrique_id)
                    ->whereBetween('flashes_models.created_at', $myArry)
                    ->orderBy('flashes_models.id', 'desc')
                    ->get();
            endif;

            if (count($request->rubrique_id) == 0 && count($request->pays_id) > 0) :
                return DB::table('flashes_models')
                    ->join('countries_models','depeche_models.pays_id', '=', 'countries_models.id')
                    ->join('rubrique_models', 'flashes_models.rubrique_id', '=', 'rubrique_models.id')
                    ->select('rubrique_models.rubrique', 'countries_models.flag', 'flashes_models.*')
                    ->whereIn('flashes_models.pays_id', $request->pays_id)
                    ->whereBetween('flashes_models.created_at', [$date_debut, $date_fin])
                    ->orderBy('flashes_models.id', 'desc')
                    ->get();
            endif;


            if (count($request->rubrique_id) > 0 && count($request->genre_id) > 0) :
                return DB::table('flashes_models')
                    ->join('rubrique_models', 'flashes_models.rubrique_id', '=', 'rubrique_models.id')
                    ->join('countries_models','depeche_models.pays_id', '=', 'countries_models.id')
                    ->select('rubrique_models.rubrique', 'genre_journalistique_models.genre', 'flashes_models.*')
                    ->whereIn('flashes_models.pays_id', $request->pays_id)
                    ->whereIn('flashes_models.rubrique_id', $request->rubrique_id)
                    ->whereBetween('flashes_models.created_at', [$date_debut, $date_fin])
                    ->orderBy('flashes_models.id', 'desc')
                    ->get();
            endif;

        } catch (\Throwable $e) {
            return response()->json(
                [
                    'status' => 'erreur',
                    'code' => 300,
                    'message' => $e->getMessage(),
                ]
            );
        }
    }
    public function get_recente_flash()
    {

        try {
            $get_flash = DB::table('flashes_models')
            ->join('countries_models','flashes_models.pays_id', '=', 'countries_models.id')
            ->join('rubrique_models','flashes_models.rubrique_id', '=', 'rubrique_models.id')
            ->select(
                'countries_models.pays',
                'countries_models.flag',
                'rubrique_models.rubrique',
                'flashes_models.*'
            )
            ->orderBy('flashes_models.id','desc')->limit(20)->get();

            $count_flash = DB::table('flashes_models')->count();

            return response()->json(
                [
                    'flash' => $get_flash,
                    'flash_number' => $count_flash,
                ]
            );
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            if (empty($request->rubrique_id)) :
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "La rubrique du flash est obligatoire"
                    ]
                );
            endif;
            if (empty($request->pays_id)) :
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "Le pays du flash est obligatoire"
                    ]
                );
            endif;

            if (empty($request->contents)) :
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "Le message du flash est obligatoire"
                    ]
                );
            endif;


            $add_flash = new FlashesModels();

            $add_flash->rubrique_id = $request->rubrique_id;
            $add_flash->pays_id = $request->pays_id;
            $add_flash->author = $request->author;
            $add_flash->contenus = $request->contents;
            $add_flash->slug = CodeGenerator::generateRfk();

            if($add_flash->save()):

                return response()->json(
                    [
                        'status' => 'succès',
                        'code' => 200,
                        'slug' => $add_flash->slug,
                        'message' => "Ok ! Le flash a été enregistré avec succès."
                    ]
                );
            else:

                return response()->json(
                    [
                        'status' => 'error',
                        'code' => 300,
                        'message' => "Erreur ! Échec de l'enregistrement du flash, veuillez réessayer!"
                    ]
                );
            endif;


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


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $slug)
    {
        try {
            if (empty($request->rubrique_id)) :
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "La rubrique du flash est obligatoire"
                    ]
                );
            endif;
            if (empty($request->pays_id)) :
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "Le pays du flash est obligatoire"
                    ]
                );
            endif;

            if (empty($request->contents)) :
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "Le message du flash est obligatoire"
                    ]
                );
            endif;


            $update_flash = FlashesModels::where('slug',$slug)->first();

            $update_flash->rubrique_id = $request->rubrique_id;
            $update_flash->pays_id = $request->pays_id;
            $update_flash->contenus = $request->contents;

            if($update_flash->save()):

                return response()->json(
                    [
                        'status' => 'succès',
                        'code' => 200,
                        'slug' => $update_flash->slug,
                        'message' => "Ok ! Le flash a été modifié avec succès."
                    ]
                );
            else:

                return response()->json(
                    [
                        'status' => 'error',
                        'code' => 300,
                        'message' => "Erreur ! Échec de la modification du flash, veuillez réessayer!"
                    ]
                );
            endif;


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
                        'message' => "Erreur!, Aucun element trouvé"
                    ]
                );
            else :

                DB::table('flashes_models')->where('slug', $slug)->delete();
                return response()->json(
                    [
                        'status' => 'succès',
                        'code' => 200,
                        'message' => "Ok!, Suppression éffectuée"
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



    protected function get_date_debut()
    {
        $current_date = new DateTime();
        $date_debut = $current_date->sub(new DateInterval('P9D'));
        $date_debut = $date_debut->format('Y-m-d');
        return $date_debut;
    }

    
    protected function get_date_fin()
    {
        $current_date = new DateTime();
        $date_fin = $current_date->format('Y-m-d');
        return $date_fin;
    }



    public function push_flash($slug)

    {
        try {
            if (!$slug) :
                return response()->json(
                    [
                        'status' => 'erreur',
                        'code' => 404,
                        'message' => "Erreur!, Aucun élément trouvé"
                    ]
                );
            else :
                $status = FlashesModels::where('slug', $slug)->value('status');
                if($status == 1):
                    FlashesModels::where('slug', $slug)->update(['status' => 0]);
                    return response()->json(
                        [
                            'status' => 'succès',
                            'code' => 200,
                            'message' => "Ok ! Votre flash a été retiré."
                        ]
                    );
                else:
                    FlashesModels::where('slug', $slug)->update(['status' => 1]);
                    return response()->json(
                        [
                            'status' => 'succès',
                            'code' => 200,
                            'message' => "Ok ! Votre flash a été publié."
                        ]
                    );
                endif;
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


    public function get_customer_flash($customer)
    {

        try {
            if (!isset($customer)) :
                return response()->json(
                    [
                        'status' => 'error',
                        'code' => 404,
                        'message' => "Erreur!, Aucun élément trouvé"
                    ]
                );
            else :
                return DB::table('flashes_models')
                ->join('countries_models','flashes_models.pays_id', '=', 'countries_models.id')
                ->select('countries_models.pays','countries_models.flag','flashes_models.*')
                ->where('flashes_models.author', $customer)
                ->orderByDesc('flashes_models.id')
                ->get();

            endif;
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




    public function get_flash_hebdo_statistique()
    {


        $date_debut = $this->get_date_debut();

        $date_fin = $this->get_date_fin();

        $count_all = DB::table('flashes_models')->whereBetween('created_at',[$date_debut,$date_fin])
            ->where('status',1)
            ->count();

        $count_ci = DB::table('flashes_models')
            ->join('countries_models','flashes_models.pays_id', '=', 'countries_models.id')
            ->select('countries_models.iso_code')->whereBetween('flashes_models.created_at',[$date_debut,$date_fin])
            ->where('flashes_models.status',1)
            ->where('countries_models.iso_code',"CI")->count();

        $count_bf = DB::table('flashes_models')
            ->join('countries_models','flashes_models.pays_id', '=', 'countries_models.id')
            ->select('countries_models.iso_code')->whereBetween('flashes_models.created_at',[$date_debut,$date_fin])
            ->where('flashes_models.status',1)
            ->where('countries_models.iso_code',"BF")->count();

        $count_ml = DB::table('flashes_models')
            ->join('countries_models','flashes_models.pays_id', '=', 'countries_models.id')
            ->select('countries_models.iso_code')->whereBetween('flashes_models.created_at',[$date_debut,$date_fin])
            ->where('flashes_models.status',1)
            ->where('countries_models.iso_code',"ML")->count();


        return response()->json(
            [
                'tout' => $count_all,
                'ci' => $count_ci,
                'bf' => $count_bf,
                'ml' => $count_ml
            ]
        );

    }

}
