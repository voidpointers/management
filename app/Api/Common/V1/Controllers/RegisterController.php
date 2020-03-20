<?php

namespace Api\Common\V1\Controllers;

use App\Controller;
use App\User;
use Api\Common\V1\Requests\RegisterRequest;
use Api\Common\V1\Traits\ResponseTrait;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    use ResponseTrait;

    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(RegisterRequest $request)
    {
        // 校验用户名是否已存在
        if ($this->userByName($request->input('username', ''))) {
            return $this->response->error('该用户已注册，请登录', 500);
        }

        $user = $this->create($request->all());

        return $this->respondWithUser($user);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * 根据用户名获取用户
     */
    protected function userByName($username)
    {
        return User::where(['username' => $username])->first();
    }
}
