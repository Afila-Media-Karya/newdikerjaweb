<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\LoginRequest;
use Auth;
use Session;
use DB;
use App\Models\User;
class AuthController extends BaseController
{
    
    public function view_login(){
        return view('auth.login');
    }

    public function login(LoginRequest $request){
         $credentials = $request->getCredentials();

         $value_session_jabatan = '';
         $value_session_jabatan_kode = '';
         $value_session_nama = '';
         $value_session_nama_jabatan = '';
         $value_session_foto = '';
         $value_session_satuan_kerja = '';
         $value_session_tipe_pegawai = '';

        $user = DB::table('users')
        ->join('tb_pegawai','users.id_pegawai','=','tb_pegawai.id')
        ->join('tb_satuan_kerja','tb_pegawai.id_satuan_kerja','=','tb_satuan_kerja.id')
        ->join('tb_jabatan','tb_jabatan.id_pegawai','tb_pegawai.id')
        ->join('tb_unit_kerja','tb_jabatan.id_unit_kerja','=','tb_unit_kerja.id')
        ->join('tb_master_jabatan','tb_jabatan.id_master_jabatan','tb_master_jabatan.id')
        ->select('tb_pegawai.id','tb_pegawai.uuid','tb_pegawai.nip','tb_pegawai.nama','tb_master_jabatan.nama_jabatan','tb_jabatan.id as id_jabatan','tb_jabatan.status','tb_pegawai.nama as nama_pegawai','tb_pegawai.foto','tb_satuan_kerja.nama_satuan_kerja','tb_unit_kerja.nama_unit_kerja','tb_pegawai.tipe_pegawai')
        ->where('users.username',$request->username)
        ->get();

         foreach ($user as $userData) {
            if ($userData->status == 'definitif') {
                // Jika status definitif ditemukan, langsung set nilainya dan hentikan iterasi
                $value_session_jabatan = 'definitif';
                $value_session_jabatan_kode = $userData->id_jabatan;
                $value_session_nama = $userData->nama_pegawai;
                $value_session_nama_jabatan = $userData->nama_jabatan;
                $value_session_satuan_kerja = $userData->nama_unit_kerja;
                $userData->foto !== null && $userData->foto !== ""   ? $value_session_foto = url('/profil/get-image-profil') : $value_session_foto = '/admin/assets/media/avatars/blank.png';
                $value_session_tipe_pegawai = $userData->tipe_pegawai;
                
                
                break;
            } else{
                if ($value_session_jabatan !== 'definitif') {
                    $value_session_jabatan = $userData->status;
                    $value_session_jabatan_kode = $userData->id_jabatan;
                    $value_session_nama = $userData->nama_pegawai;
                    $value_session_nama_jabatan = $userData->status.' '.$userData->nama_jabatan;
                    $value_session_foto = $userData->foto;
                    $value_session_satuan_kerja = $userData->nama_unit_kerja;
                    $value_session_tipe_pegawai = $userData->tipe_pegawai;
                }
            }
            
        }
        
        if (Auth::attempt($credentials)) {

            
            if (count($user) == 0) {
                Auth::logout();
                return redirect()->back()->withErrors(['error' => 'Jabatan tidak di temukan, Mohon hubungi admin opd']); 
            }

            $user = Auth::user();
            Session::put('tahun_penganggaran', date('Y'));
            Session::put('session_jabatan',$value_session_jabatan);
            Session::put('session_jabatan_kode',$value_session_jabatan_kode);
            Session::put('session_nama',$value_session_nama);
            Session::put('session_nama_jabatan',$value_session_nama_jabatan);
            Session::put('session_foto',$value_session_foto);
            Session::put('session_satuan_kerja',$value_session_satuan_kerja);
            Session::put('session_tipe_pegawai',$value_session_tipe_pegawai);
            return $this->authenticated($request, 'pegawai',$user);
        }

        // Cek autentikasi untuk guard "admin"
        if (Auth::guard('administrator')->attempt($credentials)) {
            $user = Auth::guard('administrator')->user();
            Session::put('tahun_penganggaran', date('Y'));
             $value_session_foto = '/admin/assets/media/avatars/blank.png';
             Session::put('session_foto',$value_session_foto);
             Session::put('session_nama','Admin');
             Session::put('session_satuan_kerja','Badan Kepegawaian & Pengembangan Sumber Daya Manusia');
            return $this->authenticated($request, 'administrator',$user);
        }

        

        // Jika autentikasi gagal untuk kedua guard, kembali ke halaman login dengan pesan error
        return redirect()->back()->withErrors(['error' => 'Autentikasi gagal. Silakan cek kembali username dan password Anda.']);
    }

    private function authenticated(Request $request, $guard, $user)
    {
        if ($guard == 'pegawai') {
            return redirect()->intended('dashboard-pegawai');
            
        } elseif ($guard == 'administrator') {
            if ($user->role == '1') {
                return redirect()->intended('dashboard-super-admin');    
            }elseif ($user->role == '2') {
                return redirect()->intended('dashboard-kabupaten');
            }else{
                return redirect()->intended('dashboard-keuangan');
            }
            
        }
    }

    public function logout(Request $request){
        Session::flush();
        if (Auth::guard('administrator')->check()) {
            Auth::guard('administrator')->logout();
            return redirect('login');
        }

        if (Auth::check()) {
            Auth::logout();
            return redirect('login');
        }
        
        return redirect('login');
    }

    

}
