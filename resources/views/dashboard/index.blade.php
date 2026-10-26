<x-layouts.app>
    <div x-data="profileModal" x-on:close-modal="close">
        <div class="card mb-4 overflow-hidden">
            <div class="bg-primary" style="height: 0.375rem;"></div>
            <div class="card-body p-4 d-flex flex-column flex-md-row align-items-md-center gap-4">
                <x-avatar :user="auth()->user()" size="xl" />

                <div class="flex-grow-1 min-w-0">
                    <p class="small text-uppercase fw-semibold text-body-secondary mb-1">Minha conta</p>
                    <h1 class="h3 mb-1 text-truncate">{{ auth()->user()->name }}</h1>
                    <p class="mb-0 d-flex align-items-center gap-2 text-body-secondary">
                        @if ($selectedTitle)
                            <x-icon :name="$selectedTitle->icon" />
                            {{ $selectedTitle->name }}
                        @else
                            {{ auth()->user()->getRoleNames()->map(fn ($role) => \App\Enums\UserRole::labelFor($role))->join(', ') }}
                        @endif
                    </p>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <x-icon-button :href="route('achievements')" icon="award" label="Conquistas" />
                    <x-icon-button :href="route('titles')" icon="patch-check" label="Títulos" />
                    <x-icon-button x-on:click="openProfile" icon="pencil-square" label="Editar meus dados" variant="primary" />
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-xl-8">
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-4">
                        <x-stat-tile icon="check" color="success" label="Tarefas concluídas" :value="$tasksCompleted" />
                    </div>
                    <div class="col-6 col-md-4">
                        <x-stat-tile icon="bolt" color="info" label="XP nesta semana" :value="number_format($xpThisWeek)" />
                    </div>
                    <div class="col-6 col-md-4">
                        <x-stat-tile icon="medal" color="warning" label="Ranking" :value="'#'.$rankingPosition" />
                    </div>
                    <div class="col-6 col-md-4">
                        <x-stat-tile icon="star" color="warning" label="XP total" :value="number_format($totalXp)" />
                    </div>
                    <div class="col-6 col-md-4">
                        <x-stat-tile icon="trophy" color="primary" label="Conquistas" :value="$achievementsCount" />
                    </div>
                    <div class="col-6 col-md-4">
                        <x-stat-tile icon="rocket" color="info" label="Títulos" :value="$titlesCount" />
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header fw-semibold d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-card-checklist me-1" aria-hidden="true"></i> Minhas tarefas em aberto</span>
                        <a href="{{ route('boards.index') }}" class="small fw-normal">Ver quadros</a>
                    </div>

                    <ul class="list-group list-group-flush">
                        @forelse ($openTasks as $openTask)
                            <li class="list-group-item d-flex align-items-center justify-content-between gap-3">
                                <div class="min-w-0">
                                    <a href="{{ route('tasks.show', $openTask) }}" class="fw-medium text-body text-decoration-none d-block text-truncate">{{ $openTask->title }}</a>
                                    <span class="small text-body-secondary">{{ $openTask->board->name }} · {{ $openTask->category->name }}</span>
                                </div>

                                @if ($openTask->isOverdue())
                                    <x-badge color="danger"><x-icon name="alert" /> Atrasada · {{ $openTask->due_at->format('d/m') }}</x-badge>
                                @elseif ($openTask->due_at)
                                    <x-badge :color="$openTask->due_at->isToday() ? 'warning' : 'secondary'"><i class="bi bi-calendar-event" aria-hidden="true"></i> {{ $openTask->due_at->isToday() ? 'Hoje' : $openTask->due_at->format('d/m H:i') }}</x-badge>
                                @else
                                    <span class="small text-body-tertiary">Sem prazo</span>
                                @endif
                            </li>
                        @empty
                            <li class="list-group-item text-center text-body-secondary py-4">Você não tem tarefas em aberto. Que tal assumir uma no quadro?</li>
                        @endforelse
                    </ul>
                </div>

                <div class="card">
                    <div class="card-header bg-body fw-semibold"><i class="bi bi-activity me-1" aria-hidden="true"></i> Atividade recente</div>

                    <ul class="list-group list-group-flush">
                        @forelse ($recentActivity as $transaction)
                            <li class="list-group-item d-flex align-items-center justify-content-between gap-3">
                                <span class="small text-body-secondary">{{ $transaction->description }}</span>
                                <x-xp-badge :amount="$transaction->amount" />
                            </li>
                        @empty
                            <li class="list-group-item small text-body-secondary py-4 text-center">Nenhuma atividade ainda.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div class="col-xl-4 vstack gap-4">
                <x-level-progress :progress="$levelProgress" />

                <x-checkin-button :checked-in-today="$checkedInToday" :current-streak="$currentStreak" />

                <x-stat-tile icon="fire" color="danger" label="Dias seguidos de check-in" :value="$currentStreak" />
            </div>
        </div>

        <template x-if="isOpen">
            <x-modal title="Editar meus dados" size="lg">
                <form x-on:submit.prevent="save" class="vstack gap-3">
                    <div class="row g-3">
                        <div class="col-12"><x-form-input name="nome" label="Nome" autofocus /></div>
                        <div class="col-sm-6"><x-form-input name="cpf" label="CPF" placeholder="00000000000" /></div>
                        <div class="col-sm-6"><x-form-input name="rg" label="RG" /></div>
                        <div class="col-sm-6"><x-form-input name="nascimento" label="Nascimento" type="date" /></div>
                        <div class="col-sm-6">
                            <x-form-select name="sexo" label="Sexo" placeholder="Selecione">
                                <template x-for="gender in genders" :key="gender.value">
                                    <option x-bind:value="gender.value" x-text="gender.label"></option>
                                </template>
                            </x-form-select>
                        </div>
                        <div class="col-sm-6"><x-form-input name="email" label="E-mail de contato" type="email" /></div>
                        <div class="col-sm-6"><x-form-input name="telefone1" label="Telefone 1" /></div>
                        <div class="col-sm-6"><x-form-input name="telefone2" label="Telefone 2" /></div>

                        <div class="col-12">
                            <label for="foto" class="form-label fw-medium small mb-1">Foto</label>

                            <div class="d-flex align-items-center gap-3">
                                <template x-if="photoPreview()"><img x-bind:src="photoPreview()" alt="" class="avatar avatar-lg"></template>
                                <input type="file" id="foto" accept="image/*" x-on:change="pickFile($event, 'foto')" x-bind:class="{ 'is-invalid': firstError('foto') }" class="form-control">
                            </div>
                            <div class="text-danger small mt-1" x-text="firstError('foto')"></div>
                        </div>
                    </div>

                    <h3 class="h6 border-top pt-3 mb-0">Endereço</h3>

                    <div class="row g-3">
                        <div class="col-sm-6"><x-form-input name="cep" label="CEP" placeholder="00000-000" /></div>
                        <div class="col-sm-6"><x-form-input name="numero" label="Número" placeholder="Sem número" /></div>
                        <div class="col-12"><x-form-input name="logradouro" label="Logradouro" /></div>
                        <div class="col-sm-6"><x-form-input name="cidade" label="Cidade" /></div>
                        <div class="col-sm-6"><x-form-input name="estado" label="Estado (UF)" maxlength="2" placeholder="SP" class="text-uppercase" /></div>
                    </div>

                    <h3 class="h6 border-top pt-3 mb-0">Acesso</h3>

                    <div class="row g-3">
                        <div class="col-sm-6"><x-form-input name="login_email" label="E-mail de login" type="email" /></div>
                        <div class="col-sm-6 d-none d-sm-block"></div>
                        <div class="col-sm-6"><x-form-input name="password" label="Nova senha" type="password" autocomplete="new-password" placeholder="Deixe em branco para manter" /></div>
                        <div class="col-sm-6"><x-form-input name="password_confirmation" label="Confirmar nova senha" type="password" autocomplete="new-password" /></div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <x-button variant="secondary" x-on:click="close">Cancelar</x-button>
                        <x-button type="submit" x-bind:disabled="isSaving">Salvar alterações</x-button>
                    </div>
                </form>
            </x-modal>
        </template>
    </div>
</x-layouts.app>
