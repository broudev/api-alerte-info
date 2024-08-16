<?php

namespace App\Http\Controllers\API\V1\Frontend;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class FrontendAlerteInfoController extends Controller
{



    public function get_alerteinfo_home_page_data()
    {
        try {

            $fil_actualite_data = DB::table('depeche_models')
            ->join('countries_models', 'depeche_models.pays_id', '=', 'countries_models.id')
            ->join('rubrique_models', 'depeche_models.rubrique_id', '=', 'rubrique_models.id')
            ->join('genre_journalistique_models', 'depeche_models.genre_id', '=', 'genre_journalistique_models.id')
            ->select('countries_models.pays','countries_models.flag','rubrique_models.rubrique','genre_journalistique_models.genre','depeche_models.*')
            //->whereDate('depeche_models.created_at','>=', Carbon::now()->subDay())
            ->where('depeche_models.status',1)
            ->orderBy('depeche_models.id', 'desc')
            ->limit(18)
            ->get();

            $event_keyword_data = DB::table('events_key_words_models')->get();


            $recents_depeche_data = DB::table('depeche_models')
            ->join('countries_models', 'depeche_models.pays_id', '=', 'countries_models.id')
            ->join('rubrique_models', 'depeche_models.rubrique_id', '=', 'rubrique_models.id')
            ->join('genre_journalistique_models', 'depeche_models.genre_id', '=', 'genre_journalistique_models.id')
            ->select('countries_models.pays','countries_models.flag','rubrique_models.rubrique','genre_journalistique_models.genre','depeche_models.*')
            //->whereDate('depeche_models.created_at','>=', Carbon::now()->subHours(48))
            ->where('depeche_models.status',1)
            ->orderByDesc('depeche_models.id')
            ->limit(8)
            ->get();

            $politique_news_data = DB::table('depeche_models')
            ->join('countries_models', 'depeche_models.pays_id', '=', 'countries_models.id')
            ->join('rubrique_models', 'depeche_models.rubrique_id', '=', 'rubrique_models.id')
            ->join('genre_journalistique_models', 'depeche_models.genre_id', '=', 'genre_journalistique_models.id')
            ->select('countries_models.pays','countries_models.flag','rubrique_models.rubrique','genre_journalistique_models.genre','depeche_models.*')
            //->whereDate('depeche_models.created_at','>=', Carbon::now()->subWeek())
            ->where('rubrique_models.rubrique', 'POLITIQUE')
            ->where('depeche_models.status',1)
            ->orderByDesc('depeche_models.id')
            ->limit(4)
            ->get();


            $economie_news_data = DB::table('depeche_models')
            ->join('countries_models', 'depeche_models.pays_id', '=', 'countries_models.id')
            ->join('rubrique_models', 'depeche_models.rubrique_id', '=', 'rubrique_models.id')
            ->join('genre_journalistique_models', 'depeche_models.genre_id', '=', 'genre_journalistique_models.id')
            ->select('countries_models.pays','countries_models.flag','rubrique_models.rubrique','genre_journalistique_models.genre','depeche_models.*')
            //->whereDate('depeche_models.created_at','>=', Carbon::now()->subWeek())
            ->where('rubrique_models.rubrique', 'ECONOMIE')
            ->where('depeche_models.status',1)
            ->orderByDesc('depeche_models.id')
            ->limit(4)
            ->get();

            $societe_news_data = DB::table('depeche_models')
            ->join('countries_models', 'depeche_models.pays_id', '=', 'countries_models.id')
            ->join('rubrique_models', 'depeche_models.rubrique_id', '=', 'rubrique_models.id')
            ->join('genre_journalistique_models', 'depeche_models.genre_id', '=', 'genre_journalistique_models.id')
            ->select('countries_models.pays','countries_models.flag','rubrique_models.rubrique','genre_journalistique_models.genre','depeche_models.*')
            //->whereDate('depeche_models.created_at','>=', Carbon::now()->subWeek())
            ->where('rubrique_models.rubrique', 'SOCIETE')
            ->where('depeche_models.status',1)
            ->orderByDesc('depeche_models.id')
            ->limit(4)
            ->get();

            $securite_news_data = DB::table('depeche_models')
            ->join('countries_models', 'depeche_models.pays_id', '=', 'countries_models.id')
            ->join('rubrique_models', 'depeche_models.rubrique_id', '=', 'rubrique_models.id')
            ->join('genre_journalistique_models', 'depeche_models.genre_id', '=', 'genre_journalistique_models.id')
            ->select('countries_models.pays','countries_models.flag','rubrique_models.rubrique','genre_journalistique_models.genre','depeche_models.*')
            //->whereDate('depeche_models.created_at','>=', Carbon::now()->subWeek())
            ->where('rubrique_models.rubrique', 'SECURITE')
            ->where('depeche_models.status',1)
            ->orderByDesc('depeche_models.id')
            ->limit(4)
            ->get();


            $flash_data = DB::table('depeche_models')
            ->where('depeche_models.status',1)
            //->whereDate('depeche_models.created_at','>=', Carbon::now()->subDay())
            ->select('depeche_models.titre')
            ->orderBy('depeche_models.id', 'desc')
            ->limit(8)
            ->get();

            $archive_data = DB::table('depeche_models')
            ->select(DB::raw('count(id) as `data`'), DB::raw("created_at as new_date"), DB::raw('YEAR(created_at) year, MONTH(created_at) month'))
            ->groupby('year', 'month')
            ->limit(12)
            ->orderByDesc('created_at')
            ->get();


            $banner_728X90 = DB::table('banner_models')->where('libelle',"728X90")->get();
            $banner_1920X309 = DB::table('banner_models')->where('libelle',"1920X309")->get();
            $banner_1200X1500 = DB::table('banner_models')->where('libelle',"1200X1500")->get();

            return [
                'fil_actualite_data' => $fil_actualite_data,
                'event_keyword_data' => $event_keyword_data,
                'recents_depeche_data' => $recents_depeche_data,
                'politique_news_data' => $politique_news_data,
                'economie_news_data' => $economie_news_data,
                'societe_news_data' => $societe_news_data,
                'securite_news_data' => $securite_news_data,
                'flash_data' => $flash_data,
                'banner_728X90' => $banner_728X90,
                'banner_1920X309' => $banner_1920X309,
                'banner_1200X1500' => $banner_1200X1500,
                'archive_data' => $archive_data,

            ];



        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => 'error',
                        'code' => 500,
                    'message' => $th->getMessage(),
                ]
            );
        }
    }




    // get depeche details from API by slug

    public function get_alerteinfo_depeche_details_by_slug($slug)
    {
        try {
            return DB::table('depeche_models')
                ->join('countries_models', 'depeche_models.pays_id', '=', 'countries_models.id')
                ->join('rubrique_models', 'depeche_models.rubrique_id', '=', 'rubrique_models.id')
                ->join('genre_journalistique_models', 'depeche_models.genre_id', '=', 'genre_journalistique_models.id')
                ->select('countries_models.pays','countries_models.flag','rubrique_models.rubrique','genre_journalistique_models.genre','depeche_models.*')
                ->where('depeche_models.slug', $slug)
                ->where('depeche_models.status',1)
                ->first();
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => 'error',
                    'code' => 500,
                    'message' => $th->getMessage(),
                ]
            );
        }
    }


    // Get all archives depeche
    public function get_alerteinfo_depeche_archives()
    {
        try {
            return DB::table('depeche_models')
                ->select(DB::raw('count(id) as `data`'), DB::raw("created_at as new_date"), DB::raw('YEAR(created_at) year, MONTH(created_at) month'))
                ->groupby('year', 'month')
                ->limit(12)
                ->orderByDesc('created_at')
                ->get();

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

    public function get_alerteinfo_depeche_archives_data($mounth,$year)
    {
        try {
            return DB::table('depeche_models')
            ->join('countries_models', 'depeche_models.pays_id', '=', 'countries_models.id')
            ->join('rubrique_models', 'depeche_models.rubrique_id', '=', 'rubrique_models.id')
            ->join('genre_journalistique_models', 'depeche_models.genre_id', '=', 'genre_journalistique_models.id')
            ->select('countries_models.pays','countries_models.flag','rubrique_models.rubrique','genre_journalistique_models.genre','depeche_models.*')
            ->whereMonth('articles_models.created_at', $mounth)
            ->where('depeche_models.status',1)
            ->whereYear('articles_models.created_at', $year)
            ->orderByDesc('articles_models.created_at')
            ->get();

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

    // get full depeches by rubrique

    public function get_alerteinfo_depeche_by_rubrique($rubrique)
    {
        try {
            return DB::table('depeche_models')
            ->join('countries_models', 'depeche_models.pays_id', '=', 'countries_models.id')
            ->join('rubrique_models', 'depeche_models.rubrique_id', '=', 'rubrique_models.id')
            ->join('genre_journalistique_models', 'depeche_models.genre_id', '=', 'genre_journalistique_models.id')
            ->select('countries_models.pays','countries_models.flag','rubrique_models.rubrique','genre_journalistique_models.genre','depeche_models.*')
            ->where('depeche_models.rubrique_id', $rubrique)
            ->orderBy('depeche_models.id', 'desc')
            ->limit(100)
            ->get();
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => 'error',
                    'code' => 500,
                    'message' => $th->getMessage(),
                ]
            );
        }
    }


    // get full depeches by country
    public function get_full_depeche_by_country($country)
    {
        try {
            $country_to_array = implode(',', $country);

            return DB::table('depeche_models')
                ->join('countries_models', 'depeche_models.pays_id', '=', 'countries_models.id')
                ->join('rubrique_models', 'depeche_models.rubrique_id', '=', 'rubrique_models.id')
                ->join('genre_journalistique_models', 'depeche_models.genre_id', '=', 'genre_journalistique_models.id')
                ->select('countries_models.pays','countries_models.flag','rubrique_models.rubrique','genre_journalistique_models.genre','depeche_models.*')
                ->where('depeche_models.pays_id', $country_to_array)
                ->orderBy('depeche_models.id', 'desc')
                ->limit(100)
                ->get();
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => 'error',
                    'code' => 500,
                    'message' => $th->getMessage(),
                ]
            );
        }
    }




    // get full flashes
    public function get_full_flashes()
    {
        // TODO: Implement fetching full flashes from API and returning it
        try {
            return DB::table('flashes_models')
                ->join('countries_models', 'flashes_models.pays_id', '=', 'countries_models.id')
                ->join('rubrique_models', 'flashes_models.rubrique_id', '=', 'rubrique_models.id')
                ->select(
                    'countries_models.pays',
                    'countries_models.flag',
                    'rubrique_models.rubrique',
                    'flashes_models.*'
                )
                ->orderBy('flashes_models.id', 'desc')
                ->limit(100)
                ->get();
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => 'error',
                    'code' => 500,
                    'message' => $th->getMessage(),
                ]
            );
        }
    }


    // get full recents flashes

    public function get_full_recents_flashes()
    {
        try {
            return DB::table('flashes_models')
                ->join('countries_models', 'flashes_models.pays_id', '=', 'countries_models.id')
                ->join('rubrique_models', 'flashes_models.rubrique_id', '=', 'rubrique_models.id')
                ->select(
                    'countries_models.pays',
                    'countries_models.flag',
                    'rubrique_models.rubrique',
                    'flashes_models.*'
                )
                ->where('flashes_models.status', 1)
                ->orderBy('flashes_models.id', 'desc')
                ->limit(10)
                ->get();
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => 'error',
                    'code' => 500,
                    'message' => $th->getMessage(),
                ]
            );
        }
    }


    //  ++++++++++++++++++++++++++++++++++++++ BANNERS +++++++++++++++
    public function get_frontend_728X90()
    {
        try {
            return DB::table('banner_models')->where('libelle', "728X90")
                ->where('status', 1)
                ->first();
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
}
