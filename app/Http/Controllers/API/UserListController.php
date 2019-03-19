<?php

namespace App\Http\Controllers\API;

use App\User;
use App\CreateList;
use App\File;
use App\MemberOfList;
use App\MemberToConfirmOnList; 
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserListController extends Controller
{

    public function viewList(Request $request){
        $token = $request->header('Authorization');
        $user = User::where('api_token',$token)->first();
        if($user){
            if(!$user->available){
                $createList = CreateList::where('id',$user->memberOfList->list_id)->first();
                if($user->memberOfList->ownerConfirm){
                    if($user->memberOfList->userConfirm){
                        $userConfirm = collect([]);
                        $userNoConfirm = collect([]);
                        $ownerNoConfirm = collect([]);
                        $filetest = collect([]);
                        $memberOfList = MemberOfList::where('list_id',$user->memberOfList->list_id)->get();
                        
                        
                        $conductor = User::where('id',$createList->conductor)->first();
                        foreach($memberOfList as $members){
                            $image = File::where('user_id',$members->user->id)->first();
                            if(($members->ownerConfirm == true) && ($members->userConfirm == true)){
                                $userConfirm->push($members->user);
                                $filetest->push($members->user->file);
                            }else if (($members->ownerConfirm == true) && ($members->userConfirm == false) ){
                                $userNoConfirm->push($members->user);
                                $filetest->push($members->user->file);
                                }else{
                                    $ownerNoConfirm->push($members->user);
                                    $filetest->push($members->user->file);
                                }    
                        }


                        if($user->id == $createList->user_id){
                            return response()->json([
                                'user'=> $user,
                                'conductor' => $conductor,
                                'ownerNotConfirmed' => $ownerNoConfirm,
                                'userConfirmed'=> $userConfirm,
                                'file'=> $filetest,
                                'userNotConfirmed' => $userNoConfirm,
                                'listData' => $createList,
                                'ownerConfirm' => true,
                                'userConfirm' => true, 
                                'owner' => true
                                
                             ],200);
                        }else{
                            return response()->json([
                                'user'=> $user,
                                'conductor' => $conductor,
                                'userConfirmed'=> $userConfirm,
                                'file'=> $filetest,
                                'listData' => $createList,
                                'ownerConfirm' => true,
                                'userConfirm' => true, 
                                'owner' => false
                             ],200);
                        }
                    }else{
                        return response()->json(['user'=> $user->name,
                    'message' => 'Aguardando confirmação do usuario para entrar lista',
                    'listData' => $createList,
                    'ownerConfirm' => true,
                    'userConfirm' => false, 
                    ],200);
                    }
                }else{
                    return response()->json(['user'=> $user->name,
                    'message' => 'Aguardando confirmação do dono da lista para entrar lista',
                    'listData' => $createList,
                    'ownerConfirm' => false,
                    'userConfirm' => true,
                    ],200);
                }
            }else{
                return response()->json(['user'=> $user->name,
                    'message' => 'Não esta em nenhuma lista',
                    ],200);
            }
        }else{
            return response()->json([
                'message' => 'Você foi deslogado...',
            ],200);
        }
    }
    
    public function userEnterOnList(Request $request){
        $token = $request->header('Authorization');
        $user = User::where('api_token',$token)->first();
        if($user){
            if($user->available){
                $list = CreateList::where('id',$request->list_id)->first();
                if($list){
                    $userList = new MemberOfList;
                    $userConfirm = new MemberToConfirmOnList;

                    $userList->user_id = $user->id;
                    $userList->list_id = $request->list_id;
                    $userList->ownerConfirm = false;
                    $userList->userConfirm = true;
                    $userList->save();
                    
                    $userConfirm->user_id = $user->id;
                    $userConfirm->list_id = $request->list_id;
                    $userConfirm->save();      
                    
                    $user->available = false;
                    $user->save();
                    
                    return response()->json([
                        'message' => 'Foi enviado ao administrador da lista seu id para confirmação, aguarde!',
                    ],200);
                }else{
                    return response()->json([
                        'message' => 'Lista não existe',
                    ],200);
                }
            }else{
                return response()->json([
                    'message' => 'Verifique se não está em nenhuma lista...'
                ],200);            
            }
        }else{
            return response()->json([
                'message' => 'Você foi deslogado...',
                'type' => '400'
            ],200);
        }
    }

    public function userConfirmToList(Request $request){
        $token = $request->header('Authorization');
        $user = User::where('api_token',$token)->first();
        
        if($user){
            $memberToConfirmOnList = MemberToConfirmOnList::where('user_id',$user->id)->first();
            if($memberToConfirmOnList){
                $memberToConfirmOnList->delete();
                $user->memberOfList->userConfirm = true;
                $user->memberOfList->save();
                return response()->json([
                    'message' => 'Usuario confirmou sua entrada na lista...'
                ],200);
            }else{
                return response()->json([
                    'message' => 'Usuario já esta na lista ou entrou em outra...',
            ],200);
            } 
        }else{
            return response()->json([
                'message' => 'Você foi deslogado...',
                'type' => '400'
              ],200);
        }
    }

    public function userQuitList(Request $request){
        $token = $request->header('Authorization');
        $user = User::where('api_token',$token)->first();
        if($user){
            $user->available = true;
            $user->save();
            $user->memberOfList->delete();
            return response()->json([
                    'message'=> 'saiu da lista...'
                ],200);
        }else{
            return response()->json([
                'message' => 'Você foi deslogado...',
                'type' => '400'
              ],200);
        }
    }

    public function userRecuseList(Request $request){
        $token = $request->header('Authorization');
        $user = User::where('api_token',$token)->first();
        if($user){
            $user->available = true;
            $user->save();
            $user->memberOfList->delete();
            return response()->json([
                    'message'=> 'saiu da lista...'
                ],200);
        }else{
            return response()->json([
                'message' => 'Você foi deslogado...',
                'type' => '400'
              ],200);
        }
    }
    

}
