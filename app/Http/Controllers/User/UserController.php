<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use App\Mail\UserCreated;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return $this->showAll($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ];

        $this->validate($request, $rules);

        $data = $request->all();
        $data['password'] = bcrypt($request->password);
        $data['verified'] = User::USUARIO_NO_VERIFICADO;
        $data['verification_token'] = User::generateVerificationToken();
        $data['is_admin'] = User::USUARIO_REGULAR;

        $user = User::create($data);

        return $this->showOne($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {

        return $this->showOne($user);
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
        
        $rules = [
            'email' => 'email|unique:users,email,' . $user->id,
            'password' => 'min:6|confirmed',
            'is_admin' => 'in:' . User::USUARIO_REGULAR . ',' . User::USUARIO_ADMINSTRADOR,
        ];

        $this->validate($request, $rules);

        if($request->has('name')) $user->name = $request->name;

        if($request->has('email') && $user->email != $request->email) {
            $user->email = $request->email;
            $user->verified = User::USUARIO_NO_VERIFICADO;
            $user->verification_token = User::generateVerificationToken();
        }

        if($request->has('password')){
            $password = bcrypt($request->password);
            $user->password = $password;
        } 

        if($request->has('is_admin')){
            if(!$user->esVerificado()){
                return $this->errorResponse(
                    'El usuario debe estar verificado para ser administrador', 409);
            }
            $user->is_admin = $request->is_admin;
        }
        
        if(!$user->isDirty()){
            return $this->errorResponse(
                'El usuario debe tener por lo menos un atributo diferente para ser actualizado', 422);  
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

        $user->delete();

        return $this->showOne($user);
    }

    public function verify($token)
    {
        $user = User::where('verification_token', $token)->firstOrFail();

        $user->verified = User::USUARIO_VERIFICADO;
        $user->verification_token = null;
        $user->save();

        return $this->showMessage('El usuario ha sido verificado');

    }

    public function resend(User $user){
        if($user->esVerificado()){
            return $this->errorResponse('Este usuario ya ha sido verificado', 409);
        }

        retry(5, function() use($user){
            Mail::to($user->email)->send(new UserCreated($user));
        },100);

        return $this->showMessage('Se ha reenviado el correo de verificación');

    }
}
