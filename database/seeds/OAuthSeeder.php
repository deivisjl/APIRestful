<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class OAuthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
          DB::table('oauth_clients')->insert([
	        'id' => 1,
	        'name' => 'Laravel Personal Access Client',
	        'secret' => 'yoursecret',
	        'redirect' => 'http://localhost',
	        'personal_access_client' => 1,
	        'password_client' => 0,
	        'revoked' => 0,
	        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
	        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
	    ]);

	    DB::table('oauth_clients')->insert([
	        'id' => 2,
	        'name' => 'Laravel Password Grant Client',
	        'secret' => 'yoursecret',
	        'redirect' => 'http://localhost',
	        'personal_access_client' => 0,
	        'password_client' => 1,
	        'revoked' => 0,
	        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
	        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
	    ]);

	    DB::table('oauth_personal_access_clients')->insert([
	        'id' => 1,
	        'client_id' => 1,
	        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
	        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
	    ]);
    }
}
