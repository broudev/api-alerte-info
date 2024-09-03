<?php

namespace App\Http\Controllers\API\V1\Redactions;

use App\Http\Controllers\Controller;
use App\Models\DepecheViews\DepecheViewsModels;
use App\Models\Redactions\DepecheModels;
use App\Services\CodeGenerator;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DepecheController extends Controller
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
            $get_depeche = DB::table('depeche_models')
                ->join('countries_models', 'depeche_models.pays_id', '=', 'countries_models.id')
                ->join('rubrique_models', 'depeche_models.rubrique_id', '=', 'rubrique_models.id')
                ->join('genre_journalistique_models', 'depeche_models.genre_id', '=', 'genre_journalistique_models.id')
                ->select(
                    'countries_models.pays',
                    'countries_models.flag',
                    'rubrique_models.rubrique',
                    'genre_journalistique_models.genre',
                    'depeche_models.*'
                )
                ->orderBy('depeche_models.id', 'desc')
                ->limit(100)
                ->get();

            $count_depeche = DB::table('depeche_models')->count();

            return response()->json(
                [
                    'depeche' => $get_depeche,
                    'depeche_number' => $count_depeche,
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


    public static function get_article_with_rubrique($rubrique)
    {

        try {
            $article = DB::table('depeche_models')
                ->join('countries_models', 'depeches.pays_id', '=', 'countries_models.id')
                ->select('countries_models.pays', 'countries_models.flag', 'depeche_models.*')
                ->where('depeche_models.rubrique_libelle', $rubrique)
                ->where('articles_models.status', 1)
                ->orderBy('articles_models.id', 'desc')
                ->limit(50)
                ->get();

            return [
                'depeche' => $article,
                'categorie' => $rubrique
            ];
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
            if (empty($request->titre)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "Le titre de la dépêche est obligatoire"
                    ]
                );
            endif;
            if (empty($request->rubrique_id)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "La rubrique de la dépêche est obligatoire"
                    ]
                );
            endif;
            if (empty($request->pays_id)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "Le pays de la dépêche est obligatoire"
                    ]
                );
            endif;

            if (empty($request->genre_id)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "Le genre journalistique de la dépêche est obligatoire"
                    ]
                );
            endif;


            if (empty($request->lead)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "Le lead de l'article est obligatoire"
                    ]
                );
            endif;
            if (empty($request->contenus)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "Le contenu de l'article est obligatoire"
                    ]
                );
            endif;


            if (empty($request->media_url)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "Vous n'avez pas choisir d'image"
                    ]
                );
            endif;



            $add_depeche = new DepecheModels();

            $add_depeche->titre = $request->titre;
            $add_depeche->rubrique_id = $request->rubrique_id;
            $add_depeche->pays_id = $request->pays_id;
            $add_depeche->genre_id = $request->genre_id;
            $add_depeche->author = $request->author;
            $add_depeche->lead = $request->lead;
            $add_depeche->legende = $request->legende;
            $add_depeche->contenus = $request->contenus;
            $add_depeche->media_url = $request->media_url;
            $add_depeche->slug = Str::slug($request->titre);

            if ($add_depeche->save()):

                return response()->json(
                    [
                        'status' => 'succès',
                        'code' => 200,
                        'slug' => $add_depeche->slug,
                        'message' => "Ok ! La dépêche a été enregistrée avec succès."
                    ]
                );
            else:

                return response()->json(
                    [
                        'status' => 'error',
                        'code' => 300,
                        'message' => "Erreur ! Échec de l'enregistrement de la dépêche, veuillez réessayer!"
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


    // DETAIL ARTICLE SUR ALERTE INFO
    public function get_detail_depeche_on_backend($slug)
    {
        try {
            if (!isset($slug)):
                return response()->json(
                    [
                        'status' => 'error',
                        'code' => 404,
                        'message' => "Erreur!, Aucun élément trouvé"
                    ]
                );
            else:

                return DB::table('depeche_models')
                ->join('countries_models', 'depeche_models.pays_id', '=', 'countries_models.id')
                ->join('rubrique_models', 'depeche_models.rubrique_id', '=', 'rubrique_models.id')
                ->join('genre_journalistique_models', 'depeche_models.genre_id', '=', 'genre_journalistique_models.id')
                ->select(
                    'countries_models.pays',
                    'countries_models.flag',
                    'rubrique_models.rubrique',
                    'genre_journalistique_models.genre',
                    'depeche_models.*'
                )
                ->where('depeche_models.slug', $slug)
                ->first();
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
            if (empty($request->titre)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "Le titre de l'article est obligatoire"
                    ]
                );
            endif;
            if (empty($request->rubrique_id)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "La rubrique de la dépêche est obligatoire"
                    ]
                );
            endif;
            if (empty($request->pays_id)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "Le pays de la dépêche est obligatoire"
                    ]
                );
            endif;

            if (empty($request->genre_id)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "Le genre journalistique de la dépêche est obligatoire"
                    ]
                );
            endif;
            if (empty($request->lead)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "Le lead de l'article est obligatoire"
                    ]
                );
            endif;
            if (empty($request->contenus)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "Le contenu de l'article est obligatoire"
                    ]
                );
            endif;


            if (empty($request->media_url)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "Vous n'avez pas choisir d'image"
                    ]
                );
            endif;

            $update_depeche = DepecheModels::where('slug', $slug)->first();

            $update_depeche->titre = $request->titre;
            $update_depeche->rubrique_id = $request->rubrique_id;
            $update_depeche->pays_id = $request->pays_id;
            $update_depeche->genre_id = $request->genre_id;
            $update_depeche->lead = $request->lead;
            $update_depeche->legende = $request->legende;
            $update_depeche->contenus = $request->contenus;
            $update_depeche->media_url = $request->media_url;
            $update_depeche->slug = Str::slug($request->titre);

            if ($update_depeche->save()):

                return response()->json(
                    [
                        'status' => 'succès',
                        'code' => 200,
                        'slug' => $update_depeche->slug,
                        'message' => "Ok ! La dépêche a été modifiée avec succès."
                    ]
                );
            else:

                return response()->json(
                    [
                        'status' => 'error',
                        'code' => 300,
                        'message' => "Erreur ! Échec de la modification de la dépêche, veuillez réessayer!"
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
            if (!$slug):
                return response()->json(
                    [
                        'status' => 'error',
                        'code' => 404,
                        'message' => "Erreur!, Aucun élément trouvé"
                    ]
                );
            else:
                DepecheModels::where('slug', $slug)->delete();

                return response()->json(
                    [
                        'status' => 'success',
                        'code' => 200,
                        'message' => "Ok!,Suppression effectuée"
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


    public function push_depeche($slug)
    {
        try {
            if (!$slug):
                return response()->json(
                    [
                        'status' => 'erreur',
                        'code' => 404,
                        'message' => "Erreur!, Aucun élément trouvé"
                    ]
                );
            else:
                $status = DepecheModels::where('slug', $slug)->value('status');
                if ($status == 1):
                    DepecheModels::where('slug', $slug)->update(['status' => 0]);
                    return response()->json(
                        [
                            'status' => 'succès',
                            'code' => 200,
                            'message' => "Ok ! Votre dépêche a été retirée en ligne."
                        ]
                    );
                else:
                    DepecheModels::where('slug', $slug)->update(['status' => 1]);
                    return response()->json(
                        [
                            'status' => 'succès',
                            'code' => 200,
                            'message' => "Ok ! Votre dépêche a été publiée en ligne."
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


    // GET CUSTOMER ARTICLES
    public function get_customer_depeche($customer)
    {

        try {
            if (!isset($customer)):
                return response()->json(
                    [
                        'status' => 'error',
                        'code' => 404,
                        'message' => "Erreur!, Aucun élément trouvé"
                    ]
                );
            else:
                return DB::table('depeche_models')
                    ->join('countries_models', 'depeche_models.pays_id', '=', 'countries_models.id')
                    ->join('rubrique_models', 'depeche_models.rubrique_id', '=', 'rubrique_models.id')
                    ->join('genre_journalistique_models', 'depeche_models.genre_id', '=', 'genre_journalistique_models.id')
                    ->select(
                        'countries_models.pays',
                        'countries_models.flag',
                        'rubrique_models.rubrique',
                        'genre_journalistique_models.genre',
                        'depeche_models.*'
                    )
                    ->where('depeche_models.author', $customer)
                    ->orderByDesc('depeche_models.id')
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


    




    // FILTER ARTICLE

    public function filter_on_depeche(Request $request)
    {

        try {
            $date_debut = date('Y-m-d', strtotime($request->date_debut));
            $date_fin = new DateTime(date('Y-m-d', strtotime($request->date_fin)));


            $date_fin = $date_fin->add(new DateInterval('P1D'));
            $date_fin = $date_fin->format('Y-m-d');

            $myArry = array($date_debut, $date_fin);

            if (count($request->rubrique_libelle) > 0 && count($request->pays_id) == 0):
                return DB::table('depeche_models')
                    ->join('countries_models', 'depeche_models.pays_id', '=', 'countries_models.id')
                    ->select('countries_models.pays', 'countries_models.flag', 'countries_models.iso_code', 'depeche_models.*')
                    ->whereIn('depeche_models.rubrique_libelle', $request->rubrique_libelle)
                    ->whereBetween('depeche_models.created_at', $myArry)
                    ->orderBy('id', 'desc')
                    ->get();
            endif;

            if (count($request->rubrique_libelle) == 0 && count($request->pays_id) > 0):
                return DB::table('depeche_models')
                    ->join('countries_models', 'depeche_models.pays_id', '=', 'countries_models.id')
                    ->select('countries_models.pays', 'countries_models.flag', 'countries_models.iso_code', 'depeche_models.*')
                    ->whereIn('depeche_models.pays_id', $request->pays_id)
                    ->whereBetween('depeche_models.created_at', [$date_debut, $date_fin])
                    ->orderBy('id', 'desc')
                    ->get();
            endif;


            if (count($request->rubrique_libelle) > 0 && count($request->pays_id) > 0):
                return DB::table('depeche_models')
                    ->join('pays', 'depeche_models.pays_id', '=', 'pays.id')
                    ->select('countries_models.pays', 'countries_models.flag', 'countries_models.iso_code', 'depeche_models.*')
                    ->whereIn('depeche_models.pays_id', $request->pays_id)
                    ->whereIn('depeche_models.rubrique_libelle', $request->rubrique_libelle)
                    ->whereBetween('depeche_models.created_at', [$date_debut, $date_fin])
                    ->orderBy('id', 'desc')
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

    // GET FLASH INFO ARTICLE

    public function get_depeche_flash_info()
    {
        try {
            return DB::table('depeche_models')->orderBy('id', 'desc')->where('status', 1)->limit(8)->get();
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

    // RECENTE ARTICLES

    public function get_recente_depeche()
    {

        try {
            $get_depeches = DB::table('depeche_models')
                ->join('countries_models', 'depeche_models.pays_id', '=', 'countries_models.id')
                ->select('countries_models.pays', 'countries_models.flag', 'depeche_models.*')
                ->orderBy('id', 'desc')->limit(20)->get();

            $count_depeches = DB::table('depeche_models')->count();

            return response()->json(
                [
                    'depeches' => $get_depeches,
                    'depeche_number' => $count_depeches,
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

    // STATISTIQUE

    public function get_depeche_hebdo_statistique()
    {

        try {
            $date_debut = $this->get_date_debut();

            $date_fin = $this->get_date_fin();

            $count_all = DB::table('depeche_models')->whereBetween('created_at', [$date_debut, $date_fin])
                ->where('status', 1)
                ->count();

            $count_ci = DB::table('depeche_models')
                ->join('countries_models', 'depeche_models.pays_id', '=', 'countries_models.id')
                ->select('countries_models.iso_code')->whereBetween('depeche_models.created_at', [$date_debut, $date_fin])
                ->where('depeche_models.status', 1)
                ->where('countries_models.iso_code', "CI")->count();

            $count_bf = DB::table('depeche_models')
                ->join('countries_models', 'depeche_models.pays_id', '=', 'countries_models.id')
                ->select('countries_models.iso_code')->whereBetween('depeche_models.created_at', [$date_debut, $date_fin])
                ->where('depeche_models.status', 1)
                ->where('countries_models.iso_code', "BF")->count();

            $count_ml = DB::table('depeche_models')
                ->join('countries_models', 'depeche_models.pays_id', '=', 'countries_models.id')
                ->select('countries_models.iso_code')
                ->whereBetween('depeche_models.created_at', [$date_debut, $date_fin])
                ->where('depeche_models.status', 1)
                ->where('countries_models.iso_code', "ML")->count();


            return response()->json(
                [
                    'tout' => $count_all,
                    'ci' => $count_ci,
                    'bf' => $count_bf,
                    'ml' => $count_ml
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
}
