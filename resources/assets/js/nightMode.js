window.nightMode = _ => {
    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) return;

    for(let i=0; i<240;i++){
        let star=document.createElement("div")
        star.setAttribute("class", "star");
        document.body.appendChild(star)
    }
    function setupFalcon(){
        let falcon = document.getElementById('falcon');
        if (!falcon){
            const falconContainer = document.createElement('div');
            falconContainer.setAttribute('id', 'falconContainer');
            falconContainer.style.height = document.body.clientHeight + 'px';
            console.log(document.body.clientHeight);
            document.body.appendChild(falconContainer)
            const falconElement = document.createElement('img');
            falconElement.setAttribute('src', "./images/FalconNightTheme.png");
            falconElement.setAttribute('id', 'falcon');
            falconContainer.appendChild(falconElement);
            falcon = document.getElementById('falcon');
        }

        const MAX_VELOCITY = 2;
        const MIN_VELOCITY = 0.4;
        const GRAVITY_MULT = 0.0002;

        let mouseX = window.innerWidth/2;
        let mouseY = window.innerHeight/2;

        falcon.style.top = "0px";
        falcon.style.left = "0px";

        let velocityX = 0;
        let velocityY = 0.1;
        let previousTime = 0;

        function updateFalcon(timestamp) {
            if (previousTime === undefined)
                previousTime = timestamp;
            const elapsed = timestamp - previousTime;
            // console.log(elapsed);

            let posX = parseFloat(falcon.style.left.replace('px', ''));
            let posY = parseFloat(falcon.style.top.replace('px', ''));
            let differenceX = mouseX - posX - falcon.clientWidth/2;
            let differenceY = mouseY - posY - falcon.clientHeight/2;

            let accX = differenceX * GRAVITY_MULT - velocityX * 0.001;
            let accY = differenceY * GRAVITY_MULT - velocityY * 0.001;

            velocityX += accX * elapsed/5;
            velocityY += accY * elapsed/5;

            // limit max velocity:
            const mag = Math.sqrt(Math.pow(velocityX, 2) + Math.pow(velocityY,2));
            if (mag > MAX_VELOCITY){
                velocityX = velocityX * (MAX_VELOCITY / mag);
                velocityY = velocityY * (MAX_VELOCITY / mag);
            }
            else if (mag < MIN_VELOCITY){
                velocityX = velocityX * (MIN_VELOCITY / mag);
                velocityY = velocityY * (MIN_VELOCITY / mag);
            }

            falcon.style.left = `${posX + velocityX * elapsed/5}px`;
            falcon.style.top = `${posY + velocityY * elapsed/5}px`;

            // rotation
            angle = Math.PI/2 + Math.atan2(velocityY, velocityX);
            falcon.style.transform = `rotate(${angle}rad)`;

            previousTime = timestamp;
            window.requestAnimationFrame(updateFalcon);
        }

        document.body.addEventListener('mousemove', function(e){
            mouseX = e.pageX;
            mouseY = e.pageY;
        });

        window.requestAnimationFrame(updateFalcon);
        // setInterval(updateFalcon, msPerFrame);
    }

    setupFalcon();
}
