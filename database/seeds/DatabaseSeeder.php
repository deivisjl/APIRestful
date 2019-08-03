<?php

use App\User;
use App\Product;
use App\Category;
use App\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        // $this->call(UsersTableSeeder::class);
        User::truncate();
        Category::truncate();
        Product::truncate();
        Transaction::truncate();
        DB::table('category_product')->truncate();

        /*
        |------------------------------------------------------------------|
        |      Evento para desactivar envio de correo al hacer el seed     |
        |------------------------------------------------------------------|
        |                                                                  |
        |   Metodos para invalidar el envio de correos de mailCreated      |
        |                                                                  |
        |------------------------------------------------------------------|
        */
        User::flushEventListeners();        
        Category::flushEventListeners();
        Product::flushEventListeners();
        Transaction::flushEventListeners();


        $cantidadUsuarios = 200;
        $cantidadCategories = 30;
        $cantidadProductos = 1000;
        $cantidadTransaction = 1000;

        factory(User::class, $cantidadUsuarios)->create();
        factory(Category::class, $cantidadCategories)->create();

        factory(Product::class,$cantidadUsuarios)->create()->each(
        		function ($producto){
        			$categorias = Category::all()->random(mt_rand(1,5))->pluck('id');

        			$producto->categories()->attach($categorias);
        		}
        	);

        factory(Transaction::class, $cantidadTransaction)->create();

    }
}
