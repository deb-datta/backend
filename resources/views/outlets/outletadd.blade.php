@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Add Outlet</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('register') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Name</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('district') ? ' has-error' : '' }}">
                            
                            <label for="district" class="col-md-4 control-label">District</label>
                            <div class="col-md-6">
                                <div class="input-field col s12">
                                <select id="district" name="district" class="form-control">
                                  <option @if (old('district')=="") selected @endif value="" disabled selected>Choose your option</option>
                                  <option @if (old('district')=="1") selected @endif  value="1">Option 1</option>
                                  <option @if (old('district')=="2") selected @endif  value="2">Option 2</option>
                                  <option @if (old('district')=="3") selected @endif value="3">Option 3</option>
                                </select>

                              </div>

                                
                                @if ($errors->has('district'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('district') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        


                        <div class="form-group">
                            <div class="col-md-2 col-md-offset-10">
                                <button type="submit" class="btn btn-primary">
                                    Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
