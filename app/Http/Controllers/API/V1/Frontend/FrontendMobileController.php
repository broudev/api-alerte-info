<?php

namespace App\Http\Controllers\API\V1\Frontend;

use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\SendAccountMailer;
use App\Services\CodeGenerator;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Models\AbonnesMobileModels\AbonnesMobileModels;

class FrontendMobileController extends Controller
{
    public function get_mobile_recents_depeches_and_flashes(Request $request)
    {
        try {
            if ($request->customer_countries_id == null) {
                $depeche_data = DB::table('depeche_models')
                    ->join('countries_models', 'depeche_models.pays_id', '=', 'countries_models.id')
                    ->join('rubrique_models', 'depeche_models.rubrique_id', '=', 'rubrique_models.id')
                    ->select('countries_models.pays', 'countries_models.flag', 'rubrique_models.rubrique', 'depeche_models.*')
                    ->orderBy('depeche_models.id', 'desc')
                    ->limit(15)
                    ->get();

                $flash_data = DB::table('flashes_models')
                    ->join('countries_models', 'flashes_models.pays_id', '=', 'countries_models.id')
                    ->join('rubrique_models', 'flashes_models.rubrique_id', '=', 'rubrique_models.id')
                    ->select(
                        'countries_models.pays',
                        'countries_models.flag',
                        'rubrique_models.rubrique',
                        'flashes_models.*'
                    )
                    ->orderBy('flashes_models.id', 'desc')
                    ->limit(10)
                    ->get();
                return [
                    'depeche_data' => $depeche_data,
                    'flashes_data' => $flash_data,
                ];
            } else {
                $country_to_array = explode(',', $request->customer_countries_id);

                $depeche_data = DB::table('depeche_models')
                    ->join('countries_models', 'depeche_models.pays_id', '=', 'countries_models.id')
                    ->join('rubrique_models', 'depeche_models.rubrique_id', '=', 'rubrique_models.id')
                    ->select('countries_models.pays', 'countries_models.flag', 'rubrique_models.rubrique', 'depeche_models.*')
                    ->whereIn('depeche_models.pays_id', $country_to_array)
                    ->orderBy('depeche_models.id', 'desc')
                    ->limit(15)
                    ->get();

                $flash_data = DB::table('flashes_models')
                    ->join('countries_models', 'flashes_models.pays_id', '=', 'countries_models.id')
                    ->join('rubrique_models', 'flashes_models.rubrique_id', '=', 'rubrique_models.id')
                    ->select(
                        'countries_models.pays',
                        'countries_models.flag',
                        'rubrique_models.rubrique',
                        'flashes_models.*'
                    )
                    ->whereIn('flashes_models.pays_id', $country_to_array)
                    ->orderBy('flashes_models.id', 'desc')
                    ->limit(10)
                    ->get();
            }
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


    // get full depeches join countries models and rubriques models and genre journalistiques models
    public function get_mobile_depeches()
    {
        // TODO: Implement fetching full dépêche from API and returning it

        try {
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


    // get depeche details from API by slug

    public function get_depeche_details_by_slug($slug)
    {
        try {
            return DB::table('depeche_models')
                ->join('countries_models', 'depeche_models.pays_id', '=', 'countries_models.id')
                ->join('rubrique_models', 'depeche_models.rubrique_id', '=', 'rubrique_models.id')
                ->select('countries_models.pays', 'countries_models.flag', 'rubrique_models.rubrique', 'depeche_models.*')
                ->where('depeche_models.slug', $slug)
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



    // get full depeches by country
    public function get_full_depeche_by_country($country)
    {
        try {
            $country_to_array = implode(',', $country);

            return DB::table('depeche_models')
                ->join('countries_models', 'depeche_models.pays_id', '=', 'countries_models.id')
                ->join('rubrique_models', 'depeche_models.rubrique_id', '=', 'rubrique_models.id')
                ->select('countries_models.pays', 'countries_models.flag', 'rubrique_models.rubrique', 'depeche_models.*')
                ->whereIn('depeche_models.pays_id', $country_to_array)
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

    public function get_full_depeche_archives()
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

    public function get_full_depeche_archives_data()
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



    // get rubriques list
    public function get_mobile_rubriques()
    {
        try {
            return DB::table('rubrique_models')
                ->select('id', 'rubrique')
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



    // get genres list
    public function get_genres_list()
    {
        try {
            return DB::table('genre_journalistique_models')
                ->select('id', 'genre')
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



    // get pays list



    // store mobile abonne data to database

    public function store_mobile_abonne_data(Request $request)
    {
        try {
            if(empty($request->nom)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "Erreur! Le nom est obligatoire"
                    ]
                );
            endif;
            if(empty($request->prenom)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "Erreur! Le prénom est obligatoire"
                    ]
                );
            endif;

            if(empty($request->contact)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "Erreur! Le numéro de téléphone est obligatoire"
                    ]
                );
            endif;

            if(empty($request->email)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "Erreur! L'adresse email est obligatoire"
                    ]
                );
            endif;

            // check if email is valid
            if(!filter_var($request->email, FILTER_VALIDATE_EMAIL)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "Erreur! L'adresse email n'est pas valide"
                    ]
                );
            endif;

            // check if email already exists in database
            $checkUser = DB::table('users')->where('email',$request->email)->first();

            if($checkUser != null):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "Erreur! L'adresse email est déjà utilisée pour un autre abonné"
                    ]
                );
            endif;

            // check if password is conform to password confirmation
            if($request->password != $request->password_confirmation):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "Erreur! Les mots de passe ne correspondent pas"
                    ]
                );
            endif;

            // generate a random password
            //$my_password = Str::random(10);

            // hash the password
            $hashed_password = password_hash($request->password, PASSWORD_BCRYPT);





            $store_user = new User();
            $store_user->email = $request->email;
            $store_user->phone = $request->phone;
            $store_user->user_device = $request->device_info;
            $store_user->user_type = "abonne";
            $store_user->password = $hashed_password;

            if($store_user->save()):

                $store_abonne = new AbonnesMobileModels();

                $store_abonne->abonne_fname = $request->nom;
                $store_abonne->abonne_lname = $request->prenom;
                $store_abonne->user_id = $store_user->id;
                $store_abonne->type_abonne = "premium";
                $store_abonne->abonne_phone_number = $request->contact;
                $store_abonne->abonne_email = $request->email;

                $store_abonne->slug = CodeGenerator::generateRfk();

                if($store_abonne->save()):

                    $credentials = request(['email', 'password']);

                    if (! $token = auth()->attempt($credentials)) {
                        return response()->json(
                            [
                                'code' => 302,
                                'status' => 'erreur',
                                'message' => "Oups! accès interdit !👺, Email ou mot de passe introuvable"
                            ]
                        );
                    }

                    $subject_email = "ALERTE INFO: NOTIFICATION APRES CREATION DE COMPTE";
                    $default_text = "L'agence de presse vous remercie pour votre abonnement. Voici vos identifiants pour avoir accès votre espace.";

                    Mail::to($request->abonne_email)->send(new SendAccountMailer(
                        $request->password,
                        $store_abonne->abonne_fname . ' ' . $store_abonne->abonne_lname,
                        $store_abonne->abonne_email,
                        $subject_email,
                        $default_text
                    ));


                    return response()->json(
                        [
                            'status' => 'succès',
                            'code' => 200,
                            'account_data' => [
                                'nom' => $store_abonne->abonne_fname,
                                'prenom' => $store_abonne->abonne_lname,
                                'email' => $store_abonne->abonne_email,
                                'contact' => $store_abonne->abonne_phone_number,
                                'account_id' => $store_abonne->id,
                                'token' => $token,
                                'token_type' => 'Bearer',
                                'expires_in' => auth()->factory()->getTTL(),

                            ],
                            'message' => "Ok ! Le compte a été créé avec succès. Vous allez recevoir ses accès par mail."
                        ]
                    );
                else:

                    User::where('id', $store_user->id)->delete();

                    return response()->json(
                        [
                            'status' => 'error',
                            'code' => 300,
                            'message' => "Erreur ! Échec de la création du compte, veuillez réessayer!"
                        ]
                    );
                endif;

            else:
                User::where('id', $store_user->id)->delete();
                return response()->json(
                    [
                        'status' => 'error',
                        'code' => 300,
                        'message' => "Erreur ! Échec de la création du compte , veuillez réessayer!"
                    ]
                );
            endif;


        } catch (\Throwable $e) {
            return response()->json(
                [
                    'status' => 'error',
                    'code' => 500,
                    'message' => $e->getMessage(),
                ]
            );
        }
    }

}
