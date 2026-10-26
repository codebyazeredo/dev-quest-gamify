<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Gender;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PersonRequest;
use App\Http\Requests\PaginatedListRequest;
use App\Http\Resources\PersonResource;
use App\Models\Person;
use App\Services\Admin\PersonService;
use Illuminate\Http\JsonResponse;

class PersonController extends Controller
{
    public function __construct(private PersonService $people) {}

    public function index(PaginatedListRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Person::class);

        return PersonResource::collection($this->people->paginate($request->perPage()))
            ->additional(['genders' => $this->genderOptions()])
            ->response();
    }

    public function store(PersonRequest $request): JsonResponse
    {
        $this->authorize('create', Person::class);

        $person = $this->people->create($request->safe()->except('foto'), $request->file('foto'));

        return PersonResource::make($person)
            ->additional(['message' => "Pessoa \"{$person->nome}\" criada."])
            ->response()
            ->setStatusCode(201);
    }

    public function update(PersonRequest $request, Person $person): JsonResponse
    {
        $this->authorize('update', $person);

        $person = $this->people->update($person, $request->safe()->except('foto'), $request->file('foto'));

        return PersonResource::make($person)
            ->additional(['message' => "Pessoa \"{$person->nome}\" atualizada."])
            ->response();
    }

    public function destroy(Person $person): JsonResponse
    {
        $this->authorize('delete', $person);

        $this->people->delete($person);

        return response()->json(['message' => "Pessoa \"{$person->nome}\" excluída."]);
    }

    /** @return array<int, array{value: int, label: string}> */
    private function genderOptions(): array
    {
        return array_map(fn (Gender $gender) => ['value' => $gender->value, 'label' => $gender->label()], Gender::cases());
    }
}
