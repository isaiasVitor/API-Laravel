<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\User;
use App\Address;
use App\File;
use Validator;
class AuthController extends Controller
{
  private $apiToken;
  public function __construct()
  {
    // Unique Token
    $this->apiToken = uniqid(base64_encode(str_random(60)));
  }
  /**
   * Client Login
   */
  public function postLogin(Request $request){
    // Validations
    $rules = [
      'email'=>'required|email',
      'password'=>'required|min:8'
    ];
    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
      // Validation failed
      return response()->json([
        'message' => $validator->messages(),
      ]);
    } else {
      // Fetch User
      $user = User::where('email',$request->email)->first();
      if($user) {
        // Verify the password
        if( password_verify($request->password, $user->password) ) {
          // Update Token
          $postArray = ['api_token' => $this->apiToken];
          $login = User::where('email',$request->email)->update($postArray);
          $user = User::where('email',$request->email)->first();
          if($login) {
            return response()->json([
              'access_token'=>$this->apiToken,
              'user'=> $user
            ], 201);
          }
        } else {
          return response()->json([
            'message' => 'Senha incorreta',
          ],400);
        }
      }else{
        return response()->json([
          'message' => 'Usuario não encontrado',
        ],400);
      }
    }
  }
  
  /**
   * Register
   */
  public function postRegister(Request $request){
    // Validations
    $rules = [
      'name'     => 'required|min:3',
      'email'    => 'required|unique:users,email',
      'password' => 'required|min:8',
      'cpf' => 'required|unique:users,cpf',
      'rg' => 'required|unique:users,rg',
      'gender' => 'required',
      'birthday' => 'required'
    ];
    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
      // Validation failed
      return response()->json([
        'message' => $validator->messages(),
      ],400);
    } else {
      $user = new User;
      
      $user->name        = $request->name;  
      $user->email       = $request->email;  
      $user->password   = bcrypt($request->password);  
      $user->cpf        = $request->cpf;  
      $user->rg         = $request->rg;  
      $user->gender     = $request->gender;
      $user->birthday   = $request->birthday;  
      $user->api_token  = $this->apiToken;
      $user->save();
      
      $address = new Address;
      $address->user_id = $user->id;
      $address->save();

      $file = new File;
      $file->name = 'perfilphoto.png';
      $file->size='4096';
      $file->user_id = $user->id;
      $file->save();
     
      if($user) {
        $user = User::where('api_token', $this->apiToken)->first();
        return response()->json([
          'user'         => $user,
          'access_token' => $this->apiToken,
        ],200);
      } else {
        return response()->json([
          'message' => 'Registro falhou, tente novamente mais tarde...',
        ],400);
      }
    }
  }
  /**
   * Logout
   */
  public function postLogout(Request $request){
    $token = $request->header('Authorization');
    $user = User::where('api_token',$token)->first();
    if($user) {
      $postArray = ['api_token' => null];
      $logout = User::where('id',$user->id)->update($postArray);
      if($logout) {
        return response()->json([
          'message' => 'Usuario Deslogado com Sucesso...',
        ]);
      }
    } else {
      return response()->json([
        'message' => 'Usuario não encontrado...',
      ]);
    }
  }

  public function isLogin(Request $request){
    $token = $request->header('Authorization');
    $user = User::where('api_token',$token)->first();
    if($user){
      return response()->json([
        'login'=> true
      ], 200);
    }else{
      return response()->json([
        'login' => false
      ],200);
    }
  }
}