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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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

        if (!empty($request->search)) {
            $users->where(function ($or) use ($request) {
                $or->orWhere('name', 'like', "%{$request->search}%")
                    ->orWhere('last_name', 'like', "%{$request->search}%")
                    ->orWhere('email', $request->search)
                    ->orWhere('phone', $request->search);
            });
        }

        if (!empty($request->start_date)) {
            $users->whereDate('created_at', ">=", $request->start_date);
        }

        if (!empty($request->end_date)) {
            $users->whereDate('created_at', "<=", $request->end_date);
        }

        // $users = $users->withCount('sums')->orderBy('sums_count', 'desc')->get();

        // Filtra também as "sums" através do callback.
        $users = $users->withCount([
            'sums' => function ($query) use ($request) {
                if (!empty($request->start_date)) {
                    $query->whereDate('created_at', '>=', $request->start_date);
                }
                if (!empty($request->end_date)) {
                    $query->whereDate('created_at', '<=', $request->end_date);
                }
            }
        ])
            ->orderBy('sums_count', 'desc')
            ->get();

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
        $request->merge(['password' => bcrypt($request->password)]);

        $users = new User;

        try {
            $user = $users->create($request->all());

            $this->sendToSecondaryBackend($request);

            return response()->json(['success' => 'Cadastro criado com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->errorInfo[2]], 422);
        }
    }

    /**
     * Envia os dados do usuário para um backend secundário via HTTP POST
     *
     * @param array $userData
     */
    private function sendToSecondaryBackend(StoreUserRequest $request)
    {
        try {
            // Token de serviço
            $serviceToken = env('LANCE_API_TOKEN');
            $partnerBackend = env('LANCE_API_URL');

            $secondaryApiUrl = $partnerBackend . 'gateway/savePartner';

            $response = Http::withToken($serviceToken)->post($secondaryApiUrl, $request->all());

            return $response->json();
        } catch (\Exception $e) {
            throw $e;
            //Log::error('Erro ao enviar dados para backend secundário: ' . $e->getMessage());
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

        if ($user->logo && env('FILESYSTEM_DRIVER') === "s3") {
            $user->logo = Storage::temporaryUrl(
                $user->logo,
                now()->addMinutes(5)
            );
        }

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

        if ($user) {
            if ($user->logo) {
                $user->logo = asset('storage/' . $user->logo); //Storage::url($user->logo);
            }

            if ($user->logo && env('FILESYSTEM_DRIVER') === "s3") {
                $user->logo = Storage::temporaryUrl(
                    $user->logo,
                    now()->addMinutes(5)
                );
            }
        }

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
        try {
            if ($user = User::find($id)) {
                if ($user->role < Auth::user()->role) {
                    return response()->json(['status' => 'Você não pode editar um usuário com cargo superior.'], 403);
                }

                if ($user->id === Auth::user()->id) {
                    return response()->json(['status' => 'Você não pode editar suas informações desta maneira'], 403);
                }

                $oldLogo = $user->logo;

                $request->merge(['password' => bcrypt($request->password)]);

                if ($user->update($request->all())) {

                    if (!empty($request->file('logo'))) {
                        //dd($user->logo);
                        Storage::delete($oldLogo);

                        $user->logo = $request->file('logo')->store('user');
                        $user->save();
                    }

                    //return response()->json(['sucesso' => 'Usuário atualizado com sucesso!']);
                    return response()->json($user);
                }
            } else {
                return response()->json(['error' => 'Usuário não encontrado.']);
            }
        } catch (\Exception $e) {
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
        try {
            if ($user = User::find(Auth::user()->id)) {
                $oldLogo = $user->logo;

                if ($user->update($request->all())) {
                    if (!empty($request->file('logo'))) {
                        //dd(Storage::disk());
                        Storage::delete($oldLogo);

                        //$user->logo = Storage::url($request->file('logo')->store('user'));
                        $user->logo = $request->file('logo')->store('user');
                        $user->save();
                    }

                    return response()->json($user);
                }
            } else {
                return response()->json(['error' => 'Você mesmo não foi encontrado, isso está estranho...']);
            }
        } catch (\Exception $e) {
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
        if ($user = User::find($id)) {
            $image = $user->image;

            if ($user->delete()) {
                Storage::delete($image);

                return response()->json(['success' => 'Usuário removido com sucesso!']);
            }
        } else {
            return response()->json(['error' => 'Usuário não encontrado!']);
        }
    }
}
