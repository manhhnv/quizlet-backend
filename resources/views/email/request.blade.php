@component('mail::message')
    Hello **{{$to}}**,  {{-- use double space for line break --}}
    **{{$from}}** just sent request to join your class

    Click below to confirm request
    @component('mail::button', ['url' => $link])
        Confirm
    @endcomponent
    Sincerely,
    Quizlet JP team.
@endcomponent
