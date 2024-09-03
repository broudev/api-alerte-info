<?php

namespace App\Http\Controllers\API\V1\Frontend;

use Illuminate\Http\Request;
use App\Models\Like\LikeModels;
use App\Models\Views\ViewsModels;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Dislike\DislikeModels;
use App\Models\Quoideneufs\MediaModels;
use App\Models\Quoideneufs\RubriquesQuoideneufModels;

class FrontendQuoideneufController extends Controller
{
    // GET FRONTEND HOME ARTICLE




    public function get_frontend_une_news_article()
    {
        try {
                return  DB::table('articles_models')
                ->join('rubriques_quoideneuf_models', 'articles_models.rubrique_id', '=', 'rubriques_quoideneuf_models.id')
                ->join('countries_models', 'articles_models.pays_id', '=', 'countries_models.id')
                ->join('genre_journalistique_models', 'articles_models.genre_id', '=', 'genre_journalistique_models.id')
                ->select(
                    'rubriques_quoideneuf_models.rubrique',
                    'countries_models.pays',
                    'countries_models.flag',
                    'genre_journalistique_models.genre',
                    'articles_models.*'
                )
                ->where('articles_models.status',1)
                ->orderBy('id', 'desc')
                ->limit(6)
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

    public function get_frontend_archive_article()
    {
        try {
            return DB::table('articles_models')
                ->select(DB::raw('count(id) as `data`'), DB::raw("created_at as new_date"),  DB::raw('YEAR(created_at) year, MONTH(created_at) month'))
                ->groupby('year','month')
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


    public function get_frontend_politique_article(){
        try {
            return DB::table('articles_models')
            ->join('rubriques_quoideneuf_models', 'articles_models.rubrique_id', '=', 'rubriques_quoideneuf_models.id')
            ->join('genre_journalistique_models', 'articles_models.genre_id', '=', 'genre_journalistique_models.id')
            ->join('countries_models', 'articles_models.pays_id', '=', 'countries_models.id')
            ->select(
                'rubriques_quoideneuf_models.rubrique',
                'countries_models.pays',
                'countries_models.flag',
                'genre_journalistique_models.genre',
                'articles_models.*'
            )
            ->where('rubriques_quoideneuf_models.rubrique', 'POLITIQUE')
            ->where('articles_models.status', 1)
            ->orderBy('articles_models.id','desc')
            ->limit(6)
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

    public function get_frontend_economie_article(){
        try {
            return DB::table('articles_models')
            ->join('rubriques_quoideneuf_models', 'articles_models.rubrique_id', '=', 'rubriques_quoideneuf_models.id')
            ->join('genre_journalistique_models', 'articles_models.genre_id', '=', 'genre_journalistique_models.id')
            ->join('countries_models', 'articles_models.pays_id', '=', 'countries_models.id')
            ->select(
                'rubriques_quoideneuf_models.rubrique',
                'countries_models.pays',
                'countries_models.flag',
                'genre_journalistique_models.genre',
                'articles_models.*'
            )
            ->where('rubriques_quoideneuf_models.rubrique', 'ECONOMIE')
            ->where('articles_models.status', 1)
            ->orderBy('articles_models.id','desc')
            ->limit(4)
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
    public function get_frontend_popular_article(){
        try {
            return DB::table('articles_models')->orderByDesc('counter')->where('status', 1)->limit(8)
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



    public function get_frontend_media_news(){
        try {
            return DB::table('media_models')->orderBy('id', 'desc')->limit(3)->get();
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


    public function detail_article_quoideneuf($slug)
    {


        try {
            if (!isset($slug)) :
                return response()->json(
                    [
                        'status' => 'error',
                        'code' => 404,
                        'message' => "Erreur!, Aucun élément trouvé"
                    ]
                );
            else :

                (int) $old_counter = DB::table('articles_models')->where('slug', $slug)
                    ->value('counter');

                $new_counter =  $old_counter+1;

                //return $new_counter;

                $is_updated = DB::table('articles_models')->where('slug', $slug)
                    ->update(['counter' => $new_counter]);

                if($is_updated){

                    return  DB::table('articles_models')
                        ->join('rubriques_quoideneuf_models', 'articles_models.rubrique_id', '=', 'rubriques_quoideneuf_models.id')
                        ->join('genre_journalistique_models', 'articles_models.genre_id', '=', 'genre_journalistique_models.id')
                        ->join('countries_models', 'articles_models.pays_id', '=', 'countries_models.id')
                        ->select(
                            'rubriques_quoideneuf_models.rubrique',
                            'countries_models.pays',
                            'countries_models.flag',
                            'genre_journalistique_models.genre',
                            'articles_models.*'
                        )
                        ->where('articles_models.slug', $slug)->first();
                }

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

    public function get_frontend_similar_article($rubrique, $slug)
    {

        try {
            $arry = [$slug];

            $similar_article = DB::table('articles_models')
                ->join('rubriques_quoideneuf_models', 'articles_models.rubrique_id', '=', 'rubriques_quoideneuf_models.id')
                ->join('countries_models', 'articles_models.pays_id', '=', 'countries_models.id')
                ->join('genre_journalistique_models', 'articles_models.genre_id', '=', 'genre_journalistique_models.id')
                ->select(
                    'rubriques_quoideneuf_models.rubrique',
                    'countries_models.pays',
                    'countries_models.flag',
                    'genre_journalistique_models.genre',
                    'articles_models.*'
                )
                ->where('articles_models.rubrique_id', $rubrique)
                ->whereNotIn('articles_models.slug', $arry)
                ->where('articles_models.status', 1)
                ->limit(6)
                ->get();

            return $similar_article;
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
    //countries_models

    public static function get_frontend_article_with_rubrique($slug)
    {

        try {
            $article = DB::table('articles_models')
                ->join('rubriques_quoideneuf_models', 'articles_models.rubrique_id', '=', 'rubriques_quoideneuf_models.id')
                ->join('genre_journalistique_models', 'articles_models.genre_id', '=', 'genre_journalistique_models.id')
                ->join('countries_models', 'articles_models.pays_id', '=', 'countries_models.id')
                ->select(
                    'rubriques_quoideneuf_models.rubrique',
                    'countries_models.pays',
                    'countries_models.flag',
                    'genre_journalistique_models.genre',
                    'articles_models.*'
                )
                ->where('rubriques_quoideneuf_models.slug',$slug)
                ->where('articles_models.status', 1)
                ->orderBy('articles_models.id','desc')
                ->limit(50)
                ->get();

            $rubrique = DB::table('rubriques_quoideneuf_models')->where('slug',$slug)->value('rubrique');

            return [
                'article' => $article,
                'categorie' => $rubrique
            ];
        } catch (\Throwable $e) {
            return $e->getMessage();
        }

    }

    public function get_frontend_archive_data($mounth,$year)
    {

        try {
            return DB::table('articles_models')
            ->join('rubriques_quoideneuf_models', 'articles_models.rubrique_id', '=', 'rubriques_quoideneuf_models.id')
            ->join('countries_models', 'articles_models.pays_id', '=', 'countries_models.id')
            ->join('genre_journalistique_models', 'articles_models.genre_id', '=', 'genre_journalistique_models.id')
            ->select(
                'rubriques_quoideneuf_models.rubrique',
                'countries_models.pays',
                'countries_models.flag',
                'genre_journalistique_models.genre',
                'articles_models.*'
            )
            ->whereMonth('articles_models.created_at', $mounth)
            ->whereYear('articles_models.created_at', $year)
            ->orderByDesc('articles_models.created_at')
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

    public function get_frontend_event_keywords()
    {
        try {
            return  DB::table('events_key_words_models')->where('status', 0)->get();
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


    public function get_frontend_news_by_event_keywords($keyword)
    {
        try {
            return  DB::table('articles_models')
            ->join('rubriques_quoideneuf_models', 'articles_models.rubrique_id', '=', 'rubriques_quoideneuf_models.id')
            ->join('genre_journalistique_models', 'articles_models.genre_id', '=', 'genre_journalistique_models.id')
            ->join('countries_models', 'articles_models.pays_id', '=', 'countries_models.id')
            ->select(
                'rubriques_quoideneuf_models.rubrique',
                'countries_models.pays',
                'countries_models.flag',
                'genre_journalistique_models.genre',
                'articles_models.*'
            )
            ->where('articles_models.titre','LIKE', '%'.$keyword.'%')
            ->orWhere('articles_models.lead','LIKE', '%'.$keyword.'%')
            ->orWhere('articles_models.contenus','LIKE', '%'.$keyword.'%')
            ->orderBy('id', 'desc')
            ->limit(150)
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
    public function filter_on_news_with_query($query)
    {
        try {
            return  DB::table('articles_models')
            ->join('rubriques_quoideneuf_models', 'articles_models.rubrique_id', '=', 'rubriques_quoideneuf_models.id')
            ->join('genre_journalistique_models', 'articles_models.genre_id', '=', 'genre_journalistique_models.id')
            ->join('countries_models', 'articles_models.pays_id', '=', 'countries_models.id')
            ->select(
                'rubriques_quoideneuf_models.rubrique',
                'countries_models.pays',
                'countries_models.flag',
                'genre_journalistique_models.genre',
                'articles_models.*'
            )
            ->where('articles_models.titre','LIKE', '%'.$query.'%')
            ->orWhere('articles_models.lead','LIKE', '%'.$query.'%')
            ->orWhere('articles_models.contenus','LIKE', '%'.$query.'%')
            ->orderBy('id', 'desc')
            ->limit(150)
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

    public function like_frontend_news(Request $request)
    {

        try {
            $visitor_ip = $request->ip();

            $news_slug = $request->slug;


            $is_disliked = DB::table('dislike_models')->where('news_slug', $news_slug)
                ->where('ip_address', $visitor_ip)
                ->first();
            if($is_disliked != null):
                return response()->json([
                    'code' => 201,
                    'status' => 'info',
                    'message' => "Vous avez déjà donner votre approbation à cet article."
                ]);
            else:

                $is_liked = DB::table('like_models')->where('news_slug', $news_slug)
                    ->where('ip_address', $visitor_ip)
                    ->first();

                if ($is_liked != null) :
                    DB::table('like_models')->where('news_slug', $news_slug)
                        ->where('ip_address', $visitor_ip)->delete();


                    $this->set_counter($news_slug);

                    return response()->json([
                        'code' => 200,
                        'status' => 'succès',
                        'message' => "Votre approbation a été retirée de l'article."
                    ]);
                else :
                    $new_like = new LikeModels();

                    $new_like->news_slug = $news_slug;
                    $new_like->ip_address = $visitor_ip;

                    if ($new_like->save()) :

                        $this->set_counter($news_slug);
                        return response()->json([
                            'code' => 200,
                            'status' => 'succès',
                            'message' => "Votre approbation a été enregistrée."
                        ]);
                    endif;
                endif;
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
    protected function set_counter($news_slug) {

        $likes_counter = DB::table('like_models')
            ->select(DB::raw('count(*) as like_counter, news_slug'))
            ->where('news_slug', $news_slug)
            ->groupBy('news_slug')
            ->first();

        if($likes_counter == null):
            DB::table('articles_models')->where('slug', $news_slug)
            ->update(['like_counter' => 0]);
        else:
            DB::table('articles_models')->where('slug', $news_slug)
            ->update(['like_counter' => $likes_counter->like_counter]);
        endif;

    }


    public function dislike_frontend_news(Request $request)
    {
        try {
            $visitor_ip = $request->ip();

            $news_slug = $request->slug;

            $is_liked = DB::table('like_models')->where('news_slug', $news_slug)
                    ->where('ip_address', $visitor_ip)
                    ->first();
            if($is_liked != null):
                return response()->json([
                    'code' => 201,
                    'status' => 'info',
                    'message' => "Vous avez déjà donner votre approbation à cet article."
                ]);
            else:
                $is_liked = DB::table('dislike_models')->where('news_slug', $news_slug)
                    ->where('ip_address', $visitor_ip)
                    ->first();

                if ($is_liked != null) :
                    DB::table('dislike_models')->where('news_slug', $news_slug)
                        ->where('ip_address', $visitor_ip)->delete();


                        $this->set_discounter($news_slug);

                    return response()->json([
                        'code' => 200,
                        'status' => 'succès',
                        'message' => "Votre, approbation a été retirée de l'article."
                    ]);
                else :
                    $new_dislike = new DislikeModels();

                    $new_dislike->news_slug = $news_slug;
                    $new_dislike->ip_address = $visitor_ip;

                    if ($new_dislike->save()) :

                        $this->set_counter($news_slug);
                        return response()->json([
                            'code' => 200,
                            'status' => 'succès',
                            'message' => "Merci pour votre approbation sur l'article"
                        ]);
                    endif;
                endif;
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


    protected function set_discounter($news_slug) {

        $dislikes_counter = DB::table('dislike_models')
            ->select(DB::raw('count(*) as dislike_counter, news_slug'))
            ->where('news_slug', $news_slug)->groupBy('news_slug')->first();

            if($dislikes_counter == null):
                DB::table('articles_models')->where('slug', $news_slug)
                ->update(['dislike_counter' => 0]);
            else:
                DB::table('articles_models')->where('slug', $news_slug)
                ->update(['dislike_counter' => $dislikes_counter->dislike_counter]);
            endif;
    }


    public function get_frontend_flash_info()
    {
        try {
            $get_article = DB::table('articles_models')
                ->orderBy('id', 'desc')->where('status', 1)->limit(8)->get();

            return $get_article;
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

    public function get_frontend_news_rubriques()
    {
        $news_rubrique = RubriquesQuoideneufModels::whereIn('rubrique', ['INTERNATIONAL','SPORT','TRIBUNE LIBRE','SOCIETE','CULTURE'])
        ->orderBy('rubrique','asc')
        ->get();

        $other_news_rubrique = RubriquesQuoideneufModels::whereNotIn('rubrique', ['INTERNATIONAL','SPORT','TRIBUNE LIBRE','SOCIETE','CULTURE'])
        ->orderBy('rubrique','asc')
        ->get();


        return [
            'news_rubrique' => $news_rubrique,
            'other_news_rubriques' => $other_news_rubrique
        ];
    }

    public function get_frontend_other_rubrique()
    {
        $rubrique = RubriquesQuoideneufModels::whereNotIn('rubrique', ['POLITIQUE','ECONOMIE','SOCIETE'])
        ->orderBy('rubrique','asc')
        ->get();

        return $rubrique;
    }




    public function get_frontend_728X90()
    {
        try {
            return DB::table('banner_models')->where('libelle',"728X90")
                ->where('status',1)
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


    public function get_frontend_1920X309()
    {
        try {
            return DB::table('banner_models')->where('libelle',"1920X309")
                ->where('status',1)
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

    public function get_frontend_1200X1500()
    {

        try {
            return DB::table('banner_models')->where('libelle',"1200X1500")
                ->where('status',1)
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

    public function get_frontend_media()
    {
        try {
            return MediaModels::all();
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
