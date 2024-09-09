<?php

use App\Http\Controllers\API\V1\Transactions\NotifyController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WellcomeController;
use App\Http\Controllers\API\V1\RoleController;
use App\Http\Controllers\API\V1\Banners\BannerController;
use App\Http\Controllers\API\V1\Galeries\GalerieController;
use App\Http\Controllers\API\V1\Redactions\FlashController;
use App\Http\Controllers\API\V1\Quoideneufs\MediaController;
use App\Http\Controllers\API\V1\Redactions\DepecheController;
use App\Http\Controllers\API\V1\Auth\AuthentificatorController;
use App\Http\Controllers\API\V1\Quoideneufs\ArticlesController;
use App\Http\Controllers\API\V1\Redactions\CountriesController;
use App\Http\Controllers\API\V1\Redactions\RubriquesController;
use App\Http\Controllers\API\V1\Frontend\FrontendMobileController;
use App\Http\Controllers\API\V1\DiffusionSms\CarnetAdressController;
use App\Http\Controllers\API\V1\Frontend\FrontendAlerteInfoController;
use App\Http\Controllers\API\V1\Frontend\FrontendQuoideneufController;
use App\Http\Controllers\API\V1\DiffusionSms\GestionOperatorController;
use App\Http\Controllers\API\V1\UsersAccounts\AdministrationController;
use App\Http\Controllers\API\V1\EventsKeyWords\EventsKeyWordsController;
use App\Http\Controllers\API\V1\Abonnements\ForfaitsAbonnementController;
use App\Http\Controllers\API\V1\Statistiques\AdminStatistiquesController;
use App\Http\Controllers\API\V1\Quoideneufs\GenreJournalistiqueController;
use App\Http\Controllers\API\V1\Quoideneufs\RubriquesQuoideneufController;

Route::get('/welcome', [WellcomeController::class, 'index']);



Route::post('/login_admin', [AuthentificatorController::class, 'admin_authentificator']);
//AUTHENTIFICATED ABONNE
Route::post('/login_mobile_abonne', [AuthentificatorController::class, 'authentificated_abonne']);


// ADMINISTRATION
Route::post('/check_user_account', [AuthentificatorController::class, 'checkUserAccount']);
Route::post('/update_user_password', [AuthentificatorController::class, 'updateUserPassword']);


Route::post('/check_matricule', [AuthentificatorController::class, 'check_matricule']);

//Route::post('/login_mobile', [AbonneController::class, 'login_mobile']);

Route::get('/un_authorised', [AuthentificatorController::class, 'un_authorised'])->name('un_authorised');



/**
 * =========================== START QUOIDENEUF ===============================
 * ++++++++++++++++++++++++++++++ ********************* +++++++++++++++++++++++++++++++++++++++++++++
*/

// ====== RUBRIQUE QUOIDENEUF 💚====================

Route::group(
    [
        'middleware' => 'api',
        'namespace' => 'App\Http\Controllers\API\V1\Quoideneufs',
    ],
    function ($router)
    {
        Route::controller(RubriquesQuoideneufController::class)->group(function () {
            // RUBRIQUE QUOIDENEUF 💚
            Route::get('/get_rubrique_quoideneuf',  'index');
            Route::get('/get_news_rubrique',  'get_news_rubriques');
            Route::get('/get_others_rubrique_quoideneuf',  'get_other_rubrique');
            Route::post('/add_rubrique_quoideneuf',  'store');
            Route::post('/update_rubrique_quoideneuf/{slug}',  'update');
            Route::get('/delete_rubrique_quoideneuf/{slug}',  'destroy');
        });

    }
);

// ====== GENRE JOURNALISTIQUE QUOIDENEUF 💚====================

Route::group(
    [
        'middleware' => 'api',
        'namespace' => 'App\Http\Controllers\API\V1\Quoideneufs',
    ],
    function ($router)
    {
        Route::controller(GenreJournalistiqueController::class)->group(function () {
            // GENRE JOURNALISTIQUE QUOIDENEUF 💚
            Route::get('/get_genre_journalistique', 'index');
            Route::post('/add_genre_journalistique', 'store');
            Route::post('/update_genre_journalistique/{slug}', 'update');
            Route::get('/delete_genre_journalistique/{slug}', 'destroy');
        });
    }
);

//======= ARTICLES QUOIDENEUF  💚====================

Route::group(
    [
        'middleware' => 'api',
        'namespace' => 'App\Http\Controllers\API\V1\Quoideneufs',
    ],
    function ($router) {
        // ARTICLES QUOIDENEUF 💚
        Route::controller(ArticlesController::class)->group(function () {


            // BACKEND
            Route::get('/get_articles', 'index');
            Route::get('/get_recente_articles', 'get_recente_article');
            Route::get('/get_customer_news/{customer}', 'get_customer_news');
            Route::get('/single_articles/{slug}', 'detail_article_on_backend');
            Route::get('/get_articles_hebdo_statistique', 'get_article_hebdo_statistique');
            Route::post('/add_articles', 'store');
            Route::post('/update_articles/{slug}', 'update');
            Route::get('/destroy_articles/{slug}', 'destroy');

            Route::get('/push_article/{slug}', 'push_article');
            Route::post('/filter_on_news', 'filter_on_news');
            Route::post('/filter_on_customer_news', 'filter_on_customer_news');
        });
    }
);


// ======= MEDIA QUOIDENUF 💚=====================

Route::group(
    [
        'middleware' => 'api',
        'namespace' => 'App\Http\Controllers\API\V1\Quoideneufs',
    ],
    function ($router)
    {

        Route::controller(MediaController::class)->group(function () {
             // MEDIA QUOIDENUF 💚
            Route::get('/get_media', 'index');
            Route::get('/get_recente_media', 'get_recente_media');
            Route::post('/add_media', 'store');
            Route::get('/view_media/{id}', 'view');
            Route::post('/update_media/{slug}', 'update');
            Route::get('/destroy_media/{slug}', 'destroy');
            Route::post('/filter_on_media', 'filter_on_media');
        });

    }
);



/**
 *      =========================== END QUOIDENEUF ===============================
 * ++++++++++++++++++++++++++++++ ********************* +++++++++++++++++++++++++++++++++++++++++++++
 */



 //==================================== ADMIN STATISTIQUES 💚====================
Route::group(
    [
        'middleware' => 'api',
        'namespace' => 'App\Http\Controllers\API\V1\Statistiques',
    ],
    function ($router)
    {
        Route::controller(AdminStatistiquesController::class)->group(function () {
            // ADMIN STATISTIQUES 💚
            Route::get('/get_default_statistiques', 'default_statistiques');
        });
    }
);
//==================================== END ADMIN STATISTIQUES 💚====================




/**
 *      =========================== START REDACTION ===============================
 * ++++++++++++++++++++++++++++++++ ***************** +++++++++++++++++++++++++++++++++++++++++++++
 */

//======= RUBRIQUE 💚====================
Route::group(
    [
        'middleware' => 'api',
        'namespace' => 'App\Http\Controllers\API\V1\Redactions',
    ],
    function ($router)
    {

        Route::controller(RubriquesController::class)->group(function () {
            // RUBRIQUE 💚
            Route::get('/get_rubrique', 'index');
            Route::post('/add_rubrique', 'store');
            Route::post('/update_rubrique/{slug}', 'update');
            Route::get('/delete_rubrique/{slug}', 'destroy');
        });

    }
);


Route::group([

    'middleware' => 'api',
    'namespace' => 'App\Http\Controllers\API\V1\Redactions',

], function ($router)
{
    Route::controller(CountriesController::class)->group(function () {
        // PAYS 💚
        Route::get('/get_pays', 'index');
        Route::post('/add_pays', 'store');
        Route::post('/update_pays/{slug}', 'update');
        Route::get('/destroy_pays/{slug}', 'destroy');
    });
});


// ==================================== FLASHES 💚====================
Route::group(
    [
        'middleware' => 'api',
        'namespace' => 'App\Http\Controllers\API\V1\Redactions',
    ],
    function ($router)
    {

        Route::controller(FlashController::class)->group(function () {
           // FLASHES 💚
            Route::get('/get_flash', 'index');
            Route::get('/get_recente_flash', 'get_recente_flash');
            Route::get('/single_flash/{slug}', 'single_flash');

            Route::post('/add_flash', 'store');
            Route::post('/update_flash/{slug}', 'update');
            Route::get('/destroy_flash/{slug}', 'destroy');
            Route::get('/push_flash/{slug}', 'push_flash');
            Route::get('/get_flash_hebdo_statistique', 'get_flash_hebdo_statistique');
            Route::post('/filter_on_flash', 'filter_on_flash');
        });
    }
);



// ====== DEPECHES 💚====================
Route::group(
    [
        'middleware' => 'api',
        'namespace' => 'App\Http\Controllers\API\V1\Redactions',
    ],
    function ($router)
    {

        Route::controller(DepecheController::class)->group(function () {
            // DEPECHES 💚
            Route::get('/get_depeche', 'index');
            Route::get('/get_recente_depeche', 'get_recente_depeche');
            Route::get('/get_detail_depeche_on_backend/{slug}', 'get_detail_depeche_on_backend');
            Route::get('/get_depeche_hebdo_statistique', 'get_depeche_hebdo_statistique');
            Route::post('/add_depeche', 'store');
            Route::post('/update_depeche/{slug}', 'update');
            Route::get('/destroy_depeche/{slug}', 'destroy');
            Route::get('/push_depeche/{slug}', 'push_depeche');
            Route::post('/filter_on_depeche', 'filter_on_depeche');
        });

    }
);


/**
 *      =========================== END REDACTION ===============================
 * ++++++++++++++++++++++++++++++++ ***************** +++++++++++++++++++++++++++++++++++++++++++++
 */




/**
 *      =========================== START MAIN CONTENTS ===============================
 * ++++++++++++++++++++++++++++++++ ***************** +++++++++++++++++++++++++++++++++++++++++++++
 */


// ======= EVENTS KEYS WORD 💚====================
Route::group(
    [
        'middleware' => 'api',
        'namespace' => 'App\Http\Controllers\API\V1\EventsKeyWords',
    ],
    function ($router)
    {
        Route::controller(EventsKeyWordsController::class)->group(function () {
            // EVENTS KEYS WORD 💚
            Route::get('/get_event_keywords', 'get');
            Route::get('/get_news_by_event_keywords/{keywords}', 'get_news_by_event_keywords');
            Route::post('/add_event_key_words', 'store');
            Route::get('/active_event_key_words/{slug}', 'active_event_key_words');
            Route::post('/update_event_key_words/{slug}', 'update');
            Route::get('/delete_event_key_words/{slug}', 'destroy');
        });
    }
);




// ========= BANNERS 💚====================
Route::group(
    [
        'middleware' => 'api',
        'namespace' => 'App\Http\Controllers\API\V1',
    ],
    function ($router)
    {
        Route::controller(BannerController::class)->group(function () {
            // BANNER 💚
            Route::get('/get_banner_728X90', 'get_728X90');
            Route::get('/get_banner_1920X309', 'get_1920X309');
            Route::get('/get_banner_1200X1500', 'get_1200X1500');
            Route::get('/get_on_backend', 'index');
            Route::post('/add_banner', 'store');
            Route::post('/update_banner/{slug}', 'update');
            Route::get('/delete_banner/{slug}', 'destroy');
            Route::get('/enable_or_disable_banner/{slug}', 'enable_or_disable_banner');
        });
    }
);


/**
 *      =========================== END MAIN CONTENTS ===============================
 * ++++++++++++++++++++++++++++++++ ***************** +++++++++++++++++++++++++++++++++++++++++++++
 */


/**
 *      =========================== START DIFFUSION SMS ===============================
 * ++++++++++++++++++++++++++++++++ ***************** +++++++++++++++++++++++++++++++++++++++++++++
 */

//======= CARNET ADDRESS 💚====================
Route::group(
    [
        'middleware' => 'api',
        'namespace' => 'App\Http\Controllers\API\V1\DiffusionSms',
    ],
    function ($router)
    {

        Route::controller(CarnetAdressController::class)->group(function () {
            // SINGLE CARNET ADDRESS 💚
            Route::get('/get_all_group_contact', 'index_group_contact');
            Route::post('/store_group_contact', 'store_group_contact');
            Route::post('/update_group_contact/{id}', 'update_group_contact');
            Route::get('/delete_group_contact/{id}', 'destroy_group_contact');

            // GROUP CARNET ADDRESS 💚
            Route::get('/get_all_single_contact', 'index_single_contact');
            Route::post('/store_single_contact', 'store_single_contact');
            Route::post('/update_single_contact/{id}', 'update_single_contact');
            Route::get('/delete_single_contact/{id}', 'destroy_single_contact');

            // FILE CARNET ADDRESS 💚
            Route::post('/store_document_contact', 'store_document_contact');
        });

    }
);


//======= GESTION OPERATOR 💚====================
Route::group(
    [
        'middleware' => 'api',
        'namespace' => 'App\Http\Controllers\API\V1\DiffusionSms'
    ],
    function ($router)
    {

        Route::controller(GestionOperatorController::class)->group(function () {

            // GESTION OPERATOR 💚
            Route::get('/get_all_operator', 'index');
            Route::post('/store_operator', 'store');
            Route::post('/update_operator/{id}', 'update');
            Route::get('/delete_operator/{id}', 'destroy');
            Route::get('/check_status_operator/{id}', 'check_status');
        });

    }
);

/**
 *      =========================== END DIFFUSION SMS ===============================
 * ++++++++++++++++++++++++++++++++ ***************** +++++++++++++++++++++++++++++++++++++++++++++
 */




/**
 *      =========================== START USER ACCOUNTS ===============================
 * ++++++++++++++++++++++++++++++ ********************* +++++++++++++++++++++++++++++++++++++++++++++
 */







// ==================================== COMPTE ADMIN 💚====================
Route::group(
    [
        'middleware' => 'api',
        'namespace' => 'App\Http\Controllers\API\V1\UsersAccounts',
    ],
    function ($router)
    {
        Route::controller(AdministrationController::class)->group(function () {
            // COMPTE ADMIN 💚
            Route::get('get_admin', 'index');
            Route::post('/add_admin', 'store');
            Route::get('/show_admin/{slug}', 'show');
            Route::post('/update_admin/{slug}', 'update');
            Route::get('/delete_admin/{slug}', 'destroy');
            Route::get('/enable_or_disable_admin_account/{slug}', 'enable_or_disable_account');
            Route::post('/update_administration_photo', 'update_administration_photo');
        });

    }

);

/**
 *      =========================== END USER ACCOUNTS ===============================
 * ++++++++++++++++++++++++++++++++ ***************** +++++++++++++++++++++++++++++++++++++++++++++
 */







// ==================================== LOGOUT 💚====================
Route::group(
    [
        'middleware' => 'api',
        'namespace' => 'App\Http\Controllers\API\V1\Auth',
    ],
    function ($router)
    {
        // LOGOUT 💚
        Route::get('/logout_users/{user_id}', [AuthentificatorController::class, 'logout']);
    }
);














// ==================================== ROLES 💚====================
Route::group(
    [
        'middleware' => 'api',
        'namespace' => 'App\Http\Controllers\API\V1',
    ],
    function ($router)
    {
        // ROLES 💚
        Route::get('/get_role', [RoleController::class, 'get']);
        Route::post('/add_role', [RoleController::class, 'add']);
        Route::post('/update_role/{slug}', [RoleController::class, 'update']);
        Route::get('/delete_role/{slug}', [RoleController::class, 'destroy']);


    }
);



 // ======= GALERIE 💚=====================

Route::group(
    [
        'middleware' => 'api',
        'namespace' => 'App\Http\Controllers\API\V1\Galeries',
    ],
    function ($router)
    {

        Route::controller(GalerieController::class)->group(function () {
             // GALERIE 💚
            Route::get('/get_galerie', 'index');
            Route::get('/get_galerie_limited', 'get_galerie_limited');
            Route::get('/get_recente_galerie', 'get_recente_galerie');
            Route::get('/checked_img/{query}', 'check');
            Route::post('/add_galerie', 'store');
            Route::get('/view_galerie/{id}', 'view');
            Route::post('/update_galerie/{slug}', 'update');
            Route::get('/destroy_galerie/{slug}', 'destroy');
            Route::get('/filter_on_galerie/{query}', 'filter_on_galerie');
        });

    }
);



// ==================================== FORFAITS 💚====================
Route::group(
    [
        'middleware' => 'api',
        'namespace' => 'App\Http\Controllers\API\V1\Abonnements',
    ],
    function ($router)
    {
        // FORFAITS 💚
        Route::get('/get_forfaits', [ForfaitsAbonnementController::class, 'index']);
        Route::post('/add_forfaits', [ForfaitsAbonnementController::class, 'store']);
        Route::get('/edit_forfaits/{slug}', [ForfaitsAbonnementController::class, 'edit']);
        Route::post('/update_forfaits/{slug}', [ForfaitsAbonnementController::class, 'update']);
        Route::get('/destroy_forfaits/{slug}', [ForfaitsAbonnementController::class, 'destroy']);

    }
);
/**
 *
 *
 *
    // ==================================== ABONNE 💚====================
    Route::group(
        [
            'middleware' => 'api',
            'namespace' => 'App\Http\Controllers\API\V1',
        ],
        function ($router)
        {
            // ABONNE 💚
            Route::get('/get_abonne', [AbonneController::class, 'get']);
            Route::get('/profil_abonne/{id}', [AbonneController::class, 'profil_abonne']);
            Route::post('/add_abonne', [AbonneController::class, 'add']);
            Route::get('/edit_abonne/{id}', [AbonneController::class, 'edit']);
            Route::put('/update_abonne', [AbonneController::class, 'update']);
            Route::post('/reset_password', [AbonneController::class, 'reset_password']);
            Route::post('/find_abonne', [AbonneController::class, 'find_email']);


            Route::get('/destroy_abonne/{id}/{author}', [AbonneController::class, 'destroy']);
            Route::get('/lock_abonne/{id}/{author}', [AbonneController::class, 'lock_abonne']);
            Route::get('/unlock_abonne/{id}/{author}', [AbonneController::class, 'unlock_abonne']);
            Route::get('/logout_mobile', [AbonneController::class, 'logout_mobile']);
        }
    );


    // ==================================== TYPE ABONNEMENT 💚====================
    Route::group(
        [
            'middleware' => 'api',
            'namespace' => 'App\Http\Controllers\API\V1',
        ],
        function ($router)
        {
            // TYPE ABONNEMENT 💚
            Route::get('/get_type_abonnement', [TypeAbonnementController::class, 'get']);
            Route::post('/add_type_abonnement', [TypeAbonnementController::class, 'add']);
            Route::get('/edit_type_abonnement/{id}', [TypeAbonnementController::class, 'edit']);
            Route::post('/update_type_abonnement/{id}', [TypeAbonnementController::class, 'update']);
            Route::get('/destroy_type_abonnement/{id}/{author}', [TypeAbonnementController::class, 'destroy']);


        }
    );

    // ==================================== ABONNEMENT 💚====================
    Route::group(
        [
            'middleware' => 'api',
            'namespace' => 'App\Http\Controllers\API\V1',
        ],
        function ($router)
        {
            // ABONNEMENT 💚
            Route::get('/get_abonnement', [AbonnementController::class, 'get']);
            Route::get('/get_abonnement_actif', [AbonnementController::class, 'get_abonnement_actif']);
            Route::post('/add_abonnement', [AbonnementController::class, 'add']);
            Route::get('/edit_abonnement/{id}', [AbonnementController::class, 'edit']);
            Route::post('/update_abonnement/{id}', [AbonnementController::class, 'update']);
            Route::get('/destroy_abonnement/{id}/{author}', [AbonnementController::class, 'destroy']);
            Route::get('/lock_abonnement/{id}/{author}', [AbonnementController::class, 'lock_abonnement']);
            Route::get('/unlock_abonnement/{id}/{author}', [AbonnementController::class, 'unlock_abonnement']);
            Route::put('/reabonnement', [AbonnementController::class, 'reabonnement']);
        }
    );




    // ==================================== TYPE ABONNEMENT SMS 💚====================
    Route::group(
        [
            //'middleware' => 'api',
            //'namespace' => 'App\Http\Controllers\API\V1',
        ],
        function ($router)
        {
            // JOURNAL 💚
            Route::get('/get_type_abonnement_sms', [TypeAbonnementSmsController::class, 'get']);
            Route::post('/add_type_abonnement_sms', [TypeAbonnementSmsController::class, 'add']);
            Route::get('/edit_type_abonnement_sms/{rfk}', [TypeAbonnementSmsController::class, 'edit']);
            Route::post('/update_type_abonnement_sms/{rfk}', [TypeAbonnementSmsController::class, 'update']);
            Route::get('/destroy_type_abonnement_sms/{id}/{author}', [TypeAbonnementSmsController::class, 'destroy']);

        }
    );

 *
*/


//======= FRONTEND ARTICLES QUOIDENEUF  💚====================
Route::group(
    [
        'middleware' => 'api',
        'namespace' => 'App\Http\Controllers\API\V1\Frontend',
    ],
    function ($router) {
        // ARTICLES QUOIDENEUF 💚
        Route::controller(FrontendQuoideneufController::class)->group(function () {

            // FRONTEND


            Route::get('/get_frontend_news_rubrique',  'get_frontend_news_rubriques');
            Route::get('/get_frontend_others_rubrique_quoideneuf',  'get_frontend_other_rubrique');

            Route::get('/get_frontend_une_news_article', 'get_frontend_une_news_article');
            Route::get('/get_frontend_archive_article', 'get_frontend_archive_article');
            Route::get('/get_frontend_politique_article', 'get_frontend_politique_article');
            Route::get('/get_frontend_economie_article', 'get_frontend_economie_article');
            Route::get('/get_frontend_popular_article', 'get_frontend_popular_article');
            Route::get('/get_frontend_media_news', 'get_frontend_media_news');
            Route::get('/get_frontend_detail_news/{slug}', 'detail_article_quoideneuf');
            Route::get('/get_frontend_article_with_rubrique/{slug}', 'get_frontend_article_with_rubrique');
            Route::get('/get_frontend_flash_info', 'get_frontend_flash_info');
            Route::get('/get_frontend_articles_archive', 'get_frontend_articles_archive');
            Route::get('/get_frontend_archive_data/{mounth}/{year}', 'get_frontend_archive_data');
            Route::get('/get_frontend_similar_article/{rubrique_id}/{slug}', 'get_frontend_similar_article');

            Route::post('/like_frontend_news', 'like_frontend_news');
            Route::get('/get_frontend_media', 'get_frontend_media');
            Route::post('/dislike_frontend_news', 'dislike_frontend_news');

            Route::get('/get_frontend_event_keywords', 'get_frontend_event_keywords');
            Route::get('/get_frontend_news_by_event_keywords/{keywords}', 'get_frontend_news_by_event_keywords');

            // search
            Route::get('/filter_on_news_with_query/{query}', 'filter_on_news_with_query');

            Route::get('/get_frontend_banner_728X90', 'get_frontend_728X90');
            Route::get('/get_frontend_banner_1920X309', 'get_frontend_1920X309');
            Route::get('/get_frontend_banner_1200X1500', 'get_frontend_1200X1500');

            // Route::any('/notify', [App\Http\Controllers\API\V1\Transactions\NotifyController::class, 'notify'])->name('paiement-notify');


        });
    }
);


// ===========================END FRONTEND ARTICLES QUOIDENEUF ========================





//======= FRONTEND ARTICLES ARLERTE INFO  💚====================
Route::group(
    [
        'middleware' => 'api',
        'namespace' => 'App\Http\Controllers\API\V1\Frontend',
    ],
    function ($router) {
        // ARTICLES ARLERTE INFO 💚
        Route::controller(FrontendAlerteInfoController::class)->group(function () {

            // FRONTEND

            Route::get('/get_alerteinfo_home_page_data', 'get_alerteinfo_home_page_data');
            // get alerte-info
            Route::get('/get_alerteinfo_depeche_details_by_slug/{item_slug}',  'get_alerteinfo_depeche_details_by_slug');
            // get alerte-info depeche archives
            Route::get('/get_alerteinfo_depeche_archives',  'get_alerteinfo_depeche_archives');
            // get alerte-info depeche archives data
            Route::get('/get_alerteinfo_depeche_archives_data_by_mounth_and_year/{mounth}/{year}', 'get_alerteinfo_depeche_archives_data_by_mounth_and_year');
            // get alerte-info depeche by country
            Route::get('/get_alerteinfo_depeche_by_country/{country_id}', 'get_alerteinfo_depeche_by_country');
            // get alerte-info depeche by rubrique
            Route::get('/get_alerteinfo_depeche_by_rubrique/{rubrique_id}', 'get_alerteinfo_depeche_by_rubrique');



        });
    }
);



//======= FRONTEND MOBILE CONTROLLER  💚====================
Route::group(
    [
        'middleware' => 'api',
        'namespace' => 'App\Http\Controllers\API\V1\Frontend',
    ],
    function ($router) {
        // MOBILE CONTROLLER 💚
        Route::controller(FrontendMobileController::class)->group(function () {

            // FRONTEND



            Route::post('/get_mobile_recents_depeches_and_flashes', 'get_mobile_recents_depeches_and_flashes');

            // DEPECHE REQUEST
            Route::get('/get_mobile_depeches',  'get_mobile_depeches');
            Route::get('/get_mobile_depeche_details_by_slug/{item_slug}',  'get_mobile_depeche_details_by_slug');
            Route::get('/get_mobile_depeche_by_rubrique/{rubrique_id}',  'get_mobile_depeche_by_rubrique');
            Route::get('/get_mobile_depeche_by_customer_country/{country_id}',  'get_mobile_depeche_by_country');
            Route::get('/get_mobile_depeche_archives', 'get_mobile_depeche_archives');
            Route::get('/get_mobile_depeche_archives_data/{mounth}/{year}', 'get_mobile_depeche_archives_data');

            // FLASHES REQUEST
            Route::get('/get_mobile_flashes', 'get_mobile_flashes');
            Route::get('/get_mobile_recents_flashes', 'get_mobile_recents_flashes');
            Route::get('/get_mobile_flashes_by_rubrique/{rubrique_id}', 'get_mobile_flashes_by_rubrique');
            Route::get('/get_mobile_flashes_by_country/{country_id}', 'get_mobile_flashes_by_country');

            Route::get('/get_mobile_flashes_archives', 'get_mobile_flashes_archives');
            Route::get('/get_mobile_flashes_archives_data/{mounth}/{year}', 'get_mobile_flashes_archives_data');

            Route::get('/get_mobile_countries_list', 'get_mobile_countries_list');


            Route::get('/get_mobile_banner_728X90', 'get_mobile_banner_728X90');

            Route::get('/get_mobile_countries', 'get_mobile_countries');
            Route::get('/get_mobile_rubriques', 'get_mobile_rubriques');



            Route::get('/get_mobile_forfait_abonnements', 'get_mobile_forfait_abonnements');

            /// ABONNE MOBILE AND ABONNEMENTS

            Route::post('/store_mobile_abonne_data', 'store_mobile_abonne_data');
            Route::post('/store_mobile_abonnement_data', 'store_mobile_abonnement_data');

            Route::get('/forfaits_list', 'forfaitsList');
            Route::post('/add_user_abonnement', 'store_abonne_abonnements');
            // Route::any('/notify', 'notify');

            // Route pour obtenir les détails d'un abonnement via le code d'abonnement
            Route::get('/abonnement/details/{abonnement_code}','getAbonnementDetails');

            // Route pour obtenir les détails du dernier abonnement valide ou le plus récent pour l'utilisateur authentifié
            Route::get('/get-current-abonnement', 'getLatestAbonnementDetails');

        });
    }
);

Route::any('/notify', [FrontendMobileController::class, 'notify']);

Route::get('/contact-info', function () {
    return response()->json([
        'phone' => '+225 01 02 500 320 / +225 07 09 62 06 06',
        'whatsapp' => '+225 01 02 500 320',
        'email' => 'direction@alerte-info.net',
        'facebook' => 'https://web.facebook.com/ALERTEINFOCIV'
    ]);
});



// ===========================END FRONTEND DEPECHES ========================

