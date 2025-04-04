<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
//use App\Http\Controllers\AuthController;

class AuthController extends Controller
{

    // Proses login dan logout karyawan

    public function proseslogin(Request $request){
        if(Auth::guard('karyawan')->attempt(['nik' => $request->nik, 'password' => $request->password]))
        {
            //echo "Berhasil Login";
            return redirect('/dashboard');
        }else{
            return redirect('/')->with(['warning'=>'Nik / Password Salah']);
        }
    }

    public function proseslogout(){
        if(Auth::guard('karyawan')->check()){
            Auth::guard('karyawan')->logout();
            return redirect('/');
        }
    }

    // Proses login dan logout administrator

    public function prosesloginadmin(Request $request){
        if(Auth::guard('user')->attempt(['email' => $request->email, 'password' => $request->password]))
        {
            //echo "Berhasil Login";
            return redirect('/panel/dashboardadmin');
        }else{
            return redirect('/panel')->with(['warning'=>'Email / Password Salah']);
        }
    }

    public function proseslogoutadmin(){
        if(Auth::guard('user')->check()){
            Auth::guard('user')->logout();
            return redirect('/panel');
        }
    }

    // Proses login dan logout karu

    public function prosesloginkaru(Request $request){
        if(Auth::guard('karu')->attempt(['username' => $request->username, 'password' => $request->password]))
        {
            // echo "Berhasil Login";
            return redirect('/panelkaru/dashboardkaru');
        }else{
            return redirect('/panelkaru')->with(['warning'=>'Username / Password Salah']);
        }
    }

    public function proseslogoutkaru(){
        if(Auth::guard('karu')->check()){
            Auth::guard('karu')->logout();
            return redirect('/panelkaru');
        }
    }
}
