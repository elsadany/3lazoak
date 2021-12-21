@extends('backend.layout.master')
@section('content')
    
<form action="./backend/branches/create/4" method="post">
    <div class="row col-md-12">
        <label >الموقع</label>
        <?= App\Http\Controllers\Helpers\GoogleMap::editPoint(); ?>
    </div>
    <input type="submit" value="save">
</form>
@endsection