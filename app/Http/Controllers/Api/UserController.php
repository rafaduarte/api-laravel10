<?php

namespace App\Http\Controllers\Api;

use App\DTO\Users\CreateUserDTO;
use App\DTO\Users\EditUserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreUserRequest;
use App\Http\Requests\Api\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;

class UserController extends Controller
{
    public function __construct(private UserRepository $userRepository)
    {

    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $users = $this->userRepository->getPaginate($request->filter ?? '');
        $users = $this->userRepository->getPaginate(
            totalPerPage: $rquest->total_per_page ?? 15,
            page: $request->page ?? 1,
            filter: $request->get('filter', ''),
        );

        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        // $user = $this->userRepository->createNew(new CreateUserDTO(... $request->only('name', 'email', 'password')));
        $user = $this->userRepository->createNew(new CreateUserDTO(... $request->validated()));

        return new UserResource($user);
    }
    /*
    public function store(Request $request)
    {
        $user = $this->userRepository->createNew(
            $request->name,
            $request->email,
            $request->password,
        );
        return new UserResource($user);
    }
*/
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (!$user = $this->userRepository->findById($id)) {
            return response()->json(['message' => 'user not found'], HttpResponse::HTTP_NOT_FOUND);
        }
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        $response = $this->userRepository->update(new EditUserDTO(...[$id, ... $request->validated()]));
        if(!$response) {
            return response()->json(['message' => 'user not found'], HttpResponse::HTTP_NOT_FOUND);
        }
        return response()->json(['message' => 'usuário atualizado com sucesso']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        if(!$this->userRepository->delete($id)) {
            return response()->json(['message' => 'user not found'], HttpResponse::HTTP_NOT_FOUND);
        }
        return response()->json([], HttpResponse::HTTP_NO_CONTENT);
    }
}
