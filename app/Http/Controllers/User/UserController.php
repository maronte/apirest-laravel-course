<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\UseUse;

use function Psy\debug;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return response()->json(['data' => $users], 200);
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
        $data['verification_token'] = User::generateVerificationCode();
        $data['is_admin'] = User::USUARIO_REGULAR;

        $user = User::create($data);

        return $this->showOne($user, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        return response()->json(['data' => $user], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

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
                return response()->json(
                    ['error' => 'El usuario debe estar verificado para ser administrador',
                    'code' => 409], 409
                );
            }
            $user->is_admin = $request->is_admin;
        }
        
        if(!$user->isDirty()){
            return response()->json(
                ['error' => 'El usuario debe tener por lo menos un atributo diferente para ser actualizado',
                'code' => 422], 422
            );  
        }

        $user->save();

        return response()->json(['data' => $user], 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return response()->json(['data' => $user], 200);
    }
}
