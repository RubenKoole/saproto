<head>
    <style>
        img[data-src] {
            filter: blur(0.3em);
        }

        img {
            filter: blur(0em);
            transition: filter 0.5s;
        }
    </style>
</head>
@extends('website.layouts.redesign.generic')

@section('page-title')
    Photo
@endsection

@section('container')

    <div class="row justify-content-center">

        <div class="col-auto">

            <div class="card mb-3">

                <div class="card-header bg-dark text-end">

                    <a href="{{route("photo::album::list", ["id"=> $photo->album_id])."?page=".$photo->getAlbumPageNumber(24)}}"
                       class="btn btn-success float-start me-3">
                        <i class="fas fa-images me-2"></i> {{ $photo->album->name }}
                    </a>

                    <a id="download" href="{{$photo->getOriginalUrl()}}" download
                       class="btn btn-success float-start me-3">
                        <i class="fas fa-download me-2"></i> high-res
                    </a>

                    @if ($photo->getPreviousPhoto(Auth::user()) != null)
                        <a href="{{route("photo::view", ["id"=> $photo->getPreviousPhoto(Auth::user())->id])}}"
                           class="btn btn-dark me-3">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    @endif

                    @if (Auth::user())
                        <button id="likeBtn" class="btn btn-info me-3">
                            <i class="{{$photo->likedByUser(Auth::user()->id)?'fas':'far'}} fa-heart"></i><span> {{ $photo->getLikes() }}</span>
                        </button>
                    @endif

                    @if($photo->private)
                        <a href="#" class="btn btn-info me-3" data-bs-toggle="tooltip"
                           data-bs-placement="top" title="This photo is only visible to members.">
                            <i class="fas fa-eye-slash"></i>
                        </a>
                    @endif

                    @if($photo->getNextPhoto(Auth::user()) != null)
                        <a href="{{route("photo::view", ["id"=> $photo->getNextPhoto(Auth::user())->id])}}"
                           class="btn btn-dark">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    @endif

                </div>
                @if($photo->mayViewPhoto(Auth::user()))
                    <img id="progressive-img" class="card-img-bottom" src="{!!$photo->getTinyUrl()!!}"
                         data-src="{!!$photo->getLargeUrl()!!}" style="height: 75vh; object-fit:contain">
                @else
                    <div class="d-flex justify-content-center mb-3 mt-3">
                        This photo is only visible to members!
                    </div>
                @endif
            </div>

            <div class="card mb-3">
                <div class="card-body text-center">
                    <i class="fas fa-shield-alt fa-fw me-3"></i>
                    If there is a photo that you would like removed, please contact
                    <a href="mailto:photos&#64;{{ config('proto.emaildomain') }}">
                        photos&#64;{{ config('proto.emaildomain') }}.
                    </a>
                </div>
            </div>

        </div>

    </div>

@endsection

@push('javascript')
    <script type="text/javascript" nonce="{{ csp_nonce() }}">
        const likeBtn = document.getElementById('likeBtn');
        if (likeBtn) {
            likeBtn.addEventListener('click', _ => {
                switchLike(likeBtn)
            })
        }
        function switchLike(outputElement){
            get('{{ route('photo::like', ['id' => $photo->id]) }}', null, {parse: true})
                .then((data) => {
                    const icon = outputElement.children[0]
                    const likes = outputElement.children[1]
                    data.likedByUser ? icon.classList.replace('far', 'fas') : icon.classList.replace('fas', 'far')
                    likes.innerHTML = data.likes;
                })
                .catch(err => {
                    console.error(err)
                    window.alert('Something went wrong (dis)liking the photo. Please try again.')
                })
        }

        document.addEventListener('keydown', e => {
            if (['ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown'].includes(e.key))
                e.preventDefault();

            switch (e.key) {
                @if ($photo->getPreviousPhoto(Auth::user()) != null)
                case 'ArrowLeft':
                    window.location.href = '{{route("photo::view", ["id"=> $photo->getPreviousPhoto(Auth::user())->id])}}';
                    break;
                @endif
                @if ($photo->getNextPhoto(Auth::user()) != null)
                case 'ArrowRight':
                    window.location.href = '{{route("photo::view", ["id"=> $photo->getNextPhoto(Auth::user())->id])}}';
                    break;
                @endif
                @if (Auth::check())
                case 'ArrowUp':
                    switchLike(likeBtn);
                    break;
                @endif
                @if (Auth::check())
                case 'ArrowDown':
                   document.getElementById('download').click();
                    break;
                    @endif
            }
        })

        let image = document.getElementById('progressive-img');
        image.setAttribute('src', image.getAttribute('data-src'));
        image.onload = () => {
            image.removeAttribute('data-src');
        };

        history.replaceState(null, document.title, location.pathname + "#!/history")
        history.pushState(null, document.title, location.pathname)

        window.addEventListener("popstate", function () {
            if (location.hash === "#!/history") {
                history.replaceState(null, document.title, location.pathname)
                setTimeout(_ => location.replace("{{ route('photo::album::list', ['id' => $photo->album_id])."?page=".$photo->getAlbumPageNumber(24) }}"), 10)
            }
        }, false)
    </script>
@endpush
