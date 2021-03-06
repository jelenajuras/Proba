@extends('layouts.admin')

@section('title', 'Ispravi podatke naručitelja')
<link rel="stylesheet" href="{{ URL::asset('css/create.css') }}"/>
@section('content')
<div class="page-header">
  <h2>Ispravi klijenta</h2>
</div>
<div class="forma col-xs-10 col-xs-offset-1 col-sm-10 col-sm-offset-1 col-md-10 col-md-offset-1 col-lg-6 col-lg-offset-3">
<div class="panel-body">
		<form accept-charset="UTF-8" role="form" method="post" action="{{ route('admin.customers.update', $customer->id) }}">
		<fieldset>
			<div class="form-group {{ ($errors->has('naziv')) ? 'has-error' : '' }}">
			<label>Naziv firme</label>
				<input class="form-control" name="naziv" type="text" value="{{ $customer->naziv}}"/>
				{!! ($errors->has('naziv') ? $errors->first('naziv', '<p class="text-danger">:message</p>') : '') !!}
			</div>
			<div class="form-group {{ ($errors->has('adresa')) ? 'has-error' : '' }}">
			<label>Adresa</label>
				<input class="form-control" name="adresa" type="text" value="{{ $customer->adresa }} "/>
				{!! ($errors->has('adresa') ? $errors->first('adresa', '<p class="text-danger">:message</p>') : '') !!}
			</div>
			<div class="form-group  {{ ($errors->has('grad')) ? 'has-error' : '' }}">
					<label>Grad</label>
					<input class="form-control" placeholder="Grad" name="grad" type="text" value="{{ $customer->grad }} " />
					{!! ($errors->has('grad') ? $errors->first('grad', '<p class="text-danger">:message</p>') : '') !!}
				</div>
				<div class="form-group  {{ ($errors->has('oib')) ? 'has-error' : '' }}">
					<label>OIB</label>
					<input class="form-control" placeholder="OIB" name="oib" type="text" value="{{ $customer->oib }}" />
					{!! ($errors->has('oib') ? $errors->first('oib', '<p class="text-danger">:message</p>') : '') !!}
				</div>
			{{ csrf_field() }}
			{{ method_field('PUT') }}
			<input name="_token" value="{{ csrf_token() }}" type="hidden">
			<input class="btn btn-lg btn-primary btn-block" type="submit" value="Ispravi podatke"  id="stil1">
		</fieldset>
		</form>
			
	</div>
</div>

@stop
