<!DOCTYPE html>
<html lang="en">
    @include('partials.header')
    @if(Auth::user())
        @include('partials.authbody')
    @else
        @include('partials.noauthbody')
    @endif
</html>