@extends('layouts.app')
@section('content')
<div  class="container">
	<h1>Bulk Data Display</h1>
    
    @foreach ($bulkData as $key => $data)
        <h2>{{ ucfirst($data['type']) }}</h2>
		<div class="data-block">
			<button  type="button" class="btn btn-success fetch-data-btn" data-key="{{$key}}">Fetch Data</button>
			<textarea class="form-control" rows="5" readonly="true"></textarea>
		</div>
    @endforeach
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/smart-view.js?adww') }}"></script>
@endpush