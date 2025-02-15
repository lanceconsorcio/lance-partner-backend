<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SelfUserRequest;
use App\Http\Requests\StoreAccessRequest;
use App\Http\Requests\StoreSumRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Manager\DataTransferObject\Sum\SumDTO;
use App\Manager\Sum\CreateSum;
use App\Models\Access;
use App\Models\Sum;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AccessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $accesses = new Access();

        if(!empty($request->search)){
            $accesses->where( function($or) use ($request){
                $or->orWhere( 'name', 'like', "%{$request->search}%" )
                    ->orWhere( 'last_name', 'like', "%{$request->search}%" )
                    ->orWhere( 'email', $request->search)
                    ->orWhere( 'phone', $request->search);
            });
        }
            
        $accesses =  $accesses->with('cards')->get();

        return response()->json($accesses);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAccessRequest $request)
    {
        $access = new Access();

        try{
            if($access = $access->create($request->all())){

                return response()->json($access);
            }
        }catch(\Exception $e){
            return response()->json(['error' => $e->errorInfo[2]], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Quota  $quota
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $access = Access::find($id);

        return response()->json(!empty($access) ? $access : ['error' => 'Nenhum acesso encontrada encontrada.']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function access_count(Request $request)
    {
        $accesses = new Access();

        if(!empty($request->start_date)){
            $accesses->whereDate( 'created_at', ">=", $request->start_date);
        }

        if(!empty($request->end_date)){
            $accesses->whereDate( 'created_at', "<=", $request->end_date);
        }

        $access_count = $accesses->get()->count();

        return response()->json(['count' => $access_count]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Quota  $quota
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //return response()->json($id);
        try{
            if($sum = Sum::find($id)){

                $newSumData = $request->all();
                
                if($sum->update($newSumData)){
                    return response()->json($sum);
                }
            }else{
                return response()->json(['error' => 'Soma não encontrada.']);
            }

        }catch(\Exception $e){
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
        if($sum = Sum::find($id)){
            if($sum->delete()){
                return response()->json(['success' => 'Soma removida com sucesso!']);
            }
        }else{
            return response()->json(['error' => 'Soma não encontrada!']);
        }
    }
}
