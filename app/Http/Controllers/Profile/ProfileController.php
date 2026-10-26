<?php

namespace App\Http\Controllers\Profile;

use App\Enums\Gender;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\ProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(private ProfileService $profile) {}

    public function show(Request $request): JsonResponse
    {
        return ProfileResource::make($this->profile->personOf($request->user()))
            ->additional(['genders' => $this->genderOptions()])
            ->response();
    }

    public function update(ProfileRequest $request): JsonResponse
    {
        $person = $this->profile->update($request->user(), $request->safe()->except('foto'), $request->file('foto'));

        return ProfileResource::make($person)
            ->additional(['message' => 'Seus dados foram atualizados com sucesso.'])
            ->response();
    }

    /** @return array<int, array{value: int, label: string}> */
    private function genderOptions(): array
    {
        return array_map(fn (Gender $gender) => ['value' => $gender->value, 'label' => $gender->label()], Gender::cases());
    }
}
