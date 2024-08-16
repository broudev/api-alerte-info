<?php

namespace App\Http\Controllers\API\V1\Auth;

use Illuminate\Http\Request;
use App\Services\FlashMessages;
use App\Mail\ForgetPasswordMailer;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class AuthentificatorController extends Controller
{

    public function un_authorised()
    {
        return response()->json(
            [
                'code' => 401, // code for authorization error
                'status' => 'erreur',
                'message' => "Oups! accès interdit !👺. Le token n'est plus valable ou une connexion est nécessaire"
            ]
        );
    }




    public function admin_authentificator(Request $request)
    {
        try {


            if(empty($request->email)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "L'adresse email est obligatoire"
                    ]
                );
            endif;

            if(!filter_var($request->email, FILTER_VALIDATE_EMAIL)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "L'adresse e-mail n'est pas valide"
                    ]
                );
            endif;


            if(empty($request->password)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "Le mot de passe est obligatoire"
                    ]
                );
            endif;


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

            $users = DB::table('users')->where('email', $request->email)->first();

            if (! $users || ! password_verify($request->password, $users->password))
            {
                return response()->json(
                    [
                        'code' => 300,
                        'status' => 'erreur',
                        'message' => "Le mot de passe est incorrecte"
                    ]
                );
            }


            $users_is_logged = DB::table('administration_models')
            ->join('users', 'administration_models.user_id', '=', 'users.id')
            ->join('roles', 'administration_models.role_id', '=', 'roles.id')
            ->select('users.email', 'users.connected','users.user_type','roles.role', 'administration_models.*')
            ->where('administration_models.user_id', $users->id)->first();


            if($users_is_logged):
                DB::table('users')->where('id', $users->id)->update(['connected' => 1]);

                return response()->json(
                    [
                        'code' => 200,
                        'token' => $token,
                        'users' => $users_is_logged,
                        'status' => "succès",
                        'token_type' => 'Bearer',
                        'expires_in' => auth()->factory()->getTTL(),
                        'message' => 'Vous êtes connecté 💚!'
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



    public function authentificated_abonne(Request $request)
    {
        try {

            if(empty($request->email)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "L'adresse email est obligatoire"
                    ]
                );
            endif;

            if(!filter_var($request->email, FILTER_VALIDATE_EMAIL)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "L'adresse e-mail n'est pas valide"
                    ]
                );
            endif;


            if(empty($request->password)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "Le mot de passe est obligatoire"
                    ]
                );
            endif;


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


            $users = DB::table('users')->where('email', $request->email)->first();

            if (! $users || ! password_verify($request->password, $users->password))
            {
                return response()->json(
                    [
                        'code' => 300,
                        'status' => 'erreur',
                        'message' => "Le mot de passe est incorrecte"
                    ]
                );
            }


            // get Abonné logged data
            $users_is_logged = DB::table('abonnes_mobile_models')
            ->join('users', 'abonnes_mobile_models.user_id', '=', 'users.id')
            ->select(
                'users.email',
                'users.connected',
                'users.user_type',
                'abonnes_mobile_models.abonne_fname',
                'abonnes_mobile_models.abonne_lname',
                'abonnes_mobile_models.abonne_phone_number',
                'abonnes_mobile_models.type_abonne',
                'abonnes_mobile_models.id  as account_id',
                'abonnes_mobile_models.created_at',

            )
            ->where('abonnes_mobile_models.user_id', $users->id)->first();

            if ($users_is_logged):
                DB::table('users')->where('id', $users->id)->update(['connected' => 1]);

                return response()->json(
                    [
                        'code' => 200,
                        'token' => $token,
                        'users' => $users_is_logged,
                        'status' => "succès",
                        'token_type' => 'Bearer',
                        'expires_in' => auth()->factory()->getTTL(),
                        'message' => 'Vous êtes connecté 💚!'
                    ]
                );
            endif;
        } catch (\Throwable $e) {
            return response()->json(
                [
                    'status' => 'erreur',
                        'code' => 500,
                    'message' => $e->getMessage()
                ]
            );
        }

    }


    public function checkUserAccount(Request $user)
    {

        try {
            if (empty($user->email)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "L'adresse e-mail est obligatoire."
                    ]
                );
            endif;

            if (!filter_var($user->email, FILTER_VALIDATE_EMAIL)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "L'adresse e-mail n'est pas valide"
                    ]
                );
            endif;

            $is_admin_accounts = DB::table('administration_models')
                ->join('users', 'administration_models.user_id', '=', 'users.id')->where('users.email', $user->email)
                ->first();

            if ($is_admin_accounts == null) {
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "Oups ! Votre compte n'existe pas ou est introuvable"
                    ]
                );
            } elseif ($is_admin_accounts != null) {
                return response()->json(
                    [
                        'code' => 200,
                        'status' => 'succès',
                        'message' => "Ok ! Vous pouvez continuer."
                    ]
                );
            }
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




    public function updateUserPassword(Request $user)
    {
        try {
            if (empty($user->email)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "L'adresse e-mail est obligatoire."
                    ]
                );
            endif;

            if (!filter_var($user->email, FILTER_VALIDATE_EMAIL)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "L'adresse e-mail n'est pas valide"
                    ]
                );
            endif;

            if (empty($user->password)):
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "Le mot de passe est obligatoire."
                    ]
                );
            endif;

            $is_admin_accounts = DB::table('administration_models')
                ->join('users', 'administration_models.user_id', '=', 'users.id')->where('users.email', $user->email)
                ->first();



            if ($is_admin_accounts == null ) {
                return response()->json(
                    [
                        'code' => 302,
                        'status' => 'erreur',
                        'message' => "Oups ! Votre compte n'existe pas ou est introuvable"
                    ]
                );
            } elseif ($is_admin_accounts != null ) {

                $password = password_hash($user->password, PASSWORD_BCRYPT);

                $isUpdated = DB::table('users')->where('email', $user->email)->update([
                    'password' => $password
                ]);
                if ($isUpdated) {

                    $notifiction = "Votre mot de passe a été modifié avec succès." . " " . "#Adresse email: " . " " . $user->email . " " . "#Nouveau mot de passe: " . " " . $user->password;
                    Mail::to($user->email)
                        ->send(new ForgetPasswordMailer($notifiction));

                    return response()->json(
                        [
                            'code' => 200,
                            'status' => 'succès',
                            'message' => "Ok ! Votre mot de passe a été modifié avec succès. Un mail vous a été envoyé sur votre adresse."
                        ]
                    );
                }
            }

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






    public function logout($id)
    {
        try {
            DB::table('users')->where('id', $id)->update(['connected' => 0]);
            Session::flush();
            Auth::logout();

            return response()->json(
                [
                    'code' => 200,
                    'status' => 'success',
                    'message' => "Merci ! Vous vous êtes déconnecté"
                ]
            );
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


    protected function guard()
    {
        return Auth::guard();
    }
}
