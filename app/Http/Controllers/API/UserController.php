<?php

namespace App\Http\Controllers\API;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Address;
use App\File;
use Validator;
class UserController extends Controller
{
    public function viewEditPerfilUser(Request $request)
    {
        $token = $request->header('Authorization');
        $user = User::where('api_token',$token)->first();
        if($user) {
            return response()->json([
                'user'=> $user,
                'address' => $user->address
            ],201);
        } else {
            return response()->json([
                'message' => 'não encontrou o usuario...',
                'type' => '400'
                
          ],400);
        }
    }

    public function postEditPerfilUser(Request $request)
    {

        $rules = [
            'name'     => 'required|min:3',
            'gender' => 'required',
            'birthday' => 'required',
            'phone_first' => 'required'

          ];
        $validator = Validator::make($request->all(), $rules);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            // Validation failed
            return response()->json([
                'message' => $validator->messages(),
            ],400);
        } else {
            $token = $request->header('Authorization');
            $user = User::where('api_token',$token)->first();
            if($user){
                $user->name                     = $request->name;
                $user->cpf                      = $request->cpf;
                $user->rg                       = $request->rg;
                $user->gender                   = $request->gender;
                $user->birthday                 = $request->birthday;
                $user->address->zipcode         = $request->zipcode;            
                $user->address->country         = $request->country;
                $user->address->state           = $request->state;
                $user->address->city            = $request->city;
                $user->address->neighborhood    = $request->neighborhood;
                $user->address->street          = $request->street;
                $user->address->number          = $request->number;
                $user->address->complement      = $request->complement;
                $user->address->phone_first     = $request->phone_first;
                $user->address->phone_secondary = $request->phone_secondary;
                $user->save();
                $user->address->save();
                return response()->json(["user"=>$user,$user->address],200);
            }else{
                return response()->json(['message'=> 'Você foi deslogado...',
                                        'type' => '400'
                                        ],400);
            }
        }
    }


    public function saveImage(Request $request){
        $token = $request->header('Authorization');
        $user = User::where('api_token',$token)->first();

        $data=['result'=> false];
        $target_path = public_path('storage\perfilPhoto\\'. $user->id.'.jpg');
     
        if(isset($request['file'])){
            $imagedata = $request['file'];
            $imagedata = str_replace('data:image/jpeg;base64,', '', $imagedata);
            $imagedata = str_replace('data:image/jpg;base64,','', $imagedata);
            $imagedata = str_replace(' ', '+', $imagedata);
            $imagedata = base64_decode($imagedata);

            //file_put_contents($target_path,$imagedata);
            $img= Image::make($imagedata)->resize(960,960)->save($target_path);
            
            $file = File::where('user_id',$user->id)->first();
            $file->name = $user->id.'.jpg';
            $file->size = $img->filesize();
            $file->user_id = $user->id;
            $file->save();
            $data['result'] = 'sucess';
            $data['image_url'] = 'http://18.228.137.119/API-Laravel/public/storage/perfilPhoto/'.$user->id.'.jpg';
     
        
            return response()->json([
                'file'=> $data,
                'size'=> $file->size
            ],200);

        }else{
            $data['result'] = 'fail';
            $data['image_url'] = '';
            return response()->json([
                'file'=> $data,
            ]);
        }

    }

   
    public function viewImagePerfil(Request $request){
        $token = $request->header('Authorization');
        $user = User::where('api_token',$token)->first();
        if($user){
            $file = File::where('user_id',$user->id)->first();
            if($file){
                $fileName = $file->name;
                
                return response()->json([
                    'file' => 'http://18.228.137.119/API-Laravel/public/storage/perfilPhoto/'.$fileName
                ],200);
            }
        }else{
            return response()->json([
                'message' => 'Deslogado...'
            ],400);
        }
    }

    public function removeImagePerfil(Request $request){
        $token = $request->header('Authorization');
        $user = User::where('api_token',$token)->first();
        if($user){
            $file = File::where('user_id',$user->id)->first();
            if($file){
                $file->name="perfilphoto.png";
                $file->size='4096';
                $file->save();
                return response()->json([
                    'file' => $file
                ],200);
            }
        }else{
            return response()->json([
                'message' => 'Deslogado...'
            ],400);
        }
    }

}
