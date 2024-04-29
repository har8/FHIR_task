@extends('layouts.app')
@section('content')
<div  class="container">
	<h1>Cerner Bulk Data Display</h1>
    
    @foreach ($bulkData as $key => $data)
        <h2>{{ ucfirst($data['type']) }}</h2>
		<div class="data-block">
			<button  type="button" class="btn btn-success extract-data-btn hidden" data-key="{{$key}}" data-app="cerner">Extract Data</button>
			<div class="hidden">
				<table class="table">
					<thead>
						<tr>
						<th scope="col">Patient ID</th>
						<th scope="col">Patient Fullname</th>
						<th scope="col">Gender</th>
						<th scope="col">BirthDate</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
			<button  type="button" class="btn btn-success fetch-data-btn" data-key="{{$key}}" data-app="cerner">Fetch Data</button>
			<textarea class="form-control" rows="5" readonly="true"></textarea> 
		</div>
    @endforeach
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/smart-view.js?123') }}"></script>
@endpush