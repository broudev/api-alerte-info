<?php

namespace App\Http\Controllers\API\V1\Frontend;

use DateTime;
use Exception;
use DateInterval;
use Notification;
use App\Models\User;
use App\Mail\Notifications;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Mail\SendAccountMailer;
use App\Services\CodeGenerator;
use Illuminate\Support\Facades\DB;
use App\Services\CinetPay\Marchand;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Redactions\DepecheModels;
use Illuminate\Support\Facades\Validator;
use App\Models\Redactions\CountriesModels;
use App\Models\AbonnesMobileModels\AbonnesMobileModels;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\AbonnementsMobileModels\AbonnementsMobileModels;
use App\Models\AbonnementsMobileModels\ForfaitsAbonnementsMobileModels;

class FrontendMobileController extends Controller
{
    public function get_mobile_recents_depeches_and_flashes(Request $request)
    {
        try {
            if ($request->customer_countries_id == 'undifined') {
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

    public function get_mobile_depeche_details_by_slug($slug)
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
    public function get_mobile_flashes()
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







    // store mobile abonne data to database

    // public function store_mobile_abonne_data(Request $request)
    // {
    //     try {
    //         if (empty($request->nom)):
    //             return response()->json(
    //                 [
    //                     'code' => 302,
    //                     'status' => 'erreur',
    //                     'message' => "Erreur! Le nom est obligatoire"
    //                 ]
    //             );
    //         endif;
    //         if (empty($request->prenom)):
    //             return response()->json(
    //                 [
    //                     'code' => 302,
    //                     'status' => 'erreur',
    //                     'message' => "Erreur! Le prénom est obligatoire"
    //                 ]
    //             );
    //         endif;

    //         if (empty($request->contact)):
    //             return response()->json(
    //                 [
    //                     'code' => 302,
    //                     'status' => 'erreur',
    //                     'message' => "Erreur! Le numéro de téléphone est obligatoire"
    //                 ]
    //             );
    //         endif;

    //         if (empty($request->email)):
    //             return response()->json(
    //                 [
    //                     'code' => 302,
    //                     'status' => 'erreur',
    //                     'message' => "Erreur! L'adresse email est obligatoire"
    //                 ]
    //             );
    //         endif;

    //         // check if email is valid
    //         if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)):
    //             return response()->json(
    //                 [
    //                     'code' => 302,
    //                     'status' => 'erreur',
    //                     'message' => "Erreur! L'adresse email n'est pas valide"
    //                 ]
    //             );
    //         endif;

    //         // check if email already exists in database
    //         $checkUser = DB::table('users')->where('email', $request->email)->first();

    //         if ($checkUser != null):
    //             return response()->json(
    //                 [
    //                     'code' => 302,
    //                     'status' => 'erreur',
    //                     'message' => "Erreur! L'adresse email est déjà utilisée pour un autre abonné"
    //                 ]
    //             );
    //         endif;

    //         // check if password is conform to password confirmation
    //         if ($request->password != $request->password_confirmation):
    //             return response()->json(
    //                 [
    //                     'code' => 302,
    //                     'status' => 'erreur',
    //                     'message' => "Erreur! Les mots de passe ne correspondent pas"
    //                 ]
    //             );
    //         endif;

    //         // generate a random password
    //         //$my_password = Str::random(10);

    //         // hash the password
    //         $hashed_password = password_hash($request->password, PASSWORD_BCRYPT);





    //         $store_user = new User();
    //         $store_user->email = $request->email;
    //         $store_user->phone = $request->contact;
    //         $store_user->user_device = $request->device_info;
    //         $store_user->user_type = "abonne";
    //         $store_user->password = $hashed_password;

    //         if ($store_user->save()):

    //             $store_abonne = new AbonnesMobileModels();

    //             $store_abonne->abonne_fname = $request->nom;
    //             $store_abonne->abonne_lname = $request->prenom;
    //             $store_abonne->user_id = $store_user->id;
    //             $store_abonne->type_abonne = "premium";
    //             $store_abonne->abonne_phone_number = $request->contact;
    //             $store_abonne->abonne_email = $request->email;

    //             $store_abonne->slug = CodeGenerator::generateRfk();

    //             if ($store_abonne->save()):

    //                 $credentials = request(['email', 'password']);

    //                 if (!$token = auth()->attempt($credentials)) {
    //                     return response()->json(
    //                         [
    //                             'code' => 302,
    //                             'status' => 'erreur',
    //                             'message' => "Oups! accès interdit !👺, Email ou mot de passe introuvable"
    //                         ]
    //                     );
    //                 }


    //                 $subject_email = "ALERTE INFO: NOTIFICATION APRES CREATION DE COMPTE";
    //                 $default_text = "L'agence de presse vous remercie pour la création de votre compte. Voici vos identifiants pour avoir accès votre espace.";

    //                 Mail::to($request->email)->send(new SendAccountMailer(
    //                     $request->password,
    //                     $store_abonne->abonne_fname . ' ' . $store_abonne->abonne_lname,
    //                     $store_abonne->abonne_email,
    //                     $subject_email,
    //                     $default_text
    //                 ));



    //                 return response()->json(
    //                     [
    //                         'status' => 'succès',
    //                         'code' => 200,
    //                         'account_data' => [
    //                             'nom' => $store_abonne->abonne_fname,
    //                             'prenom' => $store_abonne->abonne_lname,
    //                             'email' => $store_abonne->abonne_email,
    //                             'contact' => $store_abonne->abonne_phone_number,
    //                             'account_id' => $store_abonne->id,
    //                             'token' => $token,
    //                             'token_type' => 'Bearer',
    //                             'expires_in' => auth()->factory()->getTTL(),

    //                         ],
    //                         'message' => "Ok ! Le compte a été créé avec succès. Vous allez recevoir ses accès par mail."
    //                     ]
    //                 );
    //             else:

    //                 User::where('id', $store_user->id)->delete();

    //                 return response()->json(
    //                     [
    //                         'status' => 'error',
    //                         'code' => 300,
    //                         'message' => "Erreur ! Échec de la création du compte, veuillez réessayer!"
    //                     ]
    //                 );
    //             endif;

    //         else:
    //             User::where('id', $store_user->id)->delete();
    //             return response()->json(
    //                 [
    //                     'status' => 'error',
    //                     'code' => 300,
    //                     'message' => "Erreur ! Échec de la création du compte , veuillez réessayer!"
    //                 ]
    //             );
    //         endif;


    //     } catch (\Throwable $e) {
    //         return response()->json(
    //             [
    //                 'status' => 'error',
    //                 'code' => 300,
    //                 'message' => $e->getMessage(),
    //             ]
    //         );
    //     }
    // }


    //pour son inscription
    public function store_mobile_abonne_data(Request $request)
    {
        try {
            // Validation des données de la requête
            $validator = Validator::make($request->all(), [
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'contact' => 'required|string|max:20|unique:users,phone',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'device_info' => 'required|string|max:255'
            ]);

            // Gestion des erreurs de validation
            if ($validator->fails()) {
                return response()->json([
                    'code' => 422,
                    'status' => 'erreur',
                    'message' => "Erreur! Données invalides",
                    'errors' => $validator->errors()
                ], 422);
            }

            // Utilisation de transactions pour assurer la cohérence des données
            DB::beginTransaction();

            // Hachage du mot de passe
            $hashed_password = password_hash($request->password, PASSWORD_BCRYPT);

            // Création de l'utilisateur
            $store_user = new User();
            $store_user->email = $request->email;
            $store_user->phone = $request->contact;
            $store_user->user_device = $request->device_info;
            $store_user->user_type = "abonne";
            $store_user->password = $hashed_password;

            if (!$store_user->save()) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'code' => 500,
                    'message' => "Erreur! Échec de la création de l'utilisateur."
                ], 500);
            }

            // Création de l'abonné
            $store_abonne = new AbonnesMobileModels();
            $store_abonne->abonne_fname = $request->nom;
            $store_abonne->abonne_lname = $request->prenom;
            $store_abonne->user_id = $store_user->id;
            $store_abonne->type_abonne = "premium";
            $store_abonne->abonne_phone_number = $request->contact;
            $store_abonne->abonne_email = $request->email;
            $store_abonne->slug = CodeGenerator::generateRfk();

            if (!$store_abonne->save()) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'code' => 500,
                    'message' => "Erreur! Échec de la création de l'abonné."
                ], 500);
            }

            // Commit de la transaction si tout est bon
            DB::commit();

            // Tentative d'authentification
            $credentials = request(['email', 'password']);
            if (!$token = auth()->attempt($credentials)) {
                return response()->json([
                    'code' => 401,
                    'status' => 'erreur',
                    'message' => "Oups! Accès interdit !👺, Email ou mot de passe introuvable"
                ], 401);
            }

            // Envoi de l'email de confirmation
            Mail::to($request->email)->send(new SendAccountMailer(
                $request->password,
                $store_abonne->abonne_fname . ' ' . $store_abonne->abonne_lname,
                $store_abonne->abonne_email,
                "ALERTE INFO: NOTIFICATION APRES CREATION DE COMPTE",
                "L'agence de presse vous remercie pour la création de votre compte. Voici vos identifiants pour avoir accès à votre espace."
            ));

            // Réponse en cas de succès
            return response()->json([
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
            ]);

        } catch (\Throwable $e) {
            // Rollback de la transaction en cas d'erreur
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function store_abonne_abonnements(Request $request)
    {
        try {

            // Récupérer l'utilisateur authentifié
            $user = Auth::user();

            if(!$user) {
                      return response()->json([
                    'code' => 302,
                    'status' => 'erreur',
                    'message' => "Veuillez vous authentifié"
                ]);
            }

            $abonne = AbonnesMobileModels::where('user_id', $user->id)->first();

            $abonnement = AbonnementsMobileModels::where('abonne_id', $abonne->id)
            ->first();

            // if (empty($abonnement->id)) {
            //     return response()->json([
            //         'code' => 302,
            //         'status' => 'erreur',
            //         'message' => "Erreur! L'identifiant de l'abonné est obligatoire"
            //     ]);
            // }

            if (empty($request->forfait_id)) {
                return response()->json([
                    'code' => 302,
                    'status' => 'erreur',
                    'message' => "Erreur! Le forfait de l'abonné est obligatoire"
                ]);
            }

            if (empty($request->country_id) || sizeof($request->country_id) == 0) {
                return response()->json([
                    'code' => 302,
                    'status' => 'erreur',
                    'message' => "Erreur! Le pays de l'abonné est obligatoire"
                ]);
            }

            $forfait_info = DB::table('forfaits_abonnements_mobile_models')->where('id', $request->forfait_id)->first();

            if (!$forfait_info) {
                return response()->json([
                    'code' => 404,
                    'status' => 'erreur',
                    'message' => "Erreur! Forfait non trouvé"
                ]);
            }

            $sizeOfCountry = sizeof($request->country_id);

            $dateline = 'P' . $forfait_info->duree_forfait . 'D';

            $date_fin = new DateTime();
            $date_fin->add(new DateInterval($dateline));
            $date_fin->format('Y-m-d H:i:s');
            $date_fin_formatted = $date_fin->format('Y-m-d H:i:s');

            $add_abonnement = new AbonnementsMobileModels();
            $add_abonnement->abonnement_code = CodeGenerator::generateAbonnementCodeUnique();
            $add_abonnement->abonne_id = $abonne->id;
            $add_abonnement->abonne_forfait_id = $request->forfait_id;
            $add_abonnement->abonne_country_id = implode(',', $request->country_id);
            $add_abonnement->montant_abonnements = (int) $sizeOfCountry * $forfait_info->montant_forfait;
            $add_abonnement->date_debut = date('Y-m-d H:i:s', strtotime((now())));
            $add_abonnement->date_fin = $date_fin;
            $add_abonnement->date_fin = $date_fin_formatted;
            $add_abonnement->slug = CodeGenerator::generateSlugCode();

            if ($add_abonnement->save()) {
                DB::table('abonnes_mobile_models')->where('id', $abonnement->id)->update(['status_abonnement' => 1]);

                $abonne_info = AbonnesMobileModels::where('id', $abonnement->id)->first();
                $abonne_pays_id = explode(',', $add_abonnement->abonne_country_id);
                $abonne_conutry = CountriesModels::whereIn('id', $abonne_pays_id)->pluck('pays')->toArray();

                $subject_email = "ALERTE INFO: NOTIFICATION APRES ABONNEMENT";

                $notifications = "L'agence de presse vous remercie pour votre demande d'abonnément. Voici les informations de votre abonnement: "
                    . " <br > <br >"
                    . " <strong>Nom et prénom de l'abonné : </strong> " . " " . $abonne_info->abonne_fname . " " . $abonne_info->abonne_lname
                    . " <br > <br >"
                    . " <strong>Montant de l'abonnement : </strong> " . " " . $add_abonnement->montant_abonnements
                    . " <br > <br >"
                    . " <strong>Les pays de l'abonnement : </strong> " . " " . implode(",", $abonne_conutry)
                    . " <br > <br >"
                    . " <strong>Date fine d'échéance de l'abonnement :</strong>" . " " . $date_fin_formatted;

                // try {
                //     Mail::to($abonne_info->abonne_email)->send(new Notifications($notifications, $subject_email));
                // } catch (\Exception $e) {
                //     return response()->json([
                //         'status' => 'error',
                //         'code' => 500,
                //         'message' => 'Erreur lors de l\'envoi de l\'email: ' . $e->getMessage()
                //     ]);
                // }

                return response()->json([
                    'status' => 'success',
                    'code' => 200,
                    'abonnement_data' => [
                        'abonnement_code' => $add_abonnement->abonnement_code,
                        'date_fin' => $add_abonnement->date_fin
                    ]
                ]);
            }

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage()
            ]);
        }
    }



    // Récupère les dépêches par rubrique
    public function get_mobile_depeche_by_rubrique($rubrique_id)
    {
        try {
            // Récupérer les dépêches avec les détails du pays et de la rubrique
            $depeches = DepecheModels::join('countries_models', 'depeche_models.pays_id', '=', 'countries_models.id')
                ->join('rubrique_models', 'depeche_models.rubrique_id', '=', 'rubrique_models.id')
                ->select(
                    'countries_models.pays',
                    'countries_models.flag',
                    'rubrique_models.rubrique',
                    'depeche_models.id',
                    'depeche_models.author',
                    'depeche_models.rubrique_id',
                    'depeche_models.genre_id',
                    'depeche_models.pays_id',
                    'depeche_models.titre',
                    'depeche_models.lead',
                    'depeche_models.legende',
                    'depeche_models.media_url',
                    'depeche_models.contenus',
                    'depeche_models.counter',
                    'depeche_models.status',
                    'depeche_models.slug',
                    'depeche_models.created_at',
                    'depeche_models.updated_at'
                )
                ->where('depeche_models.rubrique_id', $rubrique_id)
                ->where('depeche_models.status', 1)
                ->orderBy('depeche_models.created_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $depeches
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Rubrique non trouvée.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Une erreur est survenue lors de la récupération des dépêches.'
            ], 500);
        }
    }


    // Récupère les dépêches par pays du client
    public function get_mobile_depeche_by_country($country_id)
    {
        try {
            $depeches = DepecheModels::where('country_id', $country_id)
                ->where('status', 1)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $depeches
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pays non trouvé.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Une erreur est survenue lors de la récupération des dépêches.'
            ], 500);
        }
    }

    // Récupère les archives des dépêches
    public function get_mobile_depeche_archives()
    {
        try {
            $archives = DepecheModels::where('status', 1)
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy(function ($date) {
                    return \Carbon\Carbon::parse($date->created_at)->format('Y-m');
                });

            return response()->json([
                'status' => 'success',
                'data' => $archives
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Une erreur est survenue lors de la récupération des archives.'
            ], 500);
        }
    }

    // Récupère les données d'archives des dépêches pour un mois et une année spécifiques
    public function get_mobile_depeche_archives_data($month, $year)
    {
        try {
            $depeches = DepecheModels::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->where('status', 1)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $depeches
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Aucune dépêche trouvée pour cette période.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Une erreur est survenue lors de la récupération des dépêches.'
            ], 500);
        }
    }

    public function get_mobile_countries_list()
    {
        try {
            $ids = [1, 2, 3];
            $countries = CountriesModels::whereIn('id', $ids)->get();

            return response()->json([
                'status' => 'success',
                'data' => $countries
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Une erreur est survenue lors de la récupération des pays.'
            ], 500);
        }
    }

    public function forfaitsList()
    {
        try {
            return ForfaitsAbonnementsMobileModels::all();
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

    public function notify(Request $request)
    {
        if (isset($request->cpm_trans_id)) {

            try {


                $cinetpay_check = [
                    "apikey" => Marchand::get_apikey(),
                    "site_id" => $request->cpm_site_id,
                    "transaction_id" => $request->cpm_trans_id
                ];
                $notify_data = $this->check_payment_status($cinetpay_check);
                $data_decode = json_decode($notify_data, true);

                $object_data = (object) $data_decode;

                //$code = 627 // EHEC
                //$code = 00 // SUCCESS
                if ($object_data->code == '00') {

                    $add_transaction = new Transaction();
                    $add_transaction->transaction_id = $request->cpm_trans_id;
                    $add_transaction->montant = $object_data->data['amount'];
                    $add_transaction->operations = $object_data->data['description'];
                    $add_transaction->method_payment = $object_data->data['payment_method'];
                    $add_transaction->date_transaction = date('Y-m-d H:i:s', strtotime($object_data->data['payment_date']));
                    $add_transaction->status = $object_data->data['status'];
                    $add_transaction->save();

                    // check if the transaction is for coupons article

                    $is_abonnements = DB::table('abonnements_mobile_models')->where('transaction_code', $request->cpm_trans_id)->first();

                    if ($is_abonnements != null) {
                        DB::table('abonnements_mobile_models')->where('abonnement_code', $request->cpm_trans_id)->update([
                            'updated_at' => date('Y-m-d H:i:s', strtotime(now())),
                            'payments' => 1,
                        ]);
                    }
                    $solde = DB::table('solde_models')->first();


                    if ($solde->solde == 0) {
                        DB::table('solde_models')->update([
                            'solde_models' => (int) $object_data->data['amount'],
                            'slug' => CodeGenerator::generateSlugCode()
                        ]);
                    }else {
                        DB::table('solde_models')->update([
                            'solde' => (int) $solde->montants + (int) $object_data->data['amount'],
                            'slug' => CodeGenerator::generateSlugCode()
                        ]);
                    }

                }

                if ($object_data->code == '627') {

                    $add_transaction = new Transaction();
                    $add_transaction->transaction_id = $request->cpm_trans_id;
                    $add_transaction->montant = $object_data->data['amount'];
                    $add_transaction->operations = $object_data->data['description'];
                    $add_transaction->method_payment = $object_data->data['payment_method'];
                    $add_transaction->date_transaction = date('Y-m-d H:i:s', strtotime($object_data->data['payment_date']));
                    $add_transaction->status = $object_data->data['status'];
                    $add_transaction->save();

                }

            } catch (Exception $e) {
                echo "Erreur :" . $e->getMessage();
            }
        } else {
            // direct acces on IPN
            echo "cpm_trans_id non fourni";
        }
    }


    public function getAbonnementDetails($abonnement_code)
    {
        try {
            // Récupérer l'abonnement en utilisant le code d'abonnement et charger les relations
            $abonnement = AbonnementsMobileModels::where('abonnement_code', $abonnement_code)
                ->with(['forfait', 'abonne'])  // Charger les relations forfait et abonné
                ->first();

            if (!$abonnement) {
                return response()->json([
                    'status' => 'erreur',
                    'code' => 404,
                    'message' => 'Abonnement non trouvé'
                ], 404);
            }

            // Récupérer les IDs des pays liés à l'abonnement
            $pays_ids = explode(',', $abonnement->abonne_country_id);

            // Récupérer les noms des pays à partir des IDs
            $pays = CountriesModels::whereIn('id', $pays_ids)->get();

            // Calculer si l'abonnement est toujours valide
            $isValid = now()->between($abonnement->date_debut, $abonnement->date_fin);

            // Retourner les informations de l'abonnement
            return response()->json([
                'status' => 'succès',
                'code' => 200,
                'abonnement' => [
                    'abonnement_code' => $abonnement->abonnement_code,
                    'date_debut' => $abonnement->date_debut,
                    'date_fin' => $abonnement->date_fin,
                    'montant_abonnements' => $abonnement->montant_abonnements,
                    'forfait' => $abonnement->forfait,  // Détails du forfait
                    'countries' => $pays,  // Liste des pays
                    'countriesId' => $pays_ids,  // Liste des IDs des pays
                    'isValid' => $isValid,  // Statut de validité de l'abonnement
                    'abonne' => $abonnement->abonne  // Détails de l'abonné
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'erreur',
                'code' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getLatestAbonnementDetails()
    {
        try {
            // Récupérer l'utilisateur authentifié
            $user = Auth::user();

            $abonne = AbonnesMobileModels::where('user_id', $user->id)->first();


            // Chercher d'abord un abonnement valide
            $abonnement = AbonnementsMobileModels::where('abonne_id', $abonne->id)
                ->where('date_fin', '>=', now())  // Filtrer les abonnements valides
                ->with(['forfait', 'abonne'])  // Charger les relations forfait et abonné
                ->orderBy('date_fin', 'desc')  // Trier par date de fin (le plus proche en premier)
                ->first();

            // Si aucun abonnement valide n'est trouvé, récupérer le dernier abonnement (non valide)
            if (!$abonnement) {
                $abonnement = AbonnementsMobileModels::where('abonne_id', $abonne->id)
                    ->with(['forfait', 'abonne'])  // Charger les relations forfait et abonné
                    ->orderBy('created_at', 'desc')  // Trier par date de création (dernier en premier)
                    ->first();
            }

            if (!$abonnement) {
                return response()->json([
                    'status' => 'erreur',
                    'code' => 404,
                    'message' => 'Aucun abonnement trouvé pour cet utilisateur'
                ], 404);
            }

            // Récupérer les IDs des pays liés à l'abonnement
            $pays_ids = explode(',', $abonnement->abonne_country_id);

            // Récupérer les noms des pays à partir des IDs
            $pays = CountriesModels::whereIn('id', $pays_ids)->get();

            // Calculer si l'abonnement est toujours valide
            $isValid = now()->between($abonnement->date_debut, $abonnement->date_fin);

            // Retourner les informations de l'abonnement
            return response()->json([
                'status' => 'succès',
                'code' => 200,
                'abonnement' => [
                    'abonnement_code' => $abonnement->abonnement_code,
                    'date_debut' => $abonnement->date_debut,
                    'date_fin' => $abonnement->date_fin,
                    'montant_abonnements' => $abonnement->montant_abonnements,
                    'forfait' => $abonnement->forfait,  // Détails du forfait
                    'countries' => $pays,  // Liste des pays
                    'countriesId' => $pays_ids,  // Liste des IDs des pays
                    'isValid' => $isValid,  // Statut de validité de l'abonnement
                    'abonne' => $abonnement->abonne  // Détails de l'abonné
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'erreur',
                'code' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }


}
