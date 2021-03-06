<?php

namespace App\Http\Controllers\User;

use App\User;
use App\Mail\UserCreated;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Transformers\UserTransformer;
use App\Http\Controllers\ApiController;

class UserController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(){

        $this->middleware('client.credentials')->only(['store','resend']);

        $this->middleware('auth:api')->except(['store','verify','resend']);

        $this->middleware('transform.input:' . UserTransformer::class)->only(['store','update']);

        $this->middleware('scope:manage-account')->only(['show','update']);

        $this->middleware('can:view,user')->only('show');        

        $this->middleware('can:update,user')->only('update');

        $this->middleware('can:delete,user')->only('destroy');
    }

    public function index()
    {
        $this->allowedAdminAction();

        $usuarios = User::all();

        return $this->showAll($usuarios);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        
        $reglas = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed'
        ];


        $this->validate($request, $reglas);


        $campos = $request->all();

        $campos['password'] = bcrypt($request->password);

        $campos['verified'] = User::USUARIO_NO_VERIFICADO;

        $campos['verification_token'] = User::generarVerificationToken();

        $campos['admin'] = User::USUARIO_REGULAR;

        $usuario = User::create($campos);

        return $this->showOne($usuario,201);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
       // $usuario = User::findOrFail($id);

        return $this->showOne($user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //$user = User::findOrFail($id);



        $reglas = [
            'email' => 'email|unique:users,email,' . $user->id,
            'password' => 'min:6|confirmed',
            'admin' => 'in:' .User::USUARIO_ADMINISTRADOR . ',' . User::USUARIO_REGULAR,
        ];

        $this->validate($request,$reglas);

        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email') && $user->email !=  $request->email) {
            
                $user->verified = User::USUARIO_NO_VERIFICADO;
                $user->verification_token = User::generarVerificationToken();
                $user->email = $request->email;

        }

        if ($request->has('password')) {
            $user->password = bcrypt($request->password);
        }

        if ($request->has('admin')) {

            $this->allowedAdminAction();
            
            if (!$user->esVerificado()) {
                return $this->errorResponse('Unicamente los usuarios verificados puede cambiar su valor de administrador',409);
            }

            $user->admin = $request->admin;
        }

        if (!$user->isDirty()) {
            return $this->errorResponse('Se debe especificar al menos un valor diferente para actualizar','code',422);
        }

        $user->save();

        return $this->showOne($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //$user = User::findOrFail($id);

        $user->delete();

        return $this->showOne($user);
    }

    public function verify($token){

        $user = User::where('verification_token', $token)->firstOrFail();

        $user->verified = User::USUARIO_VERIFICADO;

        $user->verification_token = null;

        $user->save();

        return $this->showMessage('La cuenta ha sido verificada');

    }

    public function resend(User $user){

        if ($user->esVerificado()) {
            
            return $this->errorResponse('Este usuario ya ha sido verficado.', 409);
        }

        retry(5, function() use ($user){
            
            Mail::to($user)->send(new UserCreated($user));
        },100);

        return $this->showMessage('El correo de verificacion ha sido reenviado');
    }

    public function me(Request $request)
    {
        $user = $request->user();
        return $this->showOne($user);
    }
}
