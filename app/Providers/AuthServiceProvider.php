<?php

namespace App\Providers;

use App\User;
use App\Buyer;
use App\Seller;
use App\Product;
use Carbon\Carbon;
use App\Policies\UserPolicy;
use App\Policies\BuyerPolicy;
use App\Policies\SellerPolicy;
use Laravel\Passport\Passport;
use App\Policies\ProductPolicy;
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
        //'App\Model' => 'App\Policies\ModelPolicy',
        Buyer::class => BuyerPolicy::class,
        Seller::class => SellerPolicy::class,
        User::class => UserPolicy::class,
        Transaction::class => TransactionPolicy::class,
        Product::class => ProductPolicy::class,

    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('admin-action',function($user){
            
            return $user->esAdministrador();
        });

        Passport::routes();
        Passport::tokensExpireIn(Carbon::now()->addMinutes(30));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));
        Passport::enableImplicitGrant();

        Passport::tokensCan([
                'purchase-product' => 'Crear transacciones para compra productos determinados',
                'manage-products' => 'Crear, ver, actualizar y eliminar productos',
                'manage-account' => 'Obtener la informacion de la cuenta, nombre, email, estado( sin 
                                        contraseña), modificar datos como email, nombre y contraseña. No puede eliminar la cuenta',
                'read-general' => 'Obtener informacion general, categorias donde se compra y se vende, 
                                    productos vendidos o comprados, transacciones, compras y ventas',
            ]);
    }
}
