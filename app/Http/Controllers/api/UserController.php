<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SelfUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = new User();

        if(!empty($request->search)){
            $users->where( function($or) use ($request){
                $or->orWhere( 'name', 'like', "%{$request->search}%" )
                    ->orWhere( 'last_name', 'like', "%{$request->search}%" )
                    ->orWhere( 'email', $request->search)
                    ->orWhere( 'phone', $request->search);
            });
        }
            
        $users =  $users->get();

        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        $request->merge( ['password' => bcrypt($request->password)] );

        $users = new User;

        try{
            $user = $users->create($request->all());
           // $user->companies()->attach($request->company); // $users->companies()->sync(1,3); //Isso remove todas companies atribuidas ao usuario e coloca somente 1 e 3 atribuidas a ele
            
           $companies[] = $request->company;
           $user->companies()->sync($companies);

            return response()->json($user);
        }catch(\Exception $e){
            return response()->json(['error' => $e->errorInfo[2]], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);

        return response()->json(!empty($user) ? $user : ['error' => 'Nenhum usuário encontrado.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function display($slug)
    {
        $user = User::where('slug', $slug)->get()->first();

        return response()->json(!empty($user) ? $user : ['error' => 'Nenhum corretor encontrado.']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, $id)
    {
        try{
            if($user = User::find($id)){
                if($user->role < Auth::user()->role){
                    return response()->json(['status' => 'Você não pode editar um usuário com cargo superior.'], 403);
                }

                if($user->id === Auth::user()->id){
                    return response()->json(['status' => 'Você não pode editar suas informações desta maneira'], 403);
                }

                $oldLogo = $user->logo;

                if($user->update($request->all())){

                    if(!empty($request->file('logo'))){
                        //dd($user->logo);
                        Storage::delete($oldLogo);

                        $user->logo = $request->file('logo')->store('user');
                        $user->save();
                    }

                    //return response()->json(['sucesso' => 'Usuário atualizado com sucesso!']);
                    return response()->json($user);
                }
            }else{
                return response()->json(['error' => 'Usuário não encontrado.']);
            }

        }catch(\Exception $e){
            return response()->json(['error' => $e->errorInfo[2]], 422);
        }
        
    }

    /**
     * Update the logged user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function self(SelfUserRequest $request)
    {
        try{
            if($user = User::find(Auth::user()->id)){
                $oldLogo = $user->logo;

                if($user->update($request->all())){
                    if(!empty($request->file('logo'))){
                        Storage::delete($oldLogo);

                        //$user->logo = Storage::url($request->file('logo')->store('user'));
                        $user->logo = $request->file('logo')->store('user');
                        $user->save();
                    }
                    
                    return response()->json($user);
                }
            }else{
                return response()->json(['error' => 'Você mesmo não foi encontrado, isso está estranho...']);
            }

        }catch(\Exception $e){
            dd($e);
            return response()->json(['error' => $e->errorInfo[2]], 422);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if($user = User::find($id)){
            $image = $user->image;

            if($user->delete()){
                Storage::delete($image);

                return response()->json(['success' => 'Usuário removido com sucesso!']);
            }
        }else{
            return response()->json(['error' => 'Usuário não encontrado!']);
        }
    }
}
