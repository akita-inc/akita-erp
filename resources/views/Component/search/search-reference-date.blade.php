<div class="col-md-6 col-sm-12 lh-38">
    <div class="form-check-inline">
        <label class="form-check-label">
            <input type="radio" v-model="{{ $field_radio }}" class="form-check-input" value="0">{{trans("suppliers.list.search.radio-all")}}
        </label>
    </div>
    <div class="form-check-inline">
        <label class="form-check-label">
            <input type="radio" v-model="{{ $field_radio }}" v-on:click="setDefault()" class="form-check-input" value="1" checked>{{trans("suppliers.list.search.radio-reference-date")}}
        </label>
    </div>
</div>

<div class="col-md-6 col-sm-12">
    <date-picker :lang='lang' id="reference_date" :format="format_date" value-type="format" v-model="{{ $field_date }}"></date-picker>
</div>
