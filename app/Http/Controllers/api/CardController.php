<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SelfUserRequest;
use App\Http\Requests\StoreSumRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Manager\DataTransferObject\Sum\SumDTO;
use App\Manager\Sum\CreateSum;
use App\Models\Card;
use App\Models\Sum;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $cards = new Card();
            
        $cards =  $cards->get();

        return response()->json($cards);
    }

    /**
     * @throws Exception|Throwable
     */
    public function store(StoreSumRequest $request): array {
        (new CreateSum(SumDTO::transform($request->validated())))
            ->execute();

        return [
            'success' => true
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Quota  $quota
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $sum = Sum::find($id);

        return response()->json(!empty($sum) ? $sum : ['error' => 'Nenhuma soma encontrada encontrada.']);
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
