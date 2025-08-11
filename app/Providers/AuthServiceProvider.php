<?php

namespace App\Providers;

use App\Models\Menu;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $role_id = session('role_id');
        $menus = Menu::all();

        foreach ($menus as $menu) {
            Gate::define($menu->url, function ($user) use ($role_id, $menu) {
                return $user->hasAccess($role_id, $menu->id);
            });
        }
    }
}
