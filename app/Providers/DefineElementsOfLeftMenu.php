<?php

namespace App\Providers;

use App\Models\Lang;
use function foo\func;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Module;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Models\AdminUserActionPermision;

class DefineElementsOfLeftMenu extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['view']->composer('*', function ($view) {

            //get languages
            $lang_list = Lang::where('active', 1)
                ->orderBy('position', 'ASC')
                ->get();


            //change languages
//            $lang = Request::segment(1);
//            $default_lang = Lang::whereIn('id', $arr_default_lang_id)->first();
//            if (array_key_exists($lang, $arr_lang)) {
//                Session::set('applocale', $lang);
//            } else {
//                Session::forget('applocale');
//                App::setLocale($default_lang->lang);
//            }
//
//            if (Session::has('applocale') && array_key_exists(Session::get('applocale'), $arr_lang)) {
//                App::setLocale(Session::get('applocale'));
//            }
//
//            $lang = App::getLocale();
//            $lang_id = Lang::where('lang', $lang)->first()->id;



            if (Auth::check()) {
                if (!is_null(Auth::user()->admin_user_group_id)) {
                    $user = User::find(Auth::user()->id);
                    $user_group_id = Auth::user()->admin_user_group_id;

                    $menu = Module::with(['translation', 'submenu'])->get();

                    $modules_name = Module::where(
                        'src', Request::segment(3))
                        ->first();


                    $modules_sumbenu_name = Module::where(
                        'src', Request::segment(4))
                        ->first();

//                SubRelations (new, save, active, del_to_rec, del_from_rec)
                    if (!is_null($modules_name)) {
                        $groupSubRelations = AdminUserActionPermision::where('admin_user_group_id', $user_group_id)
                            ->where('modules_id', $modules_name->id)
                            ->first();
                    } elseif (!is_null($modules_sumbenu_name)) {
                        $groupSubRelations = AdminUserActionPermision::where('admin_user_group_id', $user_group_id)
                            ->where('modules_id', $modules_sumbenu_name->id)
                            ->first();
                    } else {
                        $groupSubRelations = [];
                    }
                } else {
                    $menu = [];
                    $modules_name = [];
                    $modules_sumbenu_name = [];
                    $groupSubRelations = [];
                }
            } else {
                $menu = [];
                $modules_name = [];
                $modules_sumbenu_name = [];
                $groupSubRelations = [];

            }
            $view->menu = $menu;
            $view->modules_name = $modules_name;
            $view->modules_submenu_name = $modules_sumbenu_name;
//            $view->lang = $lang;
            $view->lang_list = $lang_list;
//            $view->lang_id = $lang_id;
            $view->groupSubRelations = $groupSubRelations;
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
