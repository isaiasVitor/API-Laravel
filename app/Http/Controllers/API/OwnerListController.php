<?php

namespace App\Http\Controllers\API;

use App\User;
use App\CreateList;
use App\MemberOfList;
use App\MemberToConfirmOnList; 
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class OwnerListController extends Controller
{

    public function createList(Request $request){
        $token = $request->header('Authorization');
        $user = User::where('api_token',$token)->first();

        if($user){
            if($user->available){
           
                $createlist = new CreateList;
                $createlist->fill($request->all());
                $createlist->user_id = $user->id;
                $createlist->conductor = $user->id;
            
                $createlist->save();

                $user->available = false;
                $user->save();

                $memberOfList = new MemberOfList;
                $memberOfList->user_id = $user->id;
                $memberOfList->list_id = $createlist->id;
                $memberOfList->ownerConfirm = true;
                $memberOfList->userConfirm = true;
                $memberOfList->save();

                return response()->json([
                    'Lista criada com sucesso...'
                ],201);
            }else{
                return response()->json([
                    'Já esta em uma lista...'
                ],400);
            }
        }else{
            return response()->json([
                'message' => 'Você foi deslogado...',
                'type' => '400'
              ],400);
        }
    }

    public function selectConductor(Request $request){
        $token = $request->header('Authorization');
        $user = User::where('api_token',$token)->first();
       
        if($user){
            $createList = CreateList::where('user_id',$user->id)->first();
            if($createList){
                $conductor = User::where('cpf',$request->conductorCPF)->first();
                if($conductor){
                    $createList->conductor = $conductor->id;
                    $createList->save();
                    return response()->json([
                        'message' => 'Motorista: '. $conductor->name
                    ],200);
                }else{
                    return response()->json([
                        'message' => 'Usuario não existe'
                    ],200);
                }
            }else{
                return response()->json([
                    'message'=>'Usuario Não encontrado'
                ],200);
            }
        }else{
            return response()->json([
                 'message'=>'Usuario Não encontrado'   
            ],200);
        }
    }

    
    public function editList(Request $request){
        $token = $request->header('Authorization');
        $user = User::where('api_token',$token)->first();
        $createList = CreateList::where('user_id',$user->id)->first();
        if($user){
            if($createList){
                $user->createList->name = $request->name;
                $user->createList->description = $request->description;
                $user->createList->save();
            }else{
                return response()->json([
                    'message' => 'Não é o dono da lista...',
                  ],400);
            }
        }else{
            return response()->json([
                'message' => 'Você foi deslogado...',
                'type' => '400'
              ],400);
        }
    }

    
    public function deleteList(Request $request){
        $token = $request->header('Authorization');
        $user = User::where('api_token',$token)->first();
        if($user){
            $createlist = CreateList::where('user_id', $user->id)->first();
            if($createlist){
                $membersList = MemberOfList::where('list_id',$user->memberOfList->list_id)->get();
                
                foreach ($membersList as $memberList) {
                    $memberList->user->available = true;
                    $memberList->user->save();
                }
                                
                $user->createList->delete();
                return response()->json([
                    'message' => 'Lista apagada com sucesso!!!',
                  ],200);
            }else{
                return response()->json([
                    'message' => 'Não é o dono da lista...',
                  ],400);
            }
        }else{
            return response()->json([
                'message' => 'Você foi deslogado...',
                'type' => '400'
            ],400);
        }
    }

    public function inviteMemberToList(Request $request){
        $token = $request->header('Authorization');
        $user = User::where('api_token',$token)->first();

        if($user){
                if($user->createList->user_id == $user->id){      
                    
                    $searchUser = User::where('cpf',$request->userCPF)->first();
                    
                    if ($searchUser){
                        if($searchUser->available){
                            $list_id = $user->createList->id;
                            $userList = new MemberOfList;
                            $userConfirm = new MemberToConfirmOnList;
                            
                            $userConfirm->user_id = $searchUser->id;
                            $userConfirm->list_id = $list_id;
                            $userConfirm->save();

                            $searchUser->available = false;
                            $searchUser->save();
                            
                            $userList->user_id = $searchUser->id;
                            $userList->list_id = $list_id;
                            $userList->ownerConfirm = true;
                            $userList->userConfirm = false;
                            $userList->save();
                            
                            return response()->json([
                                'message' => 'usuario foi convidado'
                            ],200); 
                        }else{
                            return response()->json([
                                'message' => 'Usuario ja pertence a uma lista'
                            ],200);
                        }
                    }else{
                        return response()->json([$user->name,
                            'message' => 'Usuario não encontrado, Por favor tente novamente',
                        ],400);    
                    }
                }else{
                    return response()->json([$user->name,
                            'message' => 'Não é dono da lista',
                        ],400);
                }
        }else{
            return response()->json([
                'message' => 'Você foi deslogado...',
                'type' => '400'
            ],400);
        }
    }

    public function ownerConfirmToList(Request $request){
        $token = $request->header('Authorization');
        $user = User::where('api_token',$token)->first();
        $createlist = CreateList::where('user_id', $user->id)->first();
        if($user){
            if($createlist){
                $memberToConfirm = MemberToConfirmOnList::where('user_id',$request->user_id)->first();
                if($memberToConfirm){             
                    $memberList = MemberOfList::where('user_id',$request->user_id)->first();
                    if($memberList->ownerConfirm == false){
                        $memberList->ownerConfirm  = true;
                        $memberToConfirm->delete(); 
                        $memberList->save();
                        return response()->json([
                            'message' => 'usuario incluido com sucesso...'
                        ],200);
                    }else{
                        return response()->json([
                            'message' => 'Aguardando confirmação do usuario'
                        ],200);
                    }
                }else{
                    return response()->json([
                        'message' => 'Usuario já confirmado ou não pertence a lista',
                      ],400);
                }
            }else{
                return response()->json([
                    'message' => 'Não é administrador da Lista...',
                  ],400);
            }
        }else{
            return response()->json([
                'message' => 'Você foi deslogado...',
                'type' => '400'
              ],400);
        }
    }

     public function ownerRemoveMember(Request $request){
         $token = $request->header('Authorization');
         $user = User::where('api_token',$token)->first();
         $createlist = CreateList::where('user_id', $user->id)->first();
         if($user){
             if($createlist){
                 $memberList = MemberOfList::where('user_id',$request->user_id)->first();
                 if($memberList){
                     $memberList->user->available = true;
                     $memberList->user->save();
                     $memberToConfirm = MemberToConfirmOnList::where('user_id',$request->user_id)->first();
                     if($memberToConfirm){
                         $memberToConfirm->delete();
                     }

                     $memberList->delete();
                     
                     return response()->json([
                         'message' => 'Usuario removido com sucesso',
                     ],200);
                 }else{
                     return response()->json([
                         'message' => 'usuario não pertence a lista',
                       ],400);  
                 }
             }else{
                 return response()->json([
                     'message' => 'Não é administrador da Lista...',
                   ],400);
             }
         }else{
             return response()->json([
                'message' => 'Você foi deslogado...',
                'type' => '400'
             ],400);
         }
     }
}
