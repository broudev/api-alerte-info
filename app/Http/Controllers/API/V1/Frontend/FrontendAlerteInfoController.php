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
            ->limit(20)
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
            ->limit(9)
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

            $banner_728X90 = DB::table('banner_models')->where('libelle',"728X90")->where('status', 1)->orderByDesc('id')->get();
            $banner_1920X309 = DB::table('banner_models')->where('libelle',"1920X309")->where('status', 1)->orderByDesc('id')->first();
            $banner_1200X1500 = DB::table('banner_models')->where('libelle',"1200X1500")->where('status', 1)->orderByDesc('id')->first();



            $archive_data = DB::table('depeche_models')
            ->select(DB::raw('count(id) as `data`'), DB::raw("created_at as new_date"), DB::raw('YEAR(created_at) year, MONTH(created_at) month'))
            ->groupby('year', 'month')
            ->limit(12)
            ->orderByDesc('created_at')
            ->get();

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

    

    // get depeche detail

    public function get_alerteinfo_depeche_details_by_slug($slug)
    {
        try {

            (int) $old_counter = DB::table('depeche_models')->where('slug', $slug)
                    ->value('counter');

            $new_counter =  $old_counter+1;

                //return $new_counter;

            DB::table('depeche_models')->where('slug', $slug)->update(['counter' => $new_counter]);


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

    public function get_alerteinfo_depeche_archives_data_by_mounth_and_year($mounth,$year)
    {
        try {
            return DB::table('depeche_models')
            ->join('countries_models', 'depeche_models.pays_id', '=', 'countries_models.id')
            ->join('rubrique_models', 'depeche_models.rubrique_id', '=', 'rubrique_models.id')
            ->join('genre_journalistique_models', 'depeche_models.genre_id', '=', 'genre_journalistique_models.id')
            ->select('countries_models.pays','countries_models.flag','rubrique_models.rubrique','genre_journalistique_models.genre','depeche_models.*')
            ->whereMonth('depeche_models.created_at', $mounth)
            ->where('depeche_models.status',1)
            ->whereYear('depeche_models.created_at', $year)
            ->orderByDesc('depeche_models.created_at')
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


}
