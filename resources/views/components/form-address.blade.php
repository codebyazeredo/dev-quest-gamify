<h3 class="h6 border-top pt-3 mb-0">Endereço</h3>

<div class="row g-3">
    <div class="col-sm-6">
        <x-form-select name="country_id" label="País">
            <template x-for="country in extra.countries ?? []" :key="country.id">
                <option x-bind:value="country.id" x-text="country.name"></option>
            </template>
        </x-form-select>
    </div>
    <div class="col-sm-6"><x-form-input name="cep" label="CEP" placeholder="00000-000" /></div>
    <div class="col-sm-8"><x-form-input name="logradouro" label="Logradouro" /></div>
    <div class="col-sm-4"><x-form-input name="numero" label="Número" placeholder="Sem número" /></div>
    <div class="col-sm-6"><x-form-input name="complemento" label="Complemento" /></div>
    <div class="col-sm-6"><x-form-input name="bairro" label="Bairro" /></div>
    <div class="col-sm-8"><x-form-input name="cidade" label="Cidade" /></div>
    <div class="col-sm-4"><x-form-input name="estado" label="Estado (UF)" maxlength="2" placeholder="SP" class="text-uppercase" /></div>
    <div class="col-12"><x-form-input name="referencia" label="Ponto de referência" /></div>
</div>
