@extends('website.layouts.redesign.generic')

@section('page-title')
{{$category->title}} Board
@endsection

@section('container')

    <div class="row">
        <div class="col-lg-3">
            @include('feedbackboards.newfeedback')
            @include('feedbackboards.mostvoted')
            @include('feedbackboards.unreviewed')
            @include('feedbackboards.include.searchfeedback')
        </div>

        <div class="col-lg-9">
            @include('feedbackboards.allfeedback')
        </div>
    </div>

@endsection

@push('javascript')
    <script type="text/javascript" nonce="{{ csp_nonce() }}">
        const upvoteList = Array.from(document.getElementsByClassName('upvote'))
        upvoteList.forEach(el => {
            el.addEventListener('click', e => {
                const id = e.target.parentElement.getAttribute('data-id')
                if (id) sendVote(id, 1)
                console.log(id, 1)
            })
        })

        const downvoteList = Array.from(document.getElementsByClassName('downvote'))
        downvoteList.forEach(el => {
            el.addEventListener('click', e => {
                const id = e.target.parentElement.getAttribute('data-id')
                console.log(id, -1)
                if(id) sendVote(id, -1)
            })
        })

        function sendVote(id, voteValue) {
            post('{{ route('feedback::vote') }}', { id: id, voteValue: voteValue })
            .then(data => {
                console.log(data)
                document.querySelectorAll(`[data-id='${id}']`).forEach(el => {
                    const votes = el.querySelector('.votes')
                    const upvote = el.querySelector('.upvote')
                    const downvote = el.querySelector('.downvote')
                    votes.innerHTML = data.voteScore
                    switch(data.userVote) {
                        case 1:
                            upvote.classList.replace('far', 'fas')
                            downvote.classList.replace('fas', 'far')
                            break
                        case -1:
                            upvote.classList.replace('fas', 'far')
                            downvote.classList.replace('far', 'fas')
                            break
                        case 0:
                            downvote.classList.replace('fas', 'far')
                            upvote.classList.replace('fas', 'far')
                    }
                })
            })
            .catch(err => {
                console.error(err)
                window.alert('Something went wrong voting. Please try again.')
            })
        }
    </script>
@endpush