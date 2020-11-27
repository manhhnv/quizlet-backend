@component('mail::message')
    Hello **{{$to}}**,  {{-- use double space for line break --}}
    Your friend **{{$from}}** shared to your quizlet link

    Click below to start working right now
    @component('mail::button', ['url' => $link])
        Quizlet Share
    @endcomponent
    Sincerely,
    Quizlet JP team.
@endcomponent
