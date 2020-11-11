<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check())
        {
            return redirect()->route('admin.home');
        }
        return view('admin.index');
    }

    public function home()
    {
        return view('admin.dashboard');
    }

    public function login(Request $request)
    {


        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if (in_array('',$request->only('email','password')))
        {
            $json['message'] =  $this->message->error("Oops, Informe todos os dados para fazer login")->render();
            return response()->json($json);
        }

//        if (filter_var($request->email, FILTER_VALIDATE_EMAIL))
//        {
//            $json['message'] =  $this->message->error("Oops, Informe um email válido")->render();
//            return response()->json($json);
//        }

        if (!Auth::attempt($credentials))
        {
            $json['message'] =  $this->message->error("Oops, Usuário e Senha não conferem")->render();
            return response()->json($json);
        }

        $this->authenticate($request->getClientIp());
        $json['redirect'] = route('admin.home');
        return response()->json($json);

    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login');
    }

    private function authenticate($ip){
        $user = User::where('id',Auth::user()->id);
        $user->update([
            'last_login_at' => date('Y-m-d H:i:s'),
            'last_login_ip' => $ip,
        ]);
    }
}
